<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserFactory implements UserFactoryInterface
{
    private UserRepository $userRepo;

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $userRepo, UserPasswordHasherInterface $hasher)
    {
        $this->userRepo = $userRepo;
        $this->hasher = $hasher;
    }

    private function checkIfExists(string $username, string $email)
    {
        $user = $this->userRepo->findOneBy(["username" => $username, "email" => $email]);

        return $user === null ? false : true;
    }

    public function createUser(UserInterface|User $user, array $roles, bool $verified, bool $active): ?User
    {
        if ($this->checkIfExists($user->getUserIdentifier(), $user->getEmail())) {

            return null;
        }

        $user->setRoles($roles);
        $user->setIsActive($active);
        $user->setIsVerified($verified);
        $user->setRegisteredAt(new \DateTimeImmutable("now"));

        $encodedPassword = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        $this->userRepo->save($user, true);

        return $user;
    }
}
