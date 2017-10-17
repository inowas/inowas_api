<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Inowas\ModflowModel\Model\AMQP\GeoProcessingRequest;
use Inowas\ModflowModel\Model\AMQP\GeoProcessingResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadRasterController extends InowasRestController
{
    /**
     * Upload Rasterfile.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Upload rasterfile",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     * @Rest\Post("/rasterfile")
     * @Rest\FileParam(name="file", default=false, nullable=true)
     */
    public function uploadRasterfileAction(ParamFetcher $paramFetcher): JsonResponse
    {
        $uploadedFile = $paramFetcher->get('file');
        $file = $this->get('inowas.modflowmodel.raster_files_persister')->save($uploadedFile);

        /** @var GeoProcessingResponse $response */
        $response = $this->get('inowas.modflowmodel.amqp_geo_processing')->sendRequest(
            GeoProcessingRequest::withMethodAndParameters(
                GeoProcessingRequest::METHOD_EXTRACT_RASTER_DATA,
                ['file' => $file->getFilename()]
            )
        );

        if (! $response->isValid()) {
            return new JsonResponse($response->body(), $response->statusCode()->toInt());
        }

        return new JsonResponse('', 202);
    }
}
