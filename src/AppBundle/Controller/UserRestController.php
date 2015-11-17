<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Validator\ConstraintViolationList;

class UserRestController extends FOSRestController
{
    /**
     * Return the overall user list.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall User List",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function getUsersAction()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $entity = $userManager->findUsers();

        if (!$entity) {
            throw $this->createNotFoundException('Data not found.');
        }

        $view = View::create();
        $view->setData($entity)->setStatusCode(200);

        return $view;
    }

    /**
     * Return an user identified by username/email.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an user identified by username/email",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $slug username or email
     *
     * @return View
     */
    public function getUserAction($slug)
    {

        $userManager = $this->container->get('fos_user.user_manager');
        $entity = $userManager->findUserByUsernameOrEmail($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Data not found.');
        }

        $view = View::create();
        $view->setData($entity)->setStatusCode(200);

        return $view;
    }

    /**
     * Create a User from the submitted data.<br/>
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new user from the submitted data.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="username", nullable=false, strict=true, description="Username.")
     * @RequestParam(name="email", nullable=false, strict=true, description="Email.")
     * @RequestParam(name="password", nullable=false, strict=true, description="Plain Password.")
     *
     * @return View
     */
    public function postUserAction(ParamFetcher $paramFetcher)
    {

        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($paramFetcher->get('username'));
        $user->setEmail($paramFetcher->get('email'));
        $user->setPlainPassword($paramFetcher->get('password'));
        $user->setEnabled(true);

        $view = View::create();

        $errors = $this->get('validator')->validate($user, array('Registration'));

        if (count($errors) == 0) {
            $userManager->updateUser($user);
            $view->setData($user)->setStatusCode(200);
            return $view;
        } else {
            $view = $this->getErrorsView($errors);
            return $view;
        }
    }

    /**
     * Update a User from the submitted data by ID.<br/>
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates a user from the submitted data by ID.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="id", nullable=false, strict=true, description="id.")
     * @RequestParam(name="username", nullable=true, strict=true, description="Username.")
     * @RequestParam(name="email", nullable=true, strict=true, description="Email.")
     * @RequestParam(name="name", nullable=true, strict=true, description="Name.")
     * @RequestParam(name="lastname", nullable=true, strict=true, description="Lastname.")
     * @RequestParam(name="password", nullable=true, strict=true, description="Plain Password.")
     *
     * @return View
     */
    public function putUserAction(ParamFetcher $paramFetcher)
    {

        $entity = $this->getDoctrine()->getRepository('AppBundle\Entity\User')->findOneBy(
            array('id' => $paramFetcher->get('id'))
        );

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($entity->getUsername());

        if($paramFetcher->get('username')){ $user->setUsername($paramFetcher->get('username')); }
        if($paramFetcher->get('email')){$user->setEmail($paramFetcher->get('email')); }
        if($paramFetcher->get('password')){$user->setPlainPassword($paramFetcher->get('password')); }
        if($paramFetcher->get('name')){$user->setName($paramFetcher->get('name')); }
        if($paramFetcher->get('lastname')){$user->setLastname($paramFetcher->get('lastname')); }

        $view = View::create();

        $errors = $this->get('validator')->validate($user, array('Update'));

        if (count($errors) == 0) {
            $userManager->updateUser($user);
            $view->setData($user)->setStatusCode(200);
            return $view;
        } else {
            $view = $this->getErrorsView($errors);
            return $view;
        }
    }

    /**
     * Delete an user identified by username/email.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete an user identified by username/email",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $slug username or email
     *
     * @return View
     */
    public function deleteUserAction($slug)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $entity = $userManager->findUserByUsernameOrEmail($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Data not found.');
        }

        $userManager->deleteUser($entity);

        $view = View::create();
        $view->setData("User deteled.")->setStatusCode(204);

        return $view;
    }

    /**
     * Get User Salt.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user salt by its username",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id the user username
     *
     * @return View
     */
    public function getUserSaltAction($slug)
    {

        $entity = $this->getDoctrine()->getRepository('AppBundle\Entity\User')->findOneBy(
            array('username' => $slug)
        );

        if (!$entity) {
            throw $this->createNotFoundException('Data not found.');
        }

        $salt = $entity->getSalt();

        $view = View::create();
        $view->setData(array('salt' => $salt))->setStatusCode(200);

        return $view;
    }

    /**
     * Get UserProfile.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get users profile by its username",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $slug
     * @return View
     * @internal param string $id the user username
     *
     */
    public function getUserProfileAction($slug)
    {

        $requestedUser = $this->getDoctrine()->getRepository('AppBundle\Entity\User')->findOneBy(
            array('username' => $slug)
        );

        if (!$requestedUser) {
            throw $this->createNotFoundException('Data not found.');
        }

        if (!$this->isGranted('ROLE_ADMIN'))
        {
            if ($requestedUser !== $this->getUser() )
            {
                throw $this->createAccessDeniedException('Access denied.');
            }
        }

        $profile = $requestedUser->getProfile();

        $view = View::create();
        $view->setData(array('profile' => $profile))->setStatusCode(200);

        return $view;
    }


    /**
     * Update a UserProfile from the submitted data by User-ID.<br/>
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates a UserProfile from the submitted data by ID.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="id", nullable=false, strict=true, description="user-id")
     * @RequestParam(name="firstname", nullable=true, strict=false, description="Firstname.")
     * @RequestParam(name="lastname", nullable=true, strict=false, description="Lastname.")
     *
     * @return View
     */
    public function putUsersProfileAction(ParamFetcher $paramFetcher)
    {

        $entity = $this->getDoctrine()->getRepository('AppBundle\Entity\User')->findOneBy(
            array('id' => $paramFetcher->get('id'))
        );

        $userManager = $this->container->get('fos_user.user_manager');
        $requestedUser = $userManager->findUserByUsername($entity->getUsername());

        if (!$requestedUser) {
            throw $this->createNotFoundException('Data not found.');
        }

        if (!$this->isGranted('ROLE_ADMIN'))
        {
            if ($requestedUser !== $this->getUser() )
            {
                throw $this->createAccessDeniedException('Access denied.');
            }
        }

        if($paramFetcher->get('firstname')){$requestedUser->getProfile()->setFirstName($paramFetcher->get('firstname')); }
        if($paramFetcher->get('lastname')){$requestedUser->getProfile()->setLastname($paramFetcher->get('lastname')); }

        $view = View::create();

        $userManager->updateUser($requestedUser);
        $view->setData($requestedUser)->setStatusCode(200);
        return $view;
    }

    /**
     * Get the validation errors
     *
     * @param ConstraintViolationList $errors Validator error list
     *
     * @return View
     */
    protected function getErrorsView(ConstraintViolationList $errors)
    {
        $msgs = array();
        $errorIterator = $errors->getIterator();
        foreach ($errorIterator as $validationError) {
            $msg = $validationError->getMessage();
            $params = $validationError->getMessageParameters();
            $msgs[$validationError->getPropertyPath()][] = $this->get('translator')->trans($msg, $params, 'validators');
        }
        $view = View::create($msgs);
        $view->setStatusCode(400);

        return $view;
    }

    /**
     * Return the users dashboard.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the users dashboard",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function getUserDashboardAction()
    {
        $points = $this->getDoctrine()
            ->getRepository('AppBundle:Ticket')
            ->getDashBoard($this->getUser());

        $view = View::create();
        $view->setData($points)->setStatusCode(200);

        return $view;
    }

}
