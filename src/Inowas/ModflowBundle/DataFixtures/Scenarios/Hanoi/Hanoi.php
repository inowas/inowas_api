<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\Hanoi;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\Point;
use Doctrine\DBAL\Schema\Schema;
use FOS\UserBundle\Doctrine\UserManager;
use Inowas\Common\DataFixtureInterface;
use Inowas\Modflow\Model\AreaBoundary;
use Inowas\Modflow\Model\BoundaryGeometry;
use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\BoundaryName;
use Inowas\Modflow\Model\CalculationResultWithData;
use Inowas\Modflow\Model\CalculationResultData;
use Inowas\Modflow\Model\CalculationResultType;
use Inowas\Modflow\Model\Command\AddBoundary;
use Inowas\Modflow\Model\Command\AddResultToCalculation;
use Inowas\Modflow\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\Modflow\Model\Command\ChangeModflowModelDescription;
use Inowas\Modflow\Model\Command\ChangeModflowModelGridSize;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\Command\AddModflowScenario;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelGridSize;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\PumpingRate;
use Inowas\Modflow\Model\PumpingRates;
use Inowas\Modflow\Model\SoilModelId;
use Inowas\Modflow\Model\TotalTime;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Model\WellBoundary;
use Inowas\Modflow\Model\WellType;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

ini_set('memory_limit', '2048M');

