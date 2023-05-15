<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserSecurityManager;
use App\Service\User\UserFactoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:create',
    description: "Creates a User account. \r\n  The desired password will be asked if the user doesn't already exist."
)]
class UserCreateCommand extends Command
{
    private const ARG_USERNAME = "username";
    private const ARG_EMAIL = "email";

    private const DEFAULT_PASSWORD = "password";

    private UserRepository $userRepo;
    private UserFactoryInterface $userFactory;

    public function __construct(UserRepository $userRepo, UserFactoryInterface $userFactory)
    {
        $this->userRepo = $userRepo;
        $this->userFactory = $userFactory;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_USERNAME, InputArgument::REQUIRED, 'Username used to log in.')
            ->addArgument(self::ARG_EMAIL, InputArgument::REQUIRED, 'Email address for password recovery.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument(self::ARG_USERNAME);
        $email = $input->getArgument(self::ARG_EMAIL);

        $existingUsers = $this->userRepo->findBy(["username" => $username, "email" => $email]);

        if ($existingUsers !== []) {

            $io->error("A user already has this identifier: $username and/or this email address: $email.");

            return Command::FAILURE;
        }

        $helper = $this->getHelper("question");
        $question = new Question(
            "\r\n <info>Please enter a password for this user</info> "
                . "(<comment>leaving blank will result in 'password' as the default password</comment>)"
                . "\r\n This password characteristics override any constraint or rules "
                . "\r\n >",
            self::DEFAULT_PASSWORD
        );

        $plainPassword = $helper->ask($input, $output, $question);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($plainPassword);

        $this->userFactory->createUser($user, [UserSecurityManager::BASIC],  true, true);

        $io->success("User created: $username - $email");

        return Command::SUCCESS;
    }
}
