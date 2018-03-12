<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\DataFixtures\Scenarios;

use FOS\UserBundle\Model\UserManager;
use Inowas\Common\Fixtures\DataFixtureInterface;
use Inowas\Common\Id\UserId;
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
    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    protected function loadUsers(UserManager $userManager): void
    {

        $userListHeads = array('username', 'name', 'email', 'password', 'roles');
        $userList = array(
            array('guest', 'guest', 'guest@inowas.com', 'guest', []),
            array('inowas', 'inowas', 'inowas@inowas.com', '#inowas#', ['ROLE_NM_MF'])
        );

        foreach ($userList as $item) {
            $item = array_combine($userListHeads, $item);
            $user = $userManager->findUserByUsername($item['username']);

            /** @var array $roles */
            $roles = $item['roles'];

            if (!$user) {
                // Add new User
                $user = $userManager->createUser();
                $user->setUsername($item['username']);
                $user->setName($item['name']);
                $user->setEmail($item['email']);
                $user->setPlainPassword($item['password']);
                $user->setEnabled(true);

                foreach ($roles as $role) {
                    $user->addRole($role);
                }

                $userManager->updateUser($user);
            }

            foreach ($roles as $role) {
                if (!$user->hasRole($role)) {
                    $user->addRole($role);
                    $userManager->updateUser($user);
                }
            }

            $this->userIdList[] = UserId::fromString($user->getId()->toString());
        }

        $owner = $userManager->findUserByUsername('inowas');
        $owner->addRole('ROLE_ADMIN');
        $userManager->updateUser($owner);
        $this->ownerId = $owner->getId()->toString();
    }

    protected function loadRowsFromCsv($filename): array
    {
        $header = null;
        $rows = array();
        if (($handle = fopen($filename, 'rb')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                if (null === $header) {
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
        if (($handle = fopen($filename, 'rb')) !== FALSE) {
            $data = fgetcsv($handle, 1000, ';');
            fclose($handle);
        }

        return $data;
    }

    protected function getDates(array $header): array
    {
        $dates = array();
        foreach ($header as $data) {
            if (explode(':', $data)[0] === 'date') {
                $dates[] = $data;
            }
        }
        return $dates;
    }
}
