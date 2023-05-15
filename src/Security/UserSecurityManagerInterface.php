<?php

namespace App\Security;

use App\Entity\AccountCreationRequest;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserSecurityManagerInterface
{
    public static function getRolesAll(): array;

    public function updateRoles(UserInterface $user, string $role, bool $save);

    public function activate(UserInterface $user);

    public function deactivate(UserInterface $user);

    public function verify(AccountCreationRequest $accountCreationRequest);

    public function updateEmail(UserInterface $user, string $email);
    
    public function updatePassword(UserInterface $user, string $plainPassword);

    public function regeneratePassword(UserInterface $user): string;

    public function delete(UserInterface $user);

    public function isGranted(UserInterface $user, $attribute, $object = null): bool;

    public function isMaster(UserInterface $user): bool;

    public function isAdmin(UserInterface $user): bool;
    
    public function protectSelf(UserInterface $target, UserInterface $current): bool;

    public function protectSelfAndMaster(UserInterface $target, UserInterface $current): bool;

    public function protectAll(UserInterface $target, UserInterface $current): bool;
}
