<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserRoleManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFactory implements UserFactoryInterface
{
    private UserRepository $userRepo;

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $userRepo, UserPasswordHasherInterface $hasher)
    {
        $this->userRepo = $userRepo;
        $this->hasher = $hasher;
    }

    public function createSimpleUser(string $username, string $email, string $plainPassword): ?User
    {
        if ($this->checkIfExists($username, $email)) {

            return null;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([UserRoleManager::BASIC]);
        $user->setIsActive(true);
        $user->setIsVerified(true);

        $encodedPassword = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $this->userRepo->save($user, true);

        return $user;
    }

    private function checkIfExists(string $username, string $email)
    {
        $user = $this->userRepo->findExistingUsers($username, $email);

        return $user === [] ? false : true;
    }
}
