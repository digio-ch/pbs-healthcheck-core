<?php

namespace App\Command;

use App\Model\CommandStatistics;
use App\Repository\Midata\PersonRoleRepository;
use App\Repository\Security\PermissionRepository;
use App\Service\Security\PermissionVoter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Sentry\continueTrace;

class ComputePermissionsCommand extends StatisticsCommand
{
    /** @var PersonRoleRepository $personRoleRepository */
    private PersonRoleRepository $personRoleRepository;

    /** @var PermissionRepository $permissionRepository */
    private PermissionRepository $permissionRepository;

    private float $totalDuration = 0;

    public function __construct(
        PersonRoleRepository $personRoleRepository,
        PermissionRepository $permissionRepository
    ) {
        parent::__construct();

        $this->personRoleRepository = $personRoleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    protected function configure()
    {
        $this
            ->setName("app:compute-permissions");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $output->writeln('Computing peoples default permissions...');

        $this->permissionRepository->endAllOpenPermissions();

        $viewers = $this->getViewers();
        $this->assignPermissionToRoles($viewers, PermissionVoter::ORDER_VIEWER);
        $output->writeln('finished computing all viewer permissions.');

        $editors = $this->getEditors();
        $this->assignPermissionToRoles($editors, PermissionVoter::ORDER_EDITOR);
        $output->writeln('finished computing all editor permissions.');

        $editorsPlus = $this->getEditorsPlus();
        $this->assignPermissionToRoles($editorsPlus, PermissionVoter::ORDER_EDITOR_PLUS);
        $output->writeln('finished computing all editor plus permissions.');

        $owners = $this->getOwners();
        $this->assignPermissionToRoles($owners, PermissionVoter::ORDER_OWNER);
        $output->writeln('finished computing all owner permissions.');

        $output->writeln('finished computing all default permissions.');
        $this->totalDuration = microtime(true) - $start;
        return 0;
    }

    // TODO: use PermissionType key instead of id because it is not guaranteed
    private function assignPermissionToRoles(array $roles, int $permissionType)
    {
        foreach ($roles as $key => $role) {
            $personId = $role['person_id'];
            $groupId = $role['group_id'];

            $permission = $this->permissionRepository->findByPersonGroupAndPermission(
                $groupId,
                $personId,
                $permissionType
            );
            if (!is_null($permission)) {
                $permission->setExpirationDate(null);
                $this->permissionRepository->persist($permission);

                if ($key > 0 && $key % 500 == 0) {
                    $this->permissionRepository->flush();
                }
                continue;
            }

            $this->permissionRepository->insertPermission(
                $groupId,
                $permissionType,
                null,
                $personId,
                null
            );
        }

        $this->permissionRepository->flush();
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->totalDuration, '');
    }

