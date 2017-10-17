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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @return Response
     * @throws \InvalidArgumentException
     * @Rest\Post("/rasterfile")
     * @Rest\FileParam(name="file", default=false, nullable=true)
     */
    public function uploadRasterfileAction(ParamFetcher $paramFetcher): Response
    {
        /** @var File $uploadedFile */
        $uploadedFile = $paramFetcher->get('file');
        $rasterFile = $this->get('inowas.modflowmodel.raster_files_persister')->save($uploadedFile);

        /** @var GeoProcessingResponse $response */
        $response = $this->get('inowas.modflowmodel.amqp_geo_processing')->sendRequest(
            GeoProcessingRequest::withMethodAndParameters(
                GeoProcessingRequest::METHOD_EXTRACT_RASTER_DATA,
                ['file' => $rasterFile->getFilename()]
            )
        );

        if (! $response->isValid()) {
            return new JsonResponse($response->body(), $response->statusCode()->toInt());
        }

        return new RedirectResponse( $this->generateUrl('get_rasterfile_data', ['hash' => $rasterFile->getFilename()]), 302);
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
     * @return Response
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     * @Rest\Get("/rasterfile/{hash}")
     */
    public function getRasterfileDataAction(string $hash): Response
    {
        $rasterFile = $this->get('inowas.modflowmodel.raster_files_persister')->load($hash);

        if (!$rasterFile instanceof File) {
            throw NotFoundException::withMessage(sprintf('Rasterfile with hash=%s not found', $hash));
        }

        /** @var GeoProcessingResponse $response */
        $response = $this->get('inowas.modflowmodel.amqp_geo_processing')->sendRequest(
            GeoProcessingRequest::withMethodAndParameters(
                GeoProcessingRequest::METHOD_EXTRACT_RASTER_DATA,
                ['file' => $rasterFile->getFilename()]
            )
        );

        return new JsonResponse($response->body(), $response->statusCode()->toInt());
    }
}
