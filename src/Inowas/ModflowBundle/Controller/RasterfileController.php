<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\ModflowModel\Model\AMQP\GeoProcessingRequest;
use Inowas\ModflowModel\Model\AMQP\GeoProcessingResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RasterfileController extends InowasRestController
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
     * @throws \InvalidArgumentException
     * @Rest\Post("/rasterfile")
     * @Rest\FileParam(name="file", default=false, nullable=true)
     */
    public function uploadRasterfileAction(ParamFetcher $paramFetcher): JsonResponse
    {
        /** @var File $uploadedFile */
        $uploadedFile = $paramFetcher->get('file');
        $rasterFile = $this->get('inowas.modflowmodel.raster_files_persister')->save($uploadedFile);

        return new JsonResponse( [
            'hash' => $rasterFile->getFilename(),
            'url' => $this->generateUrl('get_rasterfile_data', ['hash' => $rasterFile->getFilename()])
        ]);
    }

    /**
     * Get rasterfile data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get rasterfile data",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $hash
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     * @Rest\QueryParam(name="width", default=false, description="Width (optional)")
     * @Rest\QueryParam(name="height", default=false, description="Height (optional)")
     * @Rest\QueryParam(name="method", default="nearest", description="Interpolation Method (optional)")
     * @Rest\Get("/rasterfile/{hash}")
     */
    public function getRasterfileDataAction(string $hash, ParamFetcher $paramFetcher): Response
    {
        $rasterFile = $this->get('inowas.modflowmodel.raster_files_persister')->load($hash);

        if (!$rasterFile instanceof File) {
            throw NotFoundException::withMessage(sprintf('Rasterfile with hash=%s not found', $hash));
        }

        /** @var GeoProcessingResponse $response */
        $response = $this->get('inowas.modflowmodel.amqp_geo_processing')->sendRequest(
            GeoProcessingRequest::withMethodAndParameters(
                GeoProcessingRequest::METHOD_EXTRACT_RASTER_DATA, [
                    'file' => $rasterFile->getFilename(),
                    'width' => $paramFetcher->get('width'),
                    'height' => $paramFetcher->get('height'),
                    'method' => $paramFetcher->get('method'),
                ]
            )
        );

        return new JsonResponse($response->body(), $response->statusCode()->toInt());
    }
}
