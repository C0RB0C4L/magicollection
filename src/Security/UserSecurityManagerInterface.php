<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserSecurityManagerInterface
{
    public function getRolesAll(): array;

    public function updateRole(UserInterface $user, string $role, bool $save);

    public function updatePassword(UserInterface $user, string $plainPassword);

    public function updateEmail(UserInterface $user, string $email);
}
