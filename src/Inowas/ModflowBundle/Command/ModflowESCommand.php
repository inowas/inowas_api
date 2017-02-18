<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\Point;
use FOS\UserBundle\Doctrine\UserManager;
use Inowas\Modflow\Model\AreaBoundary;
use Inowas\Modflow\Model\BoundaryGeometry;
use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\BoundaryName;
use Inowas\Modflow\Model\CalculationResult;
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
use Inowas\Modflow\Model\SoilModelId;
use Inowas\Modflow\Model\TotalTime;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Model\WellBoundary;
use Inowas\Modflow\Model\WellType;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowESCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:es:migrate')
            ->setDescription('Migrates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $geoTools = $this->getContainer()->get('inowas.geotools');

        /** @var UserManager $userManager */
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $this->loadUsers($userManager);

        $commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
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

        $wells = array(
            array(1, 'CD10', 11777809.79499545693397522, 2401995.67269986681640148, -30, -60, 4320, -4900, 11777809.79, 2401995.67),
            array(2, 'CD11', 11778088.30947495624423027, 2401994.25986256683245301, -30, -60, 4320, -4900, 11778088.31, 2401994.26),
            array(3, 'CD12', 11778345.50155015662312508, 2401992.95128001738339663, -30, -60, 4320, -4100, 11778345.5, 2401992.95),
            array(4, 'CD13', 11778881.09251317754387856, 2401990.21416973834857345, -30, -60, 4320, -4000, 11778881.09, 2401990.21),
            array(5, 'CD17', 11780226.6936198603361845, 2401185.61681587109342217, -30, -60, 4320, -4900, 11780226.69, 2401185.62),
            array(6, 'CD18', 11780226.6936198603361845, 2401185.61681587109342217, -30, -60, 4320, -4900, 11780226.69, 2401185.62),
            array(7, 'CD19', 11780482.3135919813066721, 2400904.04203267069533467, -30, -60, 4320, -4900, 11780482.31, 2400904.04),
            array(8, 'CD20', 11780482.3135919813066721, 2400904.04203267069533467, -30, -60, 4320, -4900, 11780482.31, 2400904.04),
            array(9, 'CD7', 11777017.11587653681635857, 2401999.66968260146677494, -30, -60, 4320, -4900, 11777017.12, 2401999.67),
            array(10, 'CD8', 11777274.20259966887533665, 2401998.3772620651870966, -30, -60, 4320, -4900, 11777274.2, 2401998.38),
            array(11, 'CD9', 11777552.71745001152157784, 2401996.97288660053163767, -30, -60, 4320, -4900, 11777552.72, 2401996.97),
            array(12, 'HDI10', 11778803.9348050132393837, 2389919.65258358465507627, -40, -70, 4320, -2800, 11778803.93, 2389919.65),
            array(13, 'HDI13', 11778287.28281443752348423, 2390424.27275150641798973, -40, -70, 4320, -2800, 11778287.28, 2390424.27),
            array(14, 'HDI5', 11778565.60789936594665051, 2390422.85955950664356351, -40, -70, 4320, -2800, 11778565.61, 2390422.86),
            array(15, 'HDI6', 11778628.7308922503143549, 2390196.31113039888441563, -40, -70, 4320, -2800, 11778628.73, 2390196.31),
            array(16, 'HDI7', 11778821.00705778226256371, 2390119.93042483413591981, -40, -70, 4320, -2800, 11778821.01, 2390119.93),
            array(17, 'HDI8', 11778822.20144898630678654, 2390356.91003846284002066, -40, -70, 4320, -2800, 11778822.2, 2390356.91),
            array(18, 'KGIANG1', 11779098.08642016164958477, 2389956.93314105411991477, -32.27, -68.83, 4320, -3000, 11779098.09, 2389956.93),
            array(19, 'KTN42', 11777146.73212774097919464, 2402246.94875892251729965, -39.88, -66.35, 4320, -80, 11777146.73, 2402246.95),
            array(20, 'KTN80', 11787797.60370349697768688, 2391202.97728021768853068, -32.19, -88.36, 4320, -600, 11787797.6, 2391202.98),
            array(21, 'LN1', 11787613.29082772321999073, 2386865.27748037222772837, -40, -70, 4320, -2135, 11787613.29, 2386865.28),
            array(22, 'LN10', 11788726.25647358968853951, 2388753.58681372459977865, -40, -70, 4320, -2135, 11788726.26, 2388753.59),
            array(23, 'LN11', 11788984.59457647800445557, 2389010.63655604887753725, -40, -70, 4320, -2135, 11788984.59, 2389010.64),
            array(24, 'LN12', 11788984.59457647800445557, 2389010.63655604887753725, -40, -70, 4320, -2135, 11788984.59, 2389010.64),
            array(25, 'LN13', 11788986.16430903784930706, 2389290.6827991041354835, -40, -70, 4320, -2135, 11788986.16, 2389290.68),
            array(26, 'LN14', 11789267.49526369944214821, 2389826.59154414804652333, -40, -70, 4320, -2135, 11789267.5, 2389826.59),
            array(27, 'LN15', 11788989.17762009054422379, 2389828.17262880643829703, -40, -70, 4320, -2135, 11788989.18, 2389828.17),
            array(28, 'LN16', 11788990.6272040531039238, 2390086.69249562220647931, -40, -70, 4320, -2135, 11788990.63, 2390086.69),
            array(29, 'LN2', 11787347.26407890021800995, 2387145.71913112327456474, -40, -70, 4320, -2135, 11787347.26, 2387145.72),
            array(30, 'LN20', 11788459.96383132226765156, 2390908.39469485450536013, -40, -70, 4320, -2135, 11788459.96, 2390908.39),
            array(31, 'LN21', 11788203.05212100967764854, 2390909.84320783708244562, -40, -70, 4320, -2135, 11788203.05, 2390909.84),
            array(32, 'LN22', 11788204.4910411573946476, 2391168.37900674156844616, -40, -70, 4320, -2135, 11788204.49, 2391168.38),
            array(33, 'LN23', 11787638.65069332718849182, 2391451.64576283935457468, -40, -70, 4320, -2135, 11787638.65, 2391451.65),
            array(34, 'LN24', 11787638.65069332718849182, 2391451.64576283935457468, -40, -70, 4320, -2135, 11787638.65, 2391451.65),
            array(35, 'LN25', 11787372.4459209144115448, 2391711.6866044644266367, -40, -70, 4320, -2135, 11787372.45, 2391711.69),
            array(36, 'LN3', 11787350.23137721605598927, 2387684.22437982214614749, -40, -70, 4320, -2135, 11787350.23, 2387684.22),
            array(37, 'LN6', 11787906.80211838521063328, 2387681.1122775818221271, -40, -70, 4320, -2135, 11787906.8, 2387681.11),
            array(38, 'LN7', 11787908.23473594710230827, 2387939.60222975071519613, -40, -70, 4320, -2135, 11787908.23, 2387939.6),
            array(39, 'LN8', 11788188.08032611012458801, 2388218.06966029526665807, -40, -70, 4320, -2135, 11788188.08, 2388218.07),
            array(40, 'LN9', 11788724.69117069430649281, 2388473.54868405824527144, -40, -70, 4320, -2135, 11788724.69, 2388473.55),
            array(41, 'LY10', 11785599.57843398675322533, 2392497.18166037555783987, -40, -70, 4320, -3125, 11785599.58, 2392497.18),
            array(42, 'LY11', 11785534.08516378700733185, 2392271.29036100627854466, -40, -70, 4320, -3125, 11785534.09, 2392271.29),
            array(43, 'LY12', 11785258.63217497803270817, 2392810.39757040143013, -40,-70, 4320, -3125, 11785258.63, 2392810.4),
            array(44, 'LY13', 11785536.99871129170060158, 2392808.87313925800845027, -40, -70, 4320,- 3125,11785537, 2392808.87),
            array(45, 'LY14', 11785791.0269112978130579, 2392269.8797018863260746, -40, -70, 4320, -3125, 11785791.03, 2392269.88),
            array(46, 'LY15', 11785324.00764934346079826, 2393014.74322587111964822, -40, -70, 4320, -3125, 11785324.01, 2393014.74),
            array(47, 'LY16', 11785228.59817047603428364, 2393198.42720729578286409, -40, -70, 4320, -3125, 11785228.6, 2393198.43),
            array(48, 'LY17', 11785133.36124992743134499, 2393414.42730379290878773, -40, -70, 4320, -3125, 11785133.36, 2393414.43),
            array(49, 'LY3', 11784735.13751051388680935, 2393017.95607117516919971, -40, -70, 4320, -3125, 11784735.14, 2393017.96),
            array(50, 'LY4', 11784840.97543985024094582, 2392801.90504860132932663, -40, -70, 4320, -3125, 11784840.98, 2392801.91),
            array(51, 'LY5', 11784863.37823952175676823, 2392984.94311415310949087, -40, -70, 4320, -3125, 11784863.38, 2392984.94),
            array(52, 'LY6', 11784563.76478518545627594, 2393018.88738845149055123, -40, -70, 4320, -3125, 11784563.76, 2393018.89),
            array(53, 'LY7', 11784734.03711271658539772, 2392813.25852541066706181, -40, -70, 4320, -3125, 11784734.04, 2392813.26),
            array(54, 'LY8', 11784562.60855001211166382, 2392803.41852059355005622, -40, -70, 4320, -3125, 11784562.61, 2392803.42),
            array(55, 'LY9', 11785536.99871129170060158, 2392808.87313925800845027, -40, -70, 4320,- 3125,11785537, 2392808.87),
            array(56, 'MD7', 11775916.97566106915473938, 2396090.40221136016771197, -40, -70, 4320, -2500, 11775916.98, 2396090.4),
            array(57, 'MD8', 11775639.81231772527098656, 2396350.39451547199860215, -40, -70, 4320, -2500, 11775639.81, 2396350.39),
            array(58, 'MD9', 11775641.17792554758489132, 2396630.57373226108029485, -40, -70, 4320, -2500, 11775641.18, 2396630.57),
            array(59, 'NH10', 11778853.89063355140388012, 2396614.43721777061000466, -40, -70, 4320, -3333, 11778853.89, 2396614.44),
            array(60, 'NH11', 11778595.36439158022403717, 2396335.5729674156755209, -40, -70, 4320, -3333, 11778595.36, 2396335.57),
            array(61, 'NH12', 11778852.47466489300131798, 2396334.26022379705682397, -40, -70, 4320, -3333, 11778852.47, 2396334.26),
            array(62, 'NH13', 11779390.52857245318591595, 2396870.31426244927570224, -40, -70, 4320, -3333, 11779390.53, 2396870.31),
            array(63, 'NH14', 11779133.52573507465422153, 2396871.6345574907027185, -40, -70, 4320, -3333, 11779133.53, 2396871.63),
            array(64, 'NH4', 11779321.49263433180749416, 2395997.80848255380988121, -40, -70, 4320, -3333, 11779321.49, 2395997.81),
            array(65, 'NH5', 11779215.84146180190145969, 2396203.09262026753276587, -40, -70, 4320, -3333, 11779215.84, 2396203.09),
            array(66, 'NH6', 11779130.79418394342064857, 2396332.83497229684144258, -40, -70, 4320, -3333, 11779130.79, 2396332.83),
            array(67, 'NH7', 11779132.21451539173722267, 2396613.01176991406828165, -40, -70, 4320, -3333, 11779132.21, 2396613.01),
            array(68, 'NH8', 11778851.16781957447528839, 2396075.64495939249172807, -40, -70, 4320, -3333, 11778851.17, 2396075.64),
            array(69, 'NH9', 11778856.61411543004214764, 2397153.23475438728928566, -40, -70, 4320, -3333, 11778856.61, 2397153.23),
            array(70, 'NSL14', 11782118.79915492050349712, 2394335.68804973131045699, -40, -70, 4320, -4545, 11782118.8, 2394335.69),
            array(71, 'NSL16', 11781778.66265136003494263, 2394800.80602123867720366, -40, -70, 4320, -4545, 11781778.66, 2394800.81),
            array(72, 'NSL17', 11781745.51815875805914402, 2394607.02874239487573504, -40, -70, 4320, -4545, 11781745.52, 2394607.03),
            array(73, 'NSL18', 11782066.36777259036898613, 2394529.91463655466213822, -40, -70, 4320, -4545, 11782066.37, 2394529.91),
            array(74, 'NSL21', 11782142.87746953777968884, 2394841.98270549112930894, -40, -70, 4320, -4545, 11782142.88, 2394841.98),
            array(75, 'NSL22', 11781684.6754087470471859, 2395264.62336773658171296, -40, -70, 4320, -4545, 11781684.68, 2395264.62),
            array(76, 'NSL23', 11781800.94027045369148254, 2394983.85897833714261651, -40, -70, 4320, -4545, 11781800.94, 2394983.86),
            array(77, 'NSL24', 11781744.39507952891290188, 2394391.53954998357221484, -40, -70, 4320, -4545, 11781744.4, 2394391.54),
            array(78, 'NSL25', 11781850.61456234194338322, 2394240.13197004934772849, -40, -70, 4320, -4545, 11781850.61, 2394240.13),
            array(79, 'NSL27', 11781928.57555761933326721, 2394810.78727500885725021, -40, -70, 4320, -4545, 11781928.58, 2394810.79),
            array(80, 'NSL9', 11782142.08785763010382652, 2394691.13602665718644857, -40, -70, 4320, -4545, 11782142.09, 2394691.14),
            array(81, 'PV1', 11782822.18435899540781975, 2387590.4185093673877418, -40, -70, 4320, -2808, 11782822.18, 2387590.42),
            array(82, 'PV2', 11782821.33491864986717701, 2387428.86434731679037213, -40, -70, 4320, -2808, 11782821.33, 2387428.86),
            array(83, 'PV3', 11782906.42668719962239265, 2387342.24314445350319147, -40, -70, 4320, -2808, 11782906.43, 2387342.24),
            array(84, 'PV6', 11783090.38631781563162804, 2387707.46468133293092251, -40, -70, 4320, -2808, 11783090.39, 2387707.46),
            array(85, 'PV7', 11783089.13685310818254948, 2387470.51122804777696729, -40, -70, 4320, -2808, 11783089.14, 2387470.51),
            array(86, 'TM10', 11783188.79112967662513256, 2390140.21012788079679012, -45, -75, 4320, -3125, 11783188.79, 2390140.21),
            array(87, 'TM11', 11783639.82684437558054924, 2390396.32815032452344894, -45, -75, 4320, -3125, 11783639.83, 2390396.33),
            array(88, 'TM14', 11783641.19957356713712215, 2390654.85946180252358317, -45, -75, 4320, -3125, 11783641.2, 2390654.86),
            array(89, 'TM15', 11783916.66560793109238148, 2390114.75327363051474094, -45, -75, 4320, -3125, 11783916.67, 2390114.75),
            array(90, 'TM16', 11783337.80219421535730362, 2389967.06175833381712437, -45, -75, 4320, -3125, 11783337.8, 2389967.06),
            array(91, 'TM18', 11783670.46493076905608177, 2390116.07785767177119851, -45, -75, 4320, -3125, 11783670.46, 2390116.08),
            array(92, 'TM19', 11783918.15691797249019146, 2390394.83077630028128624, -45, -75, 4320, -3125, 11783918.16, 2390394.83),
            array(93, 'TM8', 11783381.43655735999345779, 2390117.62849007733166218, -45, -75, 4320, -3125, 11783381.44, 2390117.63),
            array(94, 'UCF22', 11783788.51202009432017803, 2390120.82815810898318887, -2.96, -19.94, 4320, -360, 11783788.51, 2390120.83),
            array(95, 'YP10', 11782881.68859566561877728, 2396852.00869912700727582, -45, -75, 4320, -4400, 11782881.69, 2396852.01),
            array(96, 'YP12', 11783141.60782415792346001, 2397400.20711772469803691, -45, -75, 4320, -4400, 11783141.61, 2397400.21),
            array(97, 'YP15', 11782885.9567362554371357, 2397660.21421966049820185, -45, -75, 4320, -4400, 11782885.96, 2397660.21),
            array(98, 'YP16', 11782884.59073513932526112, 2397401.58236767631024122, -45, -75, 4320, -4400, 11782884.59, 2397401.58),
            array(99, 'YP17', 11783141.60782415792346001, 2397400.20711772469803691, -45, -75, 4320, -4400, 11783141.61, 2397400.21),
            array(100, 'YP18', 11782606.15094231255352497, 2397403.06802417244762182, -45, -75, 4320, -4400, 11782606.15, 2397403.07),
            array(101, 'YP19', 11782883.16801407374441624, 2397132.18036714708432555, -45, -75, 4320, -4400, 11782883.17, 2397132.18),
            array(102, 'YP20', 11782604.73241920955479145, 2397133.66582690924406052, -45, -75, 4320, -4400, 11782604.73, 2397133.67),
            array(103, 'YP22', 11782349.14232590608298779, 2397404.4354342189617455, -45, -75, 4320, -4400, 11782349.14, 2397404.44),
            array(104, 'YP26', 11783141.60782415792346001, 2397400.20711772469803691, -45, -75, 4320, -4400, 11783141.61, 2397400.21),
            array(105, 'YP27', 11783140.18122811987996101, 2397130.80529930861666799, -45, -75, 4320, -4400, 11783140.18, 2397130.81),
            array(106, 'YP29', 11782607.5129128210246563, 2397661.7000650349073112, -45, -75, 4320, -4400, 11782607.51, 2397661.7),
            array(107, 'YP30', 11782885.9567362554371357, 2397660.21421966049820185, -45, -75, 4320, -4400, 11782885.96, 2397660.21),
            array(108, 'YP31', 11782353.32513551786541939, 2398200.81470863055437803, -45, -75, 4320, -4400, 11782353.33, 2398200.81),
            array(109, 'YP32', 11782353.32513551786541939, 2398200.81470863055437803, -45, -75, 4320, -4400, 11782353.33, 2398200.81),
            array(110, 'YP33', 11782354.79713419266045094, 2398481.00762987695634365, -45, -75, 4320, -4400, 11782354.8, 2398481.01),
            array(111, 'YP34', 11782077.69510736502707005, 2398741.13343313755467534, -45, -75, 4320, -4400, 11782077.7, 2398741.13),
            array(112, 'YP35', 11782077.69510736502707005, 2398741.13343313755467534, -45, -75, 4320, -4400, 11782077.7, 2398741.13),
            array(113, 'YP36', 11781727.0486772432923317, 2399281.86090355273336172, -45, -75, 4320, -4400, 11781727.05, 2399281.86),
            array(114, 'YP37', 11781822.13090667873620987, 2399022.69560982333496213, -45, -75, 4320, -4400, 11781822.13, 2399022.7),
            array(115, 'YP38', 11781822.13090667873620987, 2399022.69560982333496213, -45, -75, 4320, -4400, 11781822.13, 2399022.7),
            array(116, 'YP39', 11781546.47275182791054249, 2399563.04242969118058681, -45, -75, 4320, -4400, 11781546.47, 2399563.04),
            array(117, 'YP40', 11781375.76468020677566528, 2399704.0468372106552124, -45, -75, 4320, -4400, 11781375.76, 2399704.05),
            array(118, 'YP41', 11781130.26916185207664967, 2399867.00588304502889514, -45, -75, 4320, -4400, 11781130.27, 2399867.01),
            array(119, 'YP42', 11780758.02947362139821053, 2400364.75818847771733999, -45, -75, 4320, -4400, 11780758.03, 2400364.76)

        );

        $header = array('id', 'name', 'wkt_x', 'wkt_y', 'ztop', 'zbot', 'stoptime', 'pumpingrate', 'x', 'y');
        foreach ($wells as $row) {
            $wellData = array_combine($header, $row);
            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint($geoTools->transformPoint(new Point($wellData['x'], $wellData['y'], 3857), 4326)),
                WellType::fromString(WellType::TYPE_PUBLIC_WELL),
                LayerNumber::fromInteger(4),
                PumpingRate::fromValue($wellData['pumpingrate'])
            );

            $commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $well));
        }

        # THIS WELLS ARE THE MISSING BLACK DOTS IN THE IMAGE
        $wells = array(
            array('I01', 105.79210, 21.08638, 4326, -4900),
            array('I02', 105.78936, 21.08686, 4326, -4900),
            array('I03', 105.78712, 21.08670, 4326, -4900),
            array('I04', 105.87278, 21.00259, 4326, -4900),
            array('I05', 105.77700, 21.03880, 4326, -4900),
            array('I06', 105.77700, 21.03624, 4326, -4900),
            array('I07', 105.77734, 21.03240, 4326, -4900),
            array('I08', 105.77271, 21.03288, 4326, -4900),
            array('I09', 105.77219, 21.03929, 4326, -4900),

        );
        $header = array('name', 'x', 'y', 'srid', 'pumpingrate');
        foreach ($wells as $row) {
            $wellData = array_combine($header, $row);
            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint(new Point($wellData['x'], $wellData['y'], 4326)),
                WellType::fromString(WellType::TYPE_PUBLIC_WELL),
                LayerNumber::fromInteger(4),
                PumpingRate::fromValue($wellData['pumpingrate'])
            );

            $commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $well));
        }

        /* Add Industrial Wells */
        $industrialWells = array(
            array("G82", 589825, 2318390, 32648, 100),
            array("G85", 588149, 2325683, 32648, 250),
            array("G87", 580652, 2325872, 32648, 50),
            array("G89", 585324, 2320139, 32648, 75),
            array("G90", 582070, 2324835, 32648, 75),
            array("G91", 592239, 2317049, 32648, 50),
            array("G95", 583264, 2322450, 32648, 454),
            array("G96", 590387, 2322104, 32648, 200),
            array("G100", 586872, 2320714, 32648, 200),
            array("G104", 587651, 2317690, 32648, 50),
            array("G105", 583957, 2326907, 32648, 160),
            array("G113", 584777, 2322331, 32648, 800),
            array("G115", 591454, 2321985, 32648, 30),
            array("G121", 589536, 2320375, 32648, 840),
            array("G124", 583387, 2322271, 32648, 80),
            array("G126", 580549, 2325334, 32648, 50),
            array("G129", 582286, 2321931, 32648, 50),
            array("G132", 581283, 2323830, 32648, 300),
            array("G139", 584290, 2323219, 32648, 110),
            array("G140", 580256, 2326451, 32648, 50),
            array("G143", 583051, 2322539, 32648, 50),
            array("G147", 583991, 2330192, 32648, 100),
            array("G149", 585380, 2326708, 32648, 2400),
            array("G158", 591472, 2322405, 32648, 100),
            array("G160", 589836, 2320675, 32648, 1200),
            array("G165", 584721, 2330609, 32648, 800),
            array("G166", 584421, 2322496, 32648, 25),
            array("G168", 592492, 2319625, 32648, 130),
            array("G173", 586761, 2328726, 32648, 60),
            array("G177", 587061, 2320748, 32648, 100),
            array("G178", 581283, 2323803, 32648, 300),
            array("G179", 590393, 2322230, 32648, 120),
            array("G180", 589832, 2320756, 32648, 35),
            array("G182", 581314, 2325558, 32648, 50),
            array("G186", 583245, 2321702, 32648, 200),
            array("G188", 592011, 2320064, 32648, 120),
            array("G194", 581522, 2325719, 32648, 50),
            array("G198", 583391, 2322641, 32648, 200),
            array("G205", 583982, 2330216, 32648, 60),
            array("G210", 581595, 2323053, 32648, 250),
            array("G211", 590089, 2322184, 32648, 500),
            array("G212", 591289, 2322419, 32648, 120),
            array("G216", 591187, 2320081, 32648, 50),
            array("G220", 582958, 2321854, 32648, 50),
            array("G222", 581611, 2327484, 32648, 200),
            array("G224", 580041, 2326316, 32648, 45),
            array("G225", 587355, 2328129, 32648, 75),
            array("G226", 580504, 2325091, 32648, 1200),
            array("G230", 586872, 2328865, 32648, 250),
            array("G231", 586945, 2328926, 32648, 400),
            array("G232", 586588, 2328622, 32648, 180),
            array("G244", 592449, 2317787, 32648, 100),
            array("G246", 581681, 2331396, 32648, 320),
            array("G248", 583489, 2329712, 32648, 600),
            array("G249", 583415, 2330196, 32648, 200),
            array("G251", 581605, 2331490, 32648, 500),
            array("G252", 583806, 2322082, 32648, 100),
            array("G253", 583991, 2324371, 32648, 250),
            array("G260", 586873, 2319335, 32648, 900),
            array("G261", 587008, 2317319, 32648, 300),
            array("G268", 585265, 2318944, 32648, 270),
            array("G272", 583837, 2323304, 32648, 195),
            array("G273", 590013, 2321476, 32648, 1500),
            array("G275", 583601, 2323241, 32648, 1200),
            array("G279", 581899, 2331001, 32648, 53),
            array("G282", 581324, 2325558, 32648, 20),
            array("G283", 586473, 2321948, 32648, 5235),
            array("G285", 583601, 2323241, 32648, 3600),
            array("G289", 582466, 2322284, 32648, 100),
            array("G293", 583810, 2329951, 32648, 40),
            array("G294", 591703, 2317288, 32648, 4),
            array("G295", 591213, 2321121, 32648, 150),
            array("G297", 583594, 2329881, 32648, 170),
            array("G306", 580683, 2330793, 32648, 50),
            array("G309", 581590, 2327406, 32648, 96),
            array("G310", 580463, 2330567, 32648, 750),
            array("G312", 586404, 2322667, 32648, 1680),
            array("G314", 583439, 2325569, 32648, 70),
            array("G315", 582630, 2321279, 32648, 250),
            array("G319", 582287, 2324144, 32648, 120),
            array("G320", 582549, 2322446, 32648, 300),
            array("G321", 582443, 2322418, 32648, 240),
            array("G332", 585228, 2320455, 32648, 80),
            array("G335", 582746, 2322520, 32648, 70),
            array("G337", 580575, 2330602, 32648, 72),
            array("G341", 582604, 2321525, 32648, 60),
            array("G343", 592870, 2319201, 32648, 3220),
            array("G344", 592358, 2321666, 32648, 1920),
            array("G345", 588502, 2319702, 32648, 2600),
            array("G346", 593001, 2319421, 32648, 350),
            array("G347", 591408, 2321234, 32648, 800),
            array("G348", 590015, 2318501, 32648, 680),
            array("G356", 587024, 2320407, 32648, 20),
            array("G358", 583981, 2322216, 32648, 50),
            array("G363", 587158, 2320346, 32648, 400),
            array("G364", 580726, 2330344, 32648, 50),
            array("G365", 586670, 2320936, 32648, 120),
            array("G367", 583982, 2325224, 32648, 855),
            array("G368", 585520, 2319359, 32648, 120),
            array("G369", 584273, 2330866, 32648, 75),
            array("NT20", 586823, 2318826, 32648, 550),
            array("NT21", 587963, 2317474, 32648, 2125),
            array("NT22", 586932, 2317229, 32648, 900),
            array("NT31", 586113, 2321678, 32648, 7680),
            array("NT32", 588421, 2318928, 32648, 120),
            array("NT36", 585517, 2319750, 32648, 400)
        );
        foreach ($industrialWells as $industrialWell) {
            $header = array('name', 'x', 'y', 'srid', 'pumpingrate');
            $wellData = array_combine($header, $industrialWell);

            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint($geoTools->transformPoint(new Point($wellData['x'], $wellData['y'], $wellData['srid']), 4326)),
                WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
                LayerNumber::fromInteger(4),
                PumpingRate::fromValue($wellData['pumpingrate'])
            );

            $commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $well));
        }

        $headsS0L3 = $this->loadHeadsFromFile(__DIR__."/../DataFixtures/ES/Scenarios/Hanoi/data/base_scenario_head_layer_3.json");
        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modelId));
        $commandBus->dispatch(AddResultToCalculation::to($calculationId,
            CalculationResult::fromParameters(
                TotalTime::fromInt(120),
                CalculationResultType::fromString(CalculationResultType::HEAD_TYPE),
                CalculationResultData::from3dArray([[], [], $headsS0L3, []])
            )
        ));

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
                PumpingRate::fromValue($wellData['pumpingrate'])
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
                PumpingRate::fromValue($wellData['pumpingrate'])
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        /* Add Calculation */
        $calculationId = ModflowId::generate();
        $heads_S1L3 = $this->loadHeadsFromFile(__DIR__."/../DataFixtures/ES/Scenarios/Hanoi/data/scenario_1_head_layer_3.json");
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modelId, $scenarioId));
        $commandBus->dispatch(AddResultToCalculation::to($calculationId,
            CalculationResult::fromParameters(
                TotalTime::fromInt(1095),
                CalculationResultType::fromString(CalculationResultType::HEAD_TYPE),
                CalculationResultData::from3dArray([$heads_S1L3, $heads_S1L3, $heads_S1L3, $heads_S1L3])
            )
        ));



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
                PumpingRate::fromValue($wellData['pumpingrate'])
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        $heads_S2L3 = $this->loadHeadsFromFile(__DIR__."/../DataFixtures/ES/Scenarios/Hanoi/data/scenario_2_head_layer_3.json");
        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modelId, $scenarioId));
        $commandBus->dispatch(AddResultToCalculation::to($calculationId,
            CalculationResult::fromParameters(
                TotalTime::fromInt(1095),
                CalculationResultType::fromString(CalculationResultType::HEAD_TYPE),
                CalculationResultData::from3dArray([$heads_S2L3, $heads_S2L3, $heads_S2L3, $heads_S2L3])
            )
        ));

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
                PumpingRate::fromValue($wellData['pumpingrate'])
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
                PumpingRate::fromValue($wellData['pumpingrate'])
            );

            $commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $well));
        }

        $headsS3L3 = $this->loadHeadsFromFile(__DIR__."/../DataFixtures/ES/Scenarios/Hanoi/data/scenario_3_head_layer_3.json");
        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modelId, $scenarioId));
        $commandBus->dispatch(AddResultToCalculation::to($calculationId,
            CalculationResult::fromParameters(
                TotalTime::fromInt(120),
                CalculationResultType::fromString(CalculationResultType::HEAD_TYPE),
                CalculationResultData::from3dArray([$headsS3L3, $headsS3L3, $headsS3L3, $headsS3L3])
            )
        ));

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
                }
            }
        }

        return $heads;
    }
}
