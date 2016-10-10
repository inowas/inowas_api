<?php
/**
 *
 */
namespace Tests\AppBundle\Database;

use AppBundle\Entity\User;
use AppBundle\Entity\UserProfile;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * SecurityTests
 */
class RegisterUserTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \FOS\UserBundle\Doctrine\UserManager $userManager
     */
    private $userManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $this->userManager = static::$kernel->getContainer()
            ->get('fos_user.user_manager')
        ;
    }

    public function testTest()
    {
        $this->assertTrue(true);
    }

    /**
     * TODO make a test of it
     */
    public function addingANewUserAddsAProfile()
    {
        $userName = "testUser";
        $email = "testUser@domain.com";
        $password = "testsUsersPassword";

        $user = UserFactory::create();
        $user->setUsername($userName);
        $user->setEmail($email);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $userName
            ));

        $this->assertNotNull($user);

        $profile = $this->entityManager->getRepository('AppBundle:UserProfile')
            ->findOneBy(array(
                'user' => $user
            ));
        $this->assertNotNull($profile);

        $this->userManager->deleteUser($user);

        $profile = $this->entityManager->getRepository('AppBundle:UserProfile')
            ->findOneBy(array(
                'user' => $user
            ));
        $this->assertNull($profile);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}