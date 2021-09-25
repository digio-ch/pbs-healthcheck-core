<?php

namespace App\Command;

use App\Model\CommandStatistics;
use App\Service\PbsApiService;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchDataCommand extends StatisticsCommand
{
    public const STAT_TABLE_HEADERS = ['Table Name', 'Duration (s)', 'Items Fetched'];
    public const STAT_TOTAL_TABLE_HEADERS = ['Total Duration (s)', 'Total Items Fetched'];

    private const PAGINATED = [];
    private const NOT_PAGINATED = ['group_types', 'role_types', 'participation_types', 'j_s_kinds', 'camp_states',
        'qualification_kinds', 'event_kinds'];

    /** @var string */
    private $url;
    /** @var string */
    private $targetDir;
    /** @var PbsApiService */
    private $pbsApiService;
    /** @var SymfonyStyle */
    private $io;
    /** @var array  */
    private $stats = [];

    /**
     * FetchDataCommand constructor.
     * @param string $importDirectory
     * @param PbsApiService $pbsApiService
     */
    public function __construct(
        string $importDirectory,
        PbsApiService $pbsApiService
    ) {
        $this->targetDir = $importDirectory;
        $this->pbsApiService = $pbsApiService;
        parent::__construct();
    }


    public function configure()
    {
        $this->setName('app:fetch-data')
            ->setDescription('Fetches data from PBS MiData (' . $this->url . ') and stores it in ' . $this->targetDir);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->checkTargetDirectory();

        foreach (self::NOT_PAGINATED as $unPaginatedTable) {
            $this->io->section('Fetching data for ' . $unPaginatedTable);
            $this->processNonPaginatedTable($unPaginatedTable);
        }

        foreach (self::PAGINATED as $paginatedTable) {
            $this->io->section('Fetching data for ' . $paginatedTable);
            $this->processPaginatedTable($paginatedTable);
        }

        $this->io->success('Finished fetching all table data successfully');

        $this->io->title('Fetch Command Stats');
        $this->io->table(FetchDataCommand::STAT_TABLE_HEADERS, $this->stats);
        $stats = $this->getStats();
        $this->io->table(FetchDataCommand::STAT_TOTAL_TABLE_HEADERS, [
            [$stats->getDuration(), $stats->getItems()]
        ]);

        return 0;
    }

    public function checkTargetDirectory(): void
    {
        $this->io->section('Checking target directory');
        $exists = file_exists($this->targetDir);

        if (!$exists) {
            $this->io->warning('Target Directory does not exist!');
            $this->io->writeln('Creating target directory...');
            mkdir($this->targetDir);
            $this->io->success('Created target directory: ' . $this->targetDir);
            return;
        }

        $this->io->success('Target directory ' . $this->targetDir . ' exists.');
        $this->checkExistingFiles();
    }

    public function checkExistingFiles(): void
    {
        $this->io->section('Checking for existing files of previous imports');
        $filesInTargetDir = scandir($this->targetDir);

        // remove . and ..
        $filteredFiles = array_filter($filesInTargetDir, function ($value) {
            return ($value !== '.' && $value !== '..');
        });

        if (count($filteredFiles) === 0) {
            $this->io->success('Target directory empty');
            return;
        }

        $this->io->warning('There are existing files in the target directory');
        $this->io->block($filteredFiles);

        $this->io->writeln('Removing files...');

        foreach ($filteredFiles as $file) {
            $filePath = $this->targetDir . '/' . $file;
            if (!is_file($filePath)) {
                continue;
            }
            unlink($filePath);
        }

        $this->io->success('Removed existing files');
    }

    /**
     * @param string $tableName
     * @return void
     * @throws Exception
     */
    private function processNonPaginatedTable(string $tableName): void
    {
        $start = microtime(true);
        $result = $this->pbsApiService->getTableData($tableName);

        if ($result->getStatusCode() !== 200) {
            $this->io->error([
                'API call for table ' . $tableName . ' failed!',
                'HTTP status code: ' . $result->getStatusCode()
            ]);
            throw new Exception(
                'Got http status code ' . $result->getStatusCode() . ' from API. Stopped fetching data.'
            );
        }

        $filePath = $this->targetDir . '/' . $tableName . '.json';
        $file = fopen($filePath, 'w');
        fwrite($file, json_encode($result->getContent()[$tableName], JSON_PRETTY_PRINT));
        fclose($file);

        $timeElapsed = microtime(true) - $start;
        $this->processTableStats($filePath, $tableName, $timeElapsed, count($result->getContent()[$tableName]));
    }

    /**
     * @param string $tableName
     * @throws Exception
     */
    private function processPaginatedTable(string $tableName): void
    {
        $start = microtime(true);

        $page = 1;
        $itemsPerPage = 500;
        $itemCountPerPage = $itemsPerPage;
        $totalItemCount = 0;
        $filePath = $this->targetDir . '/' . $tableName . '.json';

        while ($itemsPerPage === $itemCountPerPage) {
            $result = $this->pbsApiService->getTableData($tableName, $page, $itemsPerPage);

            if ($result->getStatusCode() !== 200) {
                $this->io->error([
                    'API call for table ' . $tableName . ' failed!',
                    'HTTP status code: ' . $result->getStatusCode()
                ]);
                throw new Exception('Got http status code ' . $result->getStatusCode() . ' from API aborting...');
            }

            foreach ($result->getContent()[$tableName] as $item) {
                $this->appendJsonToFile($filePath, $item);
            }

            $itemCountPerPage = count($result->getContent()[$tableName]);
            $totalItemCount += $itemCountPerPage;
            $page += 1;
        }

        if ($totalItemCount === 0) {
            $this->io->error('0 records fetched for ' . $tableName);
            throw new Exception('0 records fetched for ' . $tableName);
        }

        $timeElapsed = microtime(true) - $start;
        $this->processTableStats($filePath, $tableName, $timeElapsed, $totalItemCount, $page - 1);
    }

    /**
     * @param $filePath
     * @param $data
     * @throws Exception
     */
    public function appendJsonToFile($filePath, $data)
    {
        if (file_exists($filePath)) {
            $file = fopen($filePath, 'r+');
        } else {
            $file = fopen($filePath, 'w+');
        }

        if (!$file) {
            throw new Exception('File ' . $filePath . ' could not be opened');
        }

        // move file pointer to end of file
        fseek($file, 0, SEEK_END);

        // end of file and file not empty
        if (ftell($file) > 0) {
            // move file pointer back 1 byte
            fseek($file, -1, SEEK_END);
            fwrite($file, ',', 1);
            fwrite($file, json_encode($data) . ']');
            fclose($file);
            return;
        }

        // file is empty write data as json array
        fwrite($file, json_encode(array($data)));
        fclose($file);
    }

    /**
     * @param string $filePath
     * @param string $tableName
     * @param float $duration
     * @param int $itemCount
     * @param int|null $pages
     */
    private function processTableStats(
        string $filePath,
        string $tableName,
        float $duration,
        int $itemCount,
        int $pages = null
    ): void {
        $this->io->success('Saved data of ' . $tableName . ' to ' . $filePath);

        $countStat = 'Fetched ' . $itemCount . ' items';
        if ($pages) {
            $countStat .= ' on ' . $pages . ' page(s)';
        }

        $formattedDuration = number_format($duration, 2);

        $this->io->block([
            $countStat,
            'Total duration: ' . $formattedDuration . ' seconds'
        ]);
        $this->stats[] = [$tableName, $formattedDuration, $itemCount];
    }

    public function getStats(): CommandStatistics
    {
        $totalItems = 0;
        $totalDuration = 0;
        $details = '';

        foreach ($this->stats as $stat) {
            $dur = intval($stat[1]);
            $totalDuration += $dur;
            $totalItems += $stat[2];
            $details .= $stat[2] . ' items fetched in ' . $stat[1] . ' seconds for ' . $stat[0] . ' table.' . "\n";
        }

        return new CommandStatistics($totalDuration, $details, $totalItems);
    }
}
