<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\DataFixtures\Scenarios;

use Doctrine\DBAL\Schema\Schema;
use FOS\UserBundle\Model\UserManager;
use Inowas\Common\Calculation\Budget;
use Inowas\Common\Calculation\BudgetType;
use Inowas\Common\Calculation\HeadData;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Fixtures\DataFixtureInterface;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Modflow\Model\Command\AddCalculatedBudget;
use Inowas\Modflow\Model\Command\AddCalculatedHead;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Prooph\ServiceBus\CommandBus;
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

    protected function loadResultsWithLayer(string $type, int $t0, int $t1, int $layers, string $scenario, ModflowId $calculationId, CommandBus $commandBus)
    {
        if ($type == 'heads'){
            $calculationResultType = ResultType::HEAD_TYPE;
        } elseif ($type == 'drawdown') {
            $calculationResultType = ResultType::DRAWDOWN_TYPE;
        } else {
            $calculationResultType = ResultType::HEAD_TYPE;
        }

        for ($t=$t0; $t<=$t1; $t++){
            for ($l=0; $l<=$layers; $l++){
                $fileName = sprintf('%s/%s/%s_%s-T%s-L%s.json', __DIR__, $type, $type, $scenario, $t, $l);
                if (file_exists($fileName)){
                    echo sprintf("Load %s for %s from totim=%s and Layer=%s, %s Memory usage\r\n", $type, $scenario, $t, $l, memory_get_usage());
                    $heads = $this->loadHeadsFromFile($fileName, $type=='drawdown');
                    $commandBus->dispatch(AddCalculatedHead::to(
                        $calculationId,
                        TotalTime::fromInt($t),
                        ResultType::fromString($calculationResultType),
                        HeadData::from2dArray($heads),
                        LayerNumber::fromInteger($l)
                    ));
                }
            }
        }
    }

    protected function loadBudgets(string $type, int $t0, int $t1, string $scenario, ModflowId $calculationId, CommandBus $commandBus)
    {
        if ($type == 'cumulative'){
            $budgetType = BudgetType::fromString(BudgetType::CUMULATIVE_BUDGET);
        }

        if ($type == 'incremental'){
            $budgetType = BudgetType::fromString(BudgetType::INCREMENTAL_BUDGET);
        }

        if (!isset($budgetType)){
            return;
        }

        for ($t=$t0; $t<=$t1; $t++){
            $fileName = sprintf('%s/budget/%s_budget_%s-T%s.json', __DIR__, $type, $scenario, $t);
            if (file_exists($fileName)){
                echo sprintf("Load %s Budget for %s from totim=%s, %s Memory usage\r\n", $type, $scenario, $t, memory_get_usage());
                $budget = $this->loadBudgetFromFile($fileName);
                $commandBus->dispatch(AddCalculatedBudget::to(
                    $calculationId,
                    TotalTime::fromInt($t),
                    Budget::fromArray($budget),
                    $budgetType
                ));
            }
        }
    }
}