class Hanoi implements ContainerAwareInterface, DataFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /** @var  UserId */
    private $ownerId;

    /** @var  array */
    protected $userIdList;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load()
    {
        $this->createEventStreamTableIfNotExists('event_stream');
        $geoTools = $this->container->get('inowas.geotools');

        /** @var UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');
        $this->loadUsers($userManager);

        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');
        $ownerId = $this->ownerId;
        $modelId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modelId));
        $commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modelId, ModflowModelName::fromString('BaseModel INOWAS Hanoi')));
        $commandBus->dispatch(ChangeModflowModelDescription::forModflowModel(
            $ownerId,
            $modelId,
            ModflowModelDescription::fromString(
                'Application of managed aquifer recharge for maximization of water storage capacity in Hanoi.')
        )
        );

        $area = AreaBoundary::create(BoundaryId::generate())
            ->setName(BoundaryName::fromString('Hanoi Area'))
            ->setGeometry(BoundaryGeometry::fromPolygon(new Polygon(array(
                array(
                    array(105.790767733626808, 21.094425932026443),
                    array(105.796959843400032, 21.093521487879368),
                    array(105.802017060333782, 21.092234483652170),
                    array(105.808084259744490, 21.090442258424751),
                    array(105.812499379361824, 21.088745285770433),
                    array(105.817189857772419, 21.086246452411380),
                    array(105.821849880920155, 21.083084791161816),
                    array(105.826206685192972, 21.080549811906632),
                    array(105.829745666549428, 21.077143263497668),
                    array(105.833738284468225, 21.073871989488410),
                    array(105.837054371969458, 21.068790508713093),
                    array(105.843156477826938, 21.061619066459148),
                    array(105.845257297050807, 21.058494488216656),
                    array(105.848091064693264, 21.055416254106909),
                    array(105.850415052797018, 21.051740212147806),
                    array(105.853986426189834, 21.047219935885728),
                    array(105.857317797743207, 21.042700799256870),
                    array(105.860886165285677, 21.037730164508108),
                    array(105.862781077291359, 21.033668431680731),
                    array(105.865628458812012, 21.028476242159179),
                    array(105.867512713611035, 21.022613568026749),
                    array(105.869402048566840, 21.017651320651229),
                    array(105.871388782041976, 21.013426442220442),
                    array(105.872849945737570, 21.008166192541132),
                    array(105.876181664767913, 21.003946864458868),
                    array(105.882508712001197, 21.001813076331899),
                    array(105.889491767034770, 21.000288452359857),
                    array(105.894324807327010, 20.997811850332017),
                    array(105.898130162725238, 20.994990356212355),
                    array(105.903035574892471, 20.989098851962478),
                    array(105.905619253163707, 20.984707849769400),
                    array(105.905107309855680, 20.977094091795209),
                    array(105.901707144804220, 20.969670720258843),
                    array(105.896052272867848, 20.959195015805960),
                    array(105.886865167028475, 20.950138230157627),
                    array(105.877901274443431, 20.947208019282808),
                    array(105.834499067698161, 20.951978316227517),
                    array(105.806257646336405, 20.968923300374374),
                    array(105.781856978173835, 21.008608549010258),
                    array(105.768216532593982, 21.039487418417067),
                    array(105.774357585691064, 21.072902571997240),
                    array(105.777062025914603, 21.090749775344797),
                    array(105.783049106327312, 21.093961473086512),
                    array(105.790767733626808, 21.094425932026443)
                )
            ), 4326)));
        $commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $area));

        $soilModelId = SoilModelId::generate();
        $commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($modelId, $soilModelId));

        $box = $geoTools->transformBoundingBox(new BoundingBox(578205, 594692, 2316000, 2333500, 32648), 4326);
        $boundingBox = ModflowModelBoundingBox::fromEPSG4326Coordinates($box->getXMin(), $box->getXMax(), $box->getYMin(), $box->getYMax());
        $commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));
        $commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modelId, ModflowModelGridSize::fromXY(165, 175)));

        // Add Wells
        $fileName = __DIR__ . "/data/wells_basecase.csv";
        $wells = $this->loadRowsFromCsv($fileName);
        $header = $this->loadHeaderFromCsv($fileName);
        $dates = $this->getDates($header);

        foreach ($wells as $well){
            $value = null;
            $pumpingRates = PumpingRates::create();
            foreach ($dates as $date){
                if (is_numeric($well[$date])){
                    if ($well[$date] !== $value){
                        $pumpingRates->add(PumpingRate::fromDateTimeAndCubicMetersPerDay(
                            new \DateTimeImmutable(explode(':', $date)[1]), (float)$well[$date]
                        ));
                    }
                    $value = $well[$date];
                }
            }

            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($well['Name']),
                BoundaryGeometry::fromPoint($geoTools->transformPoint(new Point($well['x'], $well['y'], $well['srid']), 4326)),
                WellType::fromString($well['type']),
                LayerNumber::fromInteger((int)$well['layer']),
                $pumpingRates
            );

            echo sprintf('Add Well %s to BaseModel'."\r\n", $well->name()->toString());
            $commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $well));
        }

        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modelId));
        $this->loadResults(0, 2000, 4, 'S0', $calculationId, $commandBus);

        /*
         * Begin add Scenario 1
         */
        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(AddModflowScenario::from($ownerId, $modelId, $scenarioId));
        $commandBus->dispatch(ChangeModflowModelName::forScenario($ownerId, $modelId, $scenarioId, ModflowModelName::fromString('Scenario 1')));
        $commandBus->dispatch(ChangeModflowModelDescription::forScenario($ownerId, $modelId, $scenarioId, ModflowModelDescription::fromString('River Bank Filtration')));

        $movedWells_sc1 = array(
            array('A01', 21.03580, 105.78032, 4326, -4900),
            array('A02', 21.03420, 105.78135, 4326, -4900),
            array('A03', 21.03131, 105.77963, 4326, -4900),
            array('A04', 20.98580, 105.80641, 4326, -4900),
            array('A05', 20.98548, 105.81430, 4326, -4900),
            array('A06', 20.98388, 105.81224, 4326, -4900),
            array('A07', 20.98484, 105.81465, 4326, -4900),
            array('A08', 20.96561, 105.85001, 4326, -4900),
            array('A09', 20.96433, 105.84761, 4326, -4900),
            array('A10', 20.96176, 105.85070, 4326, -4900)
        );
        $header = array('name', 'y', 'x', 'srid', 'pumpingrate');
        foreach ($movedWells_sc1 as $row) {
            $wellData = array_combine($header, $row);
            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint(new Point($wellData['x'], $wellData['y'], 4326)),
                WellType::fromString(WellType::TYPE_SCENARIO_MOVED_WELL),
                LayerNumber::fromInteger(4),
                PumpingRates::create()->add(PumpingRate::fromCubicMetersPerDay($wellData['pumpingrate']))
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        # THIS WELLS ARE THE RED AND YELLOW DOTS IN THE LEFT IMAGE
        $newWells_sc1 = array(
            array('A11', 21.08354, 105.81499, 4326, -4900),
            array('A12', 21.08226, 105.81671, 4326, -4900),
            array('A13', 21.04125, 105.85173, 4326, -4900),
            array('A15', 21.03868, 105.85310, 4326, -4900),
            array('A16', 21.00181, 105.87710, 4326, -4900),
            array('A17', 21.03708, 105.85379, 4326, -4900),
            array('A18', 21.03548, 105.85550, 4326, -4900),
            array('A19', 21.03484, 105.85585, 4326, -4900),
            array('A20', 20.98965, 105.89842, 4326, -4900),
            array('A21', 20.98837, 105.90014, 4326, -4900),
            array('A22', 20.98644, 105.89842, 4326, -4900)
        );

        $header = array('name', 'y', 'x', 'srid', 'pumpingrate');
        foreach ($newWells_sc1 as $row) {
            $wellData = array_combine($header, $row);

            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint(new Point($wellData['x'], $wellData['y'], 4326)),
                WellType::fromString(WellType::TYPE_SCENARIO_NEW_WELL),
                LayerNumber::fromInteger(4),
                PumpingRates::create()->add(PumpingRate::fromCubicMetersPerDay($wellData['pumpingrate']))
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        /* Add Head Results */
        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modelId, $scenarioId));
        $this->loadResults(0, 2000, 4, 'S1', $calculationId, $commandBus);

        /*
         * Begin add Scenario 2
         */
        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(AddModflowScenario::from($ownerId, $modelId, $scenarioId));
        $commandBus->dispatch(ChangeModflowModelName::forScenario($ownerId, $modelId, $scenarioId, ModflowModelName::fromString('Scenario 2')));
        $commandBus->dispatch(ChangeModflowModelDescription::forScenario($ownerId, $modelId, $scenarioId, ModflowModelDescription::fromString('Injection wells')));

        # THIS WELLS ARE THE YELLOW DOTS IN THE RIGHT IMAGE
        $newWells_sc2 = array(
            array('B01', 21.002, 105.8415, 4326, -4900),
            array('B02', 21.002, 105.8425, 4326, -4900),
            array('B03', 21.002, 105.8435, 4326, -4900),
            array('B04', 21.002, 105.8445, 4326, -4900),
            array('B05', 21.002, 105.8455, 4326, -4900),
            array('B06', 21.00271, 105.84653, 4326, -4900),
            array('B07', 20.98292, 105.82872, 4326, -4900),
            array('B08', 20.9826, 105.82975, 4326, -4900),
            array('B09', 20.9826, 105.83113, 4326, -4900),
            array('B10', 20.98164, 105.83216, 4326, -4900)
        );

        $header = array('name', 'y', 'x', 'srid', 'pumpingrate');
        foreach ($newWells_sc2 as $row) {
            $wellData = array_combine($header, $row);

            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint(new Point($wellData['x'], $wellData['y'], 4326)),
                WellType::fromString(WellType::TYPE_SCENARIO_NEW_WELL),
                LayerNumber::fromInteger(4),
                PumpingRates::create()->add(PumpingRate::fromCubicMetersPerDay($wellData['pumpingrate']))
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        /* Add Head Results */
        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modelId, $scenarioId));
        $this->loadResults(0, 2000, 4, 'S2', $calculationId, $commandBus);

        /*
        * Begin add Scenario 3
        */
        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(AddModflowScenario::from($ownerId, $modelId, $scenarioId));
        $commandBus->dispatch(ChangeModflowModelName::forScenario($ownerId, $modelId, $scenarioId, ModflowModelName::fromString('Scenario 3')));
        $commandBus->dispatch(ChangeModflowModelDescription::forScenario($ownerId, $modelId, $scenarioId, ModflowModelDescription::fromString('River bank filtration and injection wells.')));

        $movedWells_sc3 = $movedWells_sc1;
        $header = array('name', 'y', 'x', 'srid', 'pumpingrate');
        foreach ($movedWells_sc3 as $row) {
            $wellData = array_combine($header, $row);

            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint(new Point($wellData['x'], $wellData['y'], 4326)),
                WellType::fromString(WellType::TYPE_SCENARIO_MOVED_WELL),
                LayerNumber::fromInteger(4),
                PumpingRates::create()->add(PumpingRate::fromCubicMetersPerDay($wellData['pumpingrate']))
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        # THIS WELLS ARE ALL YELLOW DOTS OG BOTH IMAGES
        $newWells_sc3 = array_merge($newWells_sc1, $newWells_sc2);
        $header = array('name', 'y', 'x', 'srid', 'pumpingrate');

        foreach ($newWells_sc3 as $row) {
            $wellData = array_combine($header, $row);

            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint(new Point($wellData['x'], $wellData['y'], 4326)),
                WellType::fromString(WellType::TYPE_SCENARIO_NEW_WELL),
                LayerNumber::fromInteger(4),
                PumpingRates::create()->add(PumpingRate::fromCubicMetersPerDay($wellData['pumpingrate']))
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        /* Add Head Results */
        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modelId, $scenarioId));
        $this->loadResults(0, 2000, 4, 'S3', $calculationId, $commandBus);
    }

    public function loadUsers(UserManager $userManager): void
    {

        $userListHeads = array('username', 'email', 'password');
        $userList = array(
            array('inowas', 'inowas@inowas.com', 'inowas'),
            array('ralf.junghanns', 'ralf.junghanns@tu-dresden.de', 'inowas'),
            array('jana.ringleb', 'jana.ringleb@tu-dresden.de', 'inowas'),
            array('jana.sallwey', 'jana.sallwey@tu-dresden.de', 'inowas'),
            array('catalin.stefan', 'catalin.stefan@tu-dresden.de', 'inowas')
        );

        foreach ($userList as $item){
            $item = array_combine($userListHeads, $item);
            $user = $userManager->findUserByUsername($item['username']);
            if (!$user) {
                // Add new User
                $user = $userManager->createUser();
                $user->setUsername($item['username']);
                $user->setEmail($item['email']);
                $user->setPlainPassword($item['password']);
                $user->setEnabled(true);
                $userManager->updateUser($user);
            }
            $this->userIdList[] = UserId::fromString($user->getId()->toString());
        }

        $owner = $userManager->findUserByUsername('inowas');
        $owner->addRole('ROLE_ADMIN');
        $userManager->updateUser($owner);
        $this->ownerId = UserId::fromString($userManager->findUserByUsername('inowas')->getId()->toString());
    }

    private function loadHeadsFromFile($filename){

        if (!file_exists($filename) || !is_readable($filename)) {
            echo "File not found.\r\n";
            return FALSE;
        }

        $headsJSON = file_get_contents($filename, true);
        $heads = json_decode($headsJSON, true);


        for ($iy = 0; $iy < count($heads); $iy++){
            for ($ix = 0; $ix < count($heads[0]); $ix++){
                if ($heads[$iy][$ix] <= -9999){
                    $heads[$iy][$ix] = null;
                } else {
                    $heads[$iy][$ix] = round($heads[$iy][$ix], 2);
                }
            }
        }

        return $heads;
    }

    private function createEventStreamTableIfNotExists($tableName): void
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

    private function loadResults(int $t0, int $t1, int $layers, string $scenario, ModflowId $calculationId, CommandBus $commandBus)
    {
        for ($t=$t0; $t<=$t1; $t++){
            for ($l=0; $l<=$layers; $l++){
                $fileName = sprintf('%s/heads/heads_%s-T%s-L%s.json', __DIR__, $scenario, $t, $l);
                if (file_exists($fileName)){
                    echo sprintf("Load head for %s from totim=%s and Layer=%s, %s Memory usage\r\n", $scenario, $t, $l, memory_get_usage());
                    $heads = $this->loadHeadsFromFile($fileName);
                    $commandBus->dispatch(AddResultToCalculation::to($calculationId,
                        CalculationResultWithData::fromParameters(
                            CalculationResultType::fromString(CalculationResultType::HEAD_TYPE),
                            TotalTime::fromInt($t),
                            LayerNumber::fromInteger($l),
                            CalculationResultData::from2dArray($heads)
                        )
                    ));

                }
            }
        }
    }
}
