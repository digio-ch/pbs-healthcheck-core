<?php


namespace App\Tests\Command;


use App\Command\FetchDataCommand;
use App\Tests\Mock\PbsApiServiceMock;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FetchDataCommandTest extends KernelTestCase
{
    /** @var FetchDataCommand */
    protected $command;
    /** @var string */
    protected $targetDir;
    /** @var CommandTester */
    protected $commandTester;
    /** @var PbsApiServiceMock */
    protected $pbsApiServiceMock;

    protected function setUp() {
        self::bootKernel();
        $this->targetDir = self::$kernel->getContainer()->getParameter('import_data_dir');
        $this->pbsApiServiceMock = new PbsApiServiceMock();
        $this->command = new FetchDataCommand($this->targetDir, '', $this->pbsApiServiceMock);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecute() {
        $this->pbsApiServiceMock->generateTestData(100);
        $this->deleteDirectory($this->targetDir);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getStatusCode();

        self::assertTrue($output === 0, 'Fetch Command did not finish successfully');
        $this->checkFiles();
    }

    public function testExecute1000() {
        $this->pbsApiServiceMock->generateTestData(1000);
        $this->deleteDirectory($this->targetDir);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getStatusCode();

        self::assertTrue($output === 0, 'Fetch Command did not finish successfully');
        $this->checkFiles();
    }

    public function testExecute10000() {
        $this->pbsApiServiceMock->generateTestData(10000);
        $this->deleteDirectory($this->targetDir);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getStatusCode();

        self::assertTrue($output === 0, 'Fetch Command did not finish successfully');
        $this->checkFiles();
    }

    /**
     * @expectedException Exception
     */
    public function testStopOnFirstApiCallFail() {
        $this->pbsApiServiceMock->generateTestData();
        $this->pbsApiServiceMock->enableApiFailure(PbsApiServiceMock::FAIL_FIRST);
        $this->commandTester->execute([]);
        self::assertTrue($this->commandTester->getStatusCode() === 1);
    }

    /**
     * @expectedException Exception
     */
    public function testStopOnLastApiCall() {
        $this->pbsApiServiceMock->generateTestData();
        $this->pbsApiServiceMock->enableApiFailure(PbsApiServiceMock::FAIL_LAST);
        $this->commandTester->execute([]);
        self::assertTrue($this->commandTester->getStatusCode() === 1);
    }

    /**
     * @expectedException Exception
     */
    public function testStopOnRandomApiCall() {
        $this->pbsApiServiceMock->generateTestData();
        $this->pbsApiServiceMock->enableApiFailure(PbsApiServiceMock::FAIL_RANDOM);
        $this->commandTester->execute([]);
        self::assertTrue($this->commandTester->getStatusCode() === 1);
    }

    private function checkFiles() {
        foreach (PbsApiServiceMock::TABLES as $tableName) {
            $filePath = $this->targetDir . '/' . $tableName . '.json';

            self::assertTrue(file_exists($filePath), 'File for table '.$tableName.' could not be opened.');

            $contents = file_get_contents($filePath);
            $data = json_decode($contents, true);
            $expectedData = $this->pbsApiServiceMock->responseData[$tableName];

            self::assertEquals($expectedData, $data, 'Content does not match');
        }
    }

    private function deleteDirectory($dirname) {
        $dir = false;
        if (is_dir($dirname)) {
            $dir = opendir($dirname);
        }
        if (!$dir) {
            return false;
        }

        while($file = readdir($dir)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file)) {
                    unlink($dirname."/".$file);
                } else {
                    $this->deleteDirectory($dirname.'/'.$file);
                }
            }
        }
        closedir($dir);
        rmdir($dirname);
        return true;
    }
}