    private function getViewers(): array
    {
        return $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Bund',
            'Group::Kantonalverband',
            'Group::Region',
            'Group::Abteilung',

            'Group::Biber',
            'Group::Woelfe',
            'Group::Pfadi',
            'Group::Pio',
            'Group::AbteilungsRover',
            'Group::Pta',
        ], [
            'Group::Bund::Coach',

            'Group::Kantonalverband::Sekretariat',
            'Group::Kantonalverband::Adressverwaltung',
            'Group::Kantonalverband::PraesidiumApv',
            'Group::Kantonalverband::Mitarbeiter',
            'Group::Kantonalverband::Beisitz',
            'Group::Kantonalverband::Kassier',
            'Group::Kantonalverband::Rechnungen',
            'Group::Kantonalverband::Redaktor',
            'Group::Kantonalverband::Webmaster',
            'Group::Kantonalverband::Mediensprecher',
            'Group::Kantonalverband::MitgliedKrisenteam',
            'Group::Kantonalverband::Coach',
            'Group::Kantonalverband::VerantwortungKantonsarchiv',
            'Group::Kantonalverband::VerantwortungLagermeldung',
            'Group::Kantonalverband::VerantwortungLagerplaetze',
            'Group::Kantonalverband::VerantwortungMaterialverkaufsstelle',

            'Group::Region::Sekretariat',
            'Group::Region::Adressverwaltung',
            'Group::Region::PraesidiumApv',
            'Group::Region::Praeses',
            'Group::Region::Mitarbeiter',
            'Group::Region::Beisitz',
            'Group::Region::Kassier',
            'Group::Region::Rechnungen',
            'Group::Region::Redaktor',
            'Group::Region::Webmaster',
            'Group::Region::Mediensprecher',
            'Group::Region::MitgliedKrisenteam',
            'Group::Region::Coach',
            'Group::Region::VerantwortungLagerplaetze',
            'Group::Region::VerantwortungLagermeldung',
            'Group::Region::VerantwortungMaterialverkaufsstelle',
            'Group::Region::VerantwortungPr',

            'Group::Abteilung::Praesidium',
            'Group::Abteilung::VizePraesidium',
            'Group::Abteilung::Praeses',
            'Group::Abteilung::Beisitz',
            'Group::Abteilung::StufenleitungBiber',
            'Group::Abteilung::StufenleitungWoelfe',
            'Group::Abteilung::StufenleitungPfadi',
            'Group::Abteilung::StufenleitungPio',
            'Group::Abteilung::StufenleitungRover',
            'Group::Abteilung::StufenleitungPta',
            'Group::Abteilung::Coach',

            'Group::Biber::Einheitsleitung',
            'Group::Biber::Mitleitung',

            'Group::Woelfe::Einheitsleitung',
            'Group::Woelfe::Mitleitung',

            'Group::Pfadi::Einheitsleitung',
            'Group::Pfadi::Mitleitung',

            'Group::Pio::Einheitsleitung',
            'Group::Pio::Mitleitung',

            'Group::AbteilungsRover::Einheitsleitung',
            'Group::AbteilungsRover::Mitleitung',

            'Group::Pta::Einheitsleitung',
            'Group::Pta::Mitleitung',
        ]);
    }

    private function getEditors(): array
    {
        return $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Kantonalverband',
            'Group::Region',
        ], [
            'Group::Kantonalverband::VerantwortungBiberstufe',
            'Group::Kantonalverband::VerantwortungWolfstufe',
            'Group::Kantonalverband::VerantwortungPfadistufe',
            'Group::Kantonalverband::VerantwortungPiostufe',
            'Group::Kantonalverband::VerantwortungRoverstufe',
            'Group::Kantonalverband::VerantwortungPfadiTrotzAllem',
            'Group::Kantonalverband::VerantwortungAbteilungen',
            'Group::Kantonalverband::VerantwortungAnimationSpirituelle',
            'Group::Kantonalverband::VerantwortungIntegration',
            'Group::Kantonalverband::VerantwortungInternationales',
            'Group::Kantonalverband::VerantwortungSuchtpraeventionsprogramm',
            'Group::Kantonalverband::VerantwortungKrisenteam',
            'Group::Kantonalverband::VerantwortungPr',
            'Group::Kantonalverband::VerantwortungPraeventionSexuellerAusbeutung',
            'Group::Kantonalverband::VerantwortungNachhaltigkeit',

            'Group::Region::VerantwortungBiberstufe',
            'Group::Region::VerantwortungWolfstufe',
            'Group::Region::VerantwortungPfadistufe',
            'Group::Region::VerantwortungPiostufe',
            'Group::Region::VerantwortungRoverstufe',
            'Group::Region::VerantwortungPfadiTrotzAllem',
            'Group::Region::VerantwortungAbteilungen',
            'Group::Region::VerantwortungAnimationSpirituelle',
            'Group::Region::VerantwortungIntegration',
            'Group::Region::VerantwortungInternationales',
            'Group::Region::VerantwortungSuchtpraeventionsprogramm',
            'Group::Region::VerantwortungKrisenteam',
            'Group::Region::VerantwortungPraeventionSexuellerAusbeutung',
        ]);
    }

    private function getEditorsPlus(): array
    {
        return $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Kantonalverband',
            'Group::Region',
        ], [
            'Group::Kantonalverband::Praesidium',
            'Group::Kantonalverband::VizePraesidium',
            'Group::Kantonalverband::VerantwortungAusbildung',
            'Group::Kantonalverband::VerantwortungBetreuung',
            'Group::Kantonalverband::VerantwortungProgramm',

            'Group::Region::Praesidium',
            'Group::Region::VizePraesidium',
            'Group::Region::VerantwortungAusbildung',
            'Group::Region::VerantwortungBetreuung',
            'Group::Region::VerantwortungProgramm',
        ]);
    }

    private function getOwners(): array
    {
        return $this->personRoleRepository->findAllPersonInGroupByRole([
            'Group::Bund',
            'Group::Kantonalverband',
            'Group::Region',
            'Group::Abteilung',
        ], [
            'Group::Kantonalverband::Kantonsleitung',
            'Group::Kantonalverband::PowerUser',

            'Group::Region::Regionalleitung',
            'Group::Region::PowerUser',

            'Group::Abteilung::Abteilungsleitung',
            'Group::Abteilung::AbteilungsleitungStv',
            'Group::Abteilung::PowerUser',
        ]);
    }
}
