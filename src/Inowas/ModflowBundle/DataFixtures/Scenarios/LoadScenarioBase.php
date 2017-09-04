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
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function loadUsers(UserManager $userManager): void
    {

        $userListHeads = array('username', 'name', 'email', 'password');
        $userList = array(
            array('inowas', 'inowas', 'inowas@inowas.com', '#inowas#'),
            array('guest', 'guest', 'guest@inowas.com', '3BJ-w7v-BtP-xes'),
            array('ralf.junghanns', 'Ralf Junghanns', 'ralf.junghanns@tu-dresden.de', '#inowas#'),
            array('jana.glass', 'Jana Glass', 'jana.ringleb@tu-dresden.de', '#inowas#'),
            array('jana.sallwey', 'Jana Sallwey', 'jana.sallwey@tu-dresden.de', '#inowas#'),
            array('catalin.stefan', 'Catalin Stefan', 'catalin.stefan@tu-dresden.de', '#inowas#'),
            array('martin.wudenka', 'Martin Wudenka', 'martin.wudenka@tu-dresden.de', '#inowas#'),
            array('nawapi', 'nawapi', 'nawapi@inowas.com', 'na-ql-ww-pi'),
            array('inowas1', 'inowas1@gast', 'cheekohk'),
            array('malena', 'malena@gast', 'wohchoad'),
            array('jorge', 'jorge@gast', 'geuwaexu'),
            array('ahmad', 'ahmad@gast', 'eipobung'),
            array('jonas', 'jonas@gast', 'eyipuamo'),
            array('guzman', 'guzman@gast', 'xookahbi'),
            array('cristiano', 'cristiano@gast', 'ohfohfum'),
            array('anna', 'anna@gast', 'ahmohnah'),
            array('byambasuren', 'byambasuren@gast', 'aejohque'),
            array('jeanne', 'jeanne@gast', 'xiegooge'),
            array('nguyen', 'nguyen@gast', 'uvupauho'),
            array('meng', 'meng@gast', 'thauzeej'),
            array('xueyan', 'xueyan@gast', 'rohpooqu'),
            array('badia', 'badia@gast', 'caegouno'),
            array('rodrigo', 'rodrigo@gast', 'ahdeogei'),
            array('guillermo', 'guillermo@gast', 'yeuvoolo'),
            array('angel', 'angel@gast', 'aezepees'),
            array('sospeter', 'sospeter@gast', 'uuchahth'),
            array('bhesh', 'bhesh@gast', 'teigotab'),
            array('vahid', 'vahid@gast', 'ophiebah'),
            array('andre', 'andre@gast', 'ohsohshi'),
            array('adeyinka', 'adeyinka@gast', 'aimeipae'),
            array('muna', 'muna@gast', 'iegheith'),
            array('bewuket', 'bewuket@gast', 'hohreith'),
            array('mutaz', 'mutaz@gast', 'chohexai')
        );

        foreach ($userList as $item){
            $item = array_combine($userListHeads, $item);
            $user = $userManager->findUserByUsername($item['username']);
            if (!$user) {

                // Add new User
                $user = $userManager->createUser();
                $user->setUsername($item['username']);
                $user->setName($item['name']);
                $user->setEmail($item['email']);
                $user->setPlainPassword($item['password']);
                $user->setEnabled(true);
                $userManager->updateUser($user);
            }
            $this->userIdList[] = UserId::fromString($user->getId()->toString());
        }

        $owner = $userManager->findUserByUsername('jana.glass');
        $owner->addRole('ROLE_ADMIN');
        $userManager->updateUser($owner);
        $this->ownerId = $owner->getId()->toString();
    }

    protected function loadRowsFromCsv($filename): array {
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
            $data = fgetcsv($handle, 1000, ";");
            fclose($handle);
        }

        return $data;
    }

    protected function getDates(array $header): array{
        $dates = array();
        foreach ($header as $data){
            if (explode(':', $data)[0] === 'date'){
                $dates[] = $data;
            }
        }
        return $dates;
    }
}
