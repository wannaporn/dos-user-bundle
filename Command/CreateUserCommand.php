<?php

namespace DoS\UserBundle\Command;

use Sylius\Bundle\UserBundle\Command\CreateUserCommand as BaseCreateUserCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends BaseCreateUserCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dos:user:create')
            ->setDescription('Creates a new user account.')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
                new InputArgument('password', InputArgument::REQUIRED, 'Password'),
                new InputArgument('roles', InputArgument::IS_ARRAY, 'RBAC roles'),
                new InputOption('disabled', null, InputOption::VALUE_NONE, 'Set the user as a disabled user'),
            ))
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new user account.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');
        $disabled = $input->getOption('disabled');

        $securityRoles = ['ROLE_USER'];

        if (!empty($roles)) {
            $securityRoles = array_merge($securityRoles, $roles);
        }

        $user = $this->createUser(
            $email,
            $password,
            !$disabled,
            array(),
            $securityRoles
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
    }
}
