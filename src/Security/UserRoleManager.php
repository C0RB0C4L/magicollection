<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserRoleManagerInterface;

final class UserRoleManager implements UserRoleManagerInterface
{
    public const BASIC = "ROLE_USER";
    public const TESTER = "ROLE_TESTER";
    public const ADMIN = "ROLE_ADMIN";

    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getRolesAll(): array
    {
        return [
            self::BASIC,
            self::TESTER,
            self::ADMIN
        ];
    }

    public function updateRole(User $user, string $role, bool $save = false)
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
}
