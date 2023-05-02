<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Security\UserSecurityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

final class UserSecurityManager implements UserSecurityManagerInterface
{
    public const BASIC = "ROLE_USER";
    public const TESTER = "ROLE_TESTER";
    public const ADMIN = "ROLE_ADMIN";

    private UserRepository $userRepo;

    private UserPasswordHasherInterface $hasher;

    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(UserRepository $userRepo, UserPasswordHasherInterface $hasher, AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->userRepo = $userRepo;
        $this->hasher = $hasher;
        $this->accessDecisionManager = $accessDecisionManager;
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

    public function activate(UserInterface $user)
    {
        //if (!$this->isGranted($user, self::ADMIN)) {

            $user->setIsActive(true);

            $this->userRepo->save($user, true);
        //}
    }

    public function deactivate(UserInterface $user)
    {
        //if (!$this->isGranted($user, self::ADMIN)) {

            $user->setIsActive(false);

            $this->userRepo->save($user, true);
        //}
    }

    // allows to use te function even if $user is not the current user
    public function isGranted(UserInterface $user, $attribute, $object = null)
    {
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());

        return ($this->accessDecisionManager->decide($token, [$attribute], $object));
    }
}
