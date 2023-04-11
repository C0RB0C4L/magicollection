<?php

namespace App\Security;

use App\Entity\User;

interface UserRoleManagerInterface
{
    public function getRolesAll(): array;

    public function updateRole(User $user, string $role, bool $save);
}
