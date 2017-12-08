<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\UserId;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InowasLoadUsersCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:users:load')
            ->setDescription('Loads the users from users.json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $usersFile = __DIR__.'/../../../../users.json';
        $users = json_decode(file_get_contents($usersFile), true);

        $heads = $users['heads'];
        $users = $users['users'];

        /**
         * @var array $users
         */
        foreach ($users as $item){
            $item = array_combine($heads, $item);
            $user = $userManager->findUserByUsername($item['username']);

            if (!$user instanceof User) {
                $user = $userManager->createUser();
                $output->write('Create ');
            } else {
                $output->write('Update ');
            }

            $user->setUsername($item['username']);
            $user->setName($item['name']);
            $user->setEmail($item['email']);
            $user->setPlainPassword($item['password']);
            $user->setEnabled(true);
            $user->setRoles(['ROLE_USER']);

            /** @var string $role */
            foreach ($item['roles'] as $role) {
                $user->addRole($role);
            }

            $userManager->updateUser($user);
            $output->writeln('user '.$user->getUsername().' with roles ['.implode(', ', $user->getRoles()).']');
        }
    }
}
