<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Security\UserSecurityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserSecurityManager implements UserSecurityManagerInterface
{
    public const BASIC = "ROLE_USER";
    public const TESTER = "ROLE_TESTER";
    public const ADMIN = "ROLE_ADMIN";

    private UserRepository $userRepo;

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $userRepo, UserPasswordHasherInterface $hasher)
    {
        $this->userRepo = $userRepo;
        $this->hasher = $hasher;
    }

    public function getRolesAll(): array
    {
        return [
            self::BASIC,
            self::TESTER,
            self::ADMIN
        ];
    }

    public function updateRole(UserInterface $user, string $role, bool $save = false)
    {
        $currentRoles = $user->getRoles();

        if (!in_array($role, $currentRoles)) {

            $currentRoles[] = $role;

            $user->setRoles($currentRoles);

            if ($save) {
                $this->userRepo->save($user, true);
            }
        }
    }

    public function updatePassword(UserInterface $user, string $plainPassword)
    {
        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);

        $this->userRepo->upgradePassword($user, $hashedPassword);
    }

    public function updateEmail(UserInterface $user, string $email)
    {
        $user->setEmail($email);

        $this->userRepo->save($user, true);
    }
}
