<?php

namespace AppBundle\DataFixtures\ORM\Scenarios;

use AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;

class LoadScenarioBase
{

    protected $owner;
    protected $userList;

    public function loadUsers(UserManager $userManager){

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
            $this->userList[]=$user;
        }

        $this->owner = $userManager->findUserByUsername('inowas');
        $this->owner->addRole('ROLE_ADMIN');
        $userManager->updateUser($this->owner);
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @return array
     */
    public function getUserList()
    {
        return $this->userList;
    }

}