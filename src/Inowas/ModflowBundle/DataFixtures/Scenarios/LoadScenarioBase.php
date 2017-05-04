<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\DataFixtures\Scenarios;

use Doctrine\DBAL\Schema\Schema;
use FOS\UserBundle\Model\UserManager;
use Inowas\Common\Fixtures\DataFixtureInterface;
use Inowas\Common\Id\UserId;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class LoadScenarioBase implements ContainerAwareInterface, DataFixtureInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /** @var  UserId */
    protected $ownerId;

    /** @var  array */
    protected $userIdList;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function loadUsers(UserManager $userManager): void
    {

        $userListHeads = array('username', 'name', 'email', 'password');
        $userList = array(
            array('inowas', 'inowas', 'inowas@inowas.com', '#inowas#'),
            array('guest', 'guest', 'guest@inowas.com', '3BJ-w7v-BtP-xes'),
            array('ralf.junghanns', 'Ralf Junghanns', 'ralf.junghanns@tu-dresden.de', '#inowas#'),
            array('jana.glass', 'Jana Glass', 'jana.ringleb@tu-dresden.de', '#inowas#'),
            array('jana.sallwey', 'Jana Sallwey', 'jana.sallwey@tu-dresden.de', '#inowas#'),
            array('catalin.stefan', 'Catalin Stefan', 'catalin.stefan@tu-dresden.de', '#inowas#'),
            array('martin.wudenka', 'Martin Wudenka', 'martin.wudenka@tu-dresden.de', '#inowas#')
        );

        foreach ($userList as $item){
            $item = array_combine($userListHeads, $item);
            $user = $userManager->findUserByUsername($item['username']);
            if (!$user) {

                // Add new User
                $user = $userManager->createUser();
                $user->setUsername($item['username']);
                $user->setName($item['name']);
                $user->setEmail($item['email']);
                $user->setPlainPassword($item['password']);
                $user->setEnabled(true);
                $userManager->updateUser($user);
            }
            $this->userIdList[] = UserId::fromString($user->getId()->toString());
        }

        $owner = $userManager->findUserByUsername('jana.glass');
        $owner->addRole('ROLE_ADMIN');
        $userManager->updateUser($owner);
        $this->ownerId = $owner->getId()->toString();
    }

    protected function createEventStreamTableIfNotExists($tableName): void
    {
        $connection = $this->container->get('doctrine.dbal.default_connection');

        if (in_array($tableName, $connection->getSchemaManager()->listTableNames())){
            return;
        }

        $schema = new Schema();
        if (class_exists('Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema')) {
            EventStoreSchema::createSingleStream($schema, $tableName, true);
        }

        $queries = $schema->toSql($connection->getDatabasePlatform());

        foreach ($queries as $query){
            $connection->exec($query);
        }
    }

    protected function loadHeadsFromFile($filename, $invert = false){

        if (!file_exists($filename) || !is_readable($filename)) {
            echo "File not found.\r\n";
            return FALSE;
        }

        $headsJSON = file_get_contents($filename, true);
        $heads = json_decode($headsJSON, true);


        for ($iy = 0; $iy < count($heads); $iy++){
            for ($ix = 0; $ix < count($heads[0]); $ix++){
                if (abs($heads[$iy][$ix]) > 9999){
                    $heads[$iy][$ix] = null;
                } else {
                    $heads[$iy][$ix] = round($heads[$iy][$ix], 3);

                    if ($invert) {
                        $heads[$iy][$ix] = -$heads[$iy][$ix];
                    }
                }
            }
        }

        return $heads;
    }

    protected function loadBudgetFromFile($filename){

        if (!file_exists($filename) || !is_readable($filename)) {
            echo "File not found.\r\n";
            return FALSE;
        }

        $json = file_get_contents($filename, true);
        $budget = json_decode($json, true);

        return $budget;
    }

    protected function loadRowsFromCsv($filename): array {
        $header = null;
        $rows = array();
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($header == null){
                    $header = $data;
                    continue;
                }

                $rows[] = array_combine($header, $data);

            }
            fclose($handle);
        }

        return $rows;
    }

    protected function loadHeaderFromCsv($filename): array
    {
        $data = array();
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $data = fgetcsv($handle, 1000, ";");
            fclose($handle);
        }

        return $data;
    }

    protected function getDates(array $header): array{
        $dates = array();
        foreach ($header as $data){
            if (explode(':', $data)[0] == 'date'){
                $dates[] = $data;
            }
        }
        return $dates;
    }
}
