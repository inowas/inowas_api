<?php

namespace Inowas\AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Inowas\AppBundle\Model\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends FOSRestController
{
    /**
     * Returns the model area details specified by modelId
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the model area details specified by modelId",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Post("/users/credentials")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @RequestParam(name="username", nullable=false, strict=true, description="Username or Email")
     * @RequestParam(name="password", nullable=false, strict=true, description="Password")
     *
     * @return JsonResponse
     */
    public function getUserCredentialsAction(ParamFetcher $paramFetcher)
    {
        $username = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');

        /** @var User $user */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $passwordIsValid = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());

        if (! $passwordIsValid){
            throw new HttpException(401, 'Credentials are not valid.');
        }

        $data = new \stdClass();
        $data->api_key = $user->getApiKey();

        return new JsonResponse($data);
    }
}