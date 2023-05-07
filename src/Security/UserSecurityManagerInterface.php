<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserSecurityManagerInterface
{
    public static function getRolesAll(): array;

    public function updateRole(UserInterface $user, string $role, bool $save);

    public function updatePassword(UserInterface $user, string $plainPassword);

    public function updateEmail(UserInterface $user, string $email);

    public function activate(UserInterface $user);

    public function deactivate(UserInterface $user);

    public function verify(UserInterface $user, int $accountCreationId);

    public function isGranted(UserInterface $user, $attribute, $object = null);

    public function preventSelfHarm(UserInterface $target, UserInterface $current);
    
    public function protectMaster(UserInterface $target);

    public function protectBrothers(UserInterface $target, UserInterface $current);

    public function protectBrothersAndMaster(UserInterface $target, UserInterface $current);

    public function generatePassword(): string;
}
