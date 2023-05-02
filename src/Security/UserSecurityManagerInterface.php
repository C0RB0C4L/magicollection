<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserSecurityManagerInterface
{
    public function getRolesAll(): array;

    public function updateRole(UserInterface $user, string $role, bool $save);

    public function updatePassword(UserInterface $user, string $plainPassword);

    public function updateEmail(UserInterface $user, string $email);

    public function activate(UserInterface $user);

    public function deactivate(UserInterface $user);

    public function isGranted(UserInterface $user, $attribute, $object = null);
}
