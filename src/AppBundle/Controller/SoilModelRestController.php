<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModelScenario;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\PropertyValue;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\RasterFactory;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageParameter;
use Inowas\PyprocessingBundle\Service\GeoImage;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SoilModelRestController extends FOSRestController
{
    /**
     * Return the overall soilmodel list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall soilmodel list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserSoilmodelsAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (!$user) {
            throw $this->createNotFoundException('User with username ' . $username . ' not found.');
        }

        $soilModels = $this->getDoctrine()
            ->getRepository('AppBundle:SoilModel')
            ->findBy(
                array(
                    'owner' => $user,
                    'public' => true
                ),
                array('id' => 'ASC')
            );

        $view = View::create();
        $view->setData($soilModels)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('soilmodellist'))
            );

        return $view;
    }

    /**
     * Return the soilmodel details by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the soilmodel details by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getSoilmodelsAction($id)
    {

        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Soilmodel with id=' . $id . ' not found.');
        }

        $soilModel = $this->getDoctrine()
            ->getRepository('AppBundle:SoilModel')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (!$soilModel) {
            throw $this->createNotFoundException('Soilmodel with id=' . $id . ' not found.');
        }

        $view = View::create();
        $view->setData($soilModel)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('soilmodeldetails')));

        return $view;
    }

    /**
     * Return all layers from a soilmodel (id) sorted from top to bottom.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return all layers from a soilmodel (id) sorted from top to bottom.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getSoilmodelsGeologicallayersAction($id)
    {

        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Soilmodel with id=' . $id . ' not found.');
        }

        $soilModel = $this->getDoctrine()
            ->getRepository('AppBundle:SoilModel')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (!$soilModel) {
            throw $this->createNotFoundException('Soilmodel with id=' . $id . ' not found.');
        }

        $view = View::create();
        $view->setData($soilModel->getSortedGeologicalLayers())
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('soilmodellayers')));

        return $view;
    }

    /**
     * Return the ModflowModel Layer Property Image.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the ModflowModel Layer Property Image.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     * @param $layerNumber
     * @param $propertyAbbreviation
     * @QueryParam(name="_format", nullable=true, description="Image format, default png", default="png")
     *
     * @return BinaryFileResponse
     * @throws
     */
    public function getModflowmodelLayerPropertyAction(ParamFetcher $paramFetcher, $id, $layerNumber, $propertyAbbreviation)
    {

        // Top-Elevation == Bottom elevation of the layer above
        if ($layerNumber > 0 && $propertyAbbreviation == PropertyType::TOP_ELEVATION) {
            $layerNumber -= 1;
            $propertyAbbreviation = PropertyType::BOTTOM_ELEVATION;
        }

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if (!$model->hasSoilModel()) {
            throw new NotFoundHttpException(sprintf('ModflowModel %s has no SoilModel already.', $model->getId()->toString()));
        }

        $soilModel = $model->getSoilModel();
        if (!$soilModel->hasGeologicalLayers()) {
            throw new NotFoundHttpException(sprintf('ModflowModel %s has a Soilmodel without Layers.', $model->getId()->toString()));
        }

        /** @var GeologicalLayer $layer */
        $layer = $soilModel->getLayerByNumber($layerNumber);
        if (null === $layer) {
            throw new NotFoundHttpException(sprintf('SoilModel has no Layer with layernumber %s.', $layerNumber));
        }

        /** @var PropertyType $propertyType */
        $propertyType = PropertyTypeFactory::create($propertyAbbreviation);
        $property = $layer->getPropertyByPropertyType($propertyType);

        if (null === $property) {
            throw new \Exception(sprintf('Layer %s has no Property %s', $layerNumber, $propertyAbbreviation));
        }

        if (!$property->getValues()->count() == 1) {
            throw new \Exception('Property has more then one value');
        }

        /** @var PropertyValue $propertyValue */
        $propertyValue = $property->getValues()->first();

        if ($propertyValue->hasRaster()) {
            $raster = $propertyValue->getRaster();
        } elseif ($propertyValue->hasValue()) {
            $raster = RasterFactory::create()
                ->setGridSize($model->getGridSize())
                ->setBoundingBox($model->getBoundingBox());

            $data = array();
            $value = $propertyValue->getValue();

            $ny = $model->getGridSize()->getNY();
            for ($y = 0; $y < $ny; $y++) {
                $data[] = array_fill(0, $model->getGridSize()->getNX(), $value);
            }

            $raster->setData($data);
        } else {
            throw new \Exception('PropertyValue has no Value');
        }

        $colorScheme = GeoImage::COLOR_RELIEF_JET;
        $min = 290;
        $max = 480;
        if ($propertyType->getAbbreviation() == "hh") {
            $colorScheme = GeoImage::COLOR_RELIEF_GIST_RAINBOW;
            $min = -30;
            $max = 8;
        }

        $fileFormat = $paramFetcher->get('_format');
        $geoImageService = $this->get('inowas.geoimage');
        $geoImageParameter = new GeoImageParameter($raster, $model->getActiveCells()->toArray(), $min, $max, $fileFormat, $colorScheme);
        $geoImageService->createImage($geoImageParameter);
        $outputFileName = $geoImageService->getOutputFileName();

        $fs = new Filesystem();
        if (!$fs->exists($outputFileName)) {
            throw new \Exception('Something went wrong creating the image');
        }

        $response = new BinaryFileResponse($outputFileName);
        switch ($fileFormat) {
            case "png":
                $hType = "image/png";
                break;
            case "tiff":
                $hType = "image/tiff";
                break;
            default:
                $hType = "";
        }

        $response->headers->set('Content-Type', $hType);
        return $response;
    }


    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findModelById($id)
    {

        if (!Uuid::isValid($id)) {
            throw $this->createNotFoundException('Model with id=' . $id . ' not found.');
        }

        $scenario = $this->getDoctrine()
            ->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModelScenario) {
            return $scenario->getModel();
        }

        $model = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        return $model;
    }
}
