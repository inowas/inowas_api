<?php

namespace Tests\AppBundle;

use AppBundle\Entity\User;
use AppBundle\Model\UserFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestControllerTestCase extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    private $entityManager;

    /** @var User $owner */
    private $owner;

    /** @var User $user */
    protected $user;


    public function getEntityManager(){
        if (! $this->entityManager instanceof EntityManager ){
            self::bootKernel();
            $this->entityManager = static::$kernel->getContainer()
                ->get('doctrine.orm.default_entity_manager')
            ;
        }

        return $this->entityManager;
    }

    public function getUser(){
        if (! $this->user instanceof User){
            $this->user = UserFactory::createTestUser('User'.rand(1000, 15000));
        }

        return $this->user;
    }

    public function getOwner(){
        if (! $this->owner instanceof User){
            $this->owner = UserFactory::createTestUser('Owner'.rand(1000, 15000));
        }

        return $this->owner;
    }
}
