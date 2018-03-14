<?php

namespace Inowas\AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Inowas\AppBundle\Model\User;
use Inowas\AppBundle\Model\UserProfile;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Exception\UserNotAuthenticatedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/** @noinspection LongInheritanceChainInspection */
class UserController extends InowasRestController
{
    /**
     * Returns the api-key of the user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the api-key of the user.",
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
     * @RequestParam(name="username", nullable=false, strict=true, description="Username or email")
     * @RequestParam(name="password", nullable=false, strict=true, description="Password")
     *
     * @return JsonResponse
     * @throws \RuntimeException
     */
    public function getUserCredentialsAction(ParamFetcher $paramFetcher): JsonResponse
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

        $this->get('logger')->addInfo(sprintf('User with username %s has requested API-Key', $username));

        $data = [];
        $data['api_key'] = $user->getApiKey();

        return new JsonResponse($data);
    }

    /**
     * Returns the api-key of the user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the api-key of the user.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Get("/users/enable/{username}")
     *
     * @param string $username
     * @param ParamFetcher $paramFetcher
     * @QueryParam(name="key", nullable=false, strict=true, description="Hash to enable the user.")
     * @QueryParam(name="redirectTo", nullable=false, strict=false, description="The redirect Url.")
     * @return RedirectResponse
     * @throws \Inowas\ModflowBundle\Exception\InvalidArgumentException
     */
    public function enableUserAction(string $username, ParamFetcher $paramFetcher): RedirectResponse
    {

        $key = base64_decode($paramFetcher->get('key'));
        $redirectTo = base64_decode($paramFetcher->get('redirectTo'));

        /** @var User $user */
        $user = $this->get('fos_user.user_manager')->findUserByUsername(base64_decode($username));

        if ($user->isEnabled()) {
            return $this->redirect($redirectTo);
        }

        if (! $user instanceof User){
            throw InvalidArgumentException::withMessage(sprintf('Username unknown.'));
        }

        if ($user->getApiKey() !== $key) {
            throw InvalidArgumentException::withMessage(sprintf('Key not valid.'));
        }

        $user->setEnabled(true);
        $user->renewApiKey();
        $this->get('fos_user.user_manager')->updateUser($user);

        return $this->redirect($redirectTo);
    }

    /**
     * Returns the api-key of the user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the api-key of the user.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Post("/users/signup")
     *
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     * @RequestParam(name="name", nullable=false, strict=true, description="Name of the user")
     * @RequestParam(name="username", nullable=false, strict=true, description="Username")
     * @RequestParam(name="email", nullable=false, strict=true, description="email")
     * @RequestParam(name="password", nullable=false, strict=true, description="Password")
     * @RequestParam(name="redirectTo", nullable=false, strict=true, description="Redirect to")
     *
     * @throws \Inowas\ModflowBundle\Exception\InvalidArgumentException
     */
    public function signupUserAction(ParamFetcher $paramFetcher): JsonResponse
    {
        $name = $paramFetcher->get('name');
        $email = $paramFetcher->get('email');
        $password = $paramFetcher->get('password');
        $username = $paramFetcher->get('username');

        /** @var User $user */
        $user = $this->get('fos_user.user_manager')->findUserByUsername($username);

        if ($user instanceof User){
            throw InvalidArgumentException::withMessage(sprintf(
                'The username already exits.'
            ));
        }

        /** @var User $user */
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if ($user instanceof User){
            throw InvalidArgumentException::withMessage(sprintf(
                'The email already exits.'
            ));
        }

        $user = $this->get('fos_user.user_manager')->createUser();
        $user->setName($name);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);

        $this->get('fos_user.user_manager')->updateUser($user);

        $redirectTo = $paramFetcher->get('redirectTo');

        $scheme = $this->getParameter('router.request_context.scheme');
        $host = $this->getParameter('router.request_context.host');
        $path =  $this->generateUrl('enable_user', [
            'username' => base64_encode($user->getUsername()),
            'key' => base64_encode($user->getApiKey()),
            'redirectTo' => base64_encode($redirectTo)
        ]);

        $url = sprintf('%s://%s%s', $scheme, $host, $path);

        $data = [];
        $data['api_key'] = $user->getApiKey();

        $message = (new \Swift_Message('Welcome to Inowas!'))
            ->setFrom('ralf.junghanns@tu-dresden.de')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    ':email:signup_activate_account.html.twig',
                    array('name' => $name, 'url' => $url)
                ),
                'text/html'
            )
        ;

        $this->get('swiftmailer.mailer')->send($message);

        return new JsonResponse($data);
    }

    /**
     * Returns the user profile and roles of the user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the user profile and roles of the user.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Get("/users")
     *
     * @return JsonResponse
     * @throws \Inowas\ModflowBundle\Exception\UserNotAuthenticatedException
     * @throws \LogicException
     */
    public function getUserProfileAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (! $user instanceof User){
            throw UserNotAuthenticatedException::withMessage(sprintf(
                'Something went wrong with the authentication. User is not authenticated. Please check your credentials.'
            ));
        }

        $response = array();
        $response['user_name'] = $user->getUsername();
        $response['name'] = $user->getName();
        $response['email'] = $user->getEmail();
        $response['roles'] = $user->getRoles();
        $response['enabled'] = $user->isEnabled();
        $response['profile'] = $user->getProfile()->toArray();

        return new JsonResponse($response);
    }

    /**
     * Returns the userProfile for the user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the api-key of the user.",
     *   statusCodes = {
     *     303 = "Redirect when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Put("/users")
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws UserNotAuthenticatedException
     */
    public function putUserProfileAction(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (! $user instanceof User){
            throw UserNotAuthenticatedException::withMessage(sprintf(
                'Something went wrong with the authentication. User is not authenticated. Please check your credentials.'
            ));
        }

        $content = $this->getContentAsArray($request);

        $key = 'user_name';
        if ($this->containsKey($key, $content)) {
            $user->setUsername($this->getValueByKey($key, $content));
        }

        $key = 'name';
        if ($this->containsKey($key, $content)) {
            $user->setName($this->getValueByKey($key, $content));
        }

        $key = 'email';
        if ($this->containsKey($key, $content)) {
            $user->setEmail($this->getValueByKey($key, $content));
        }

        $profile = UserProfile::fromArray($content);
        $user->setProfile($profile);

        $this->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->generateUrl('get_user_profile'), 303);
    }
}
