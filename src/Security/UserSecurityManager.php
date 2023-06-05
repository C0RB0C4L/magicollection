<?php

namespace App\Security;

use App\Entity\AccountCreationRequest;
use App\Entity\User;
use App\Helper\StringGeneratorTrait;
use App\Repository\AccountCreationRequestRepository;
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
    public const MASTER = "ROLE_MASTER";

    private UserRepository $userRepo;

    private UserPasswordHasherInterface $hasher;

    private AccessDecisionManagerInterface $accessDecisionManager;

    private AccountCreationRequestRepository $accountCreationRepo;

    use StringGeneratorTrait;

    public function __construct(
        UserRepository $userRepo,
        UserPasswordHasherInterface $hasher,
        AccessDecisionManagerInterface $accessDecisionManager,
        AccountCreationRequestRepository $accountCreationRepo
    ) {
        $this->userRepo = $userRepo;
        $this->hasher = $hasher;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->accountCreationRepo = $accountCreationRepo;
    }


    public static function getRolesAll(): array
    {
        return [
            self::MASTER,
            self::ADMIN,
            self::TESTER,
            self::BASIC
        ];
    }


    public function updateRoles(UserInterface|User $user, array $roles, bool $isMaster = false, bool $save = false): void
    {
        if ($isMaster) {
            array_unshift($roles, self::MASTER);
        }
        
        $user->setRoles(array_values(array_unique($roles)));

        if ($save) {
            $this->userRepo->save($user, true);
        }
    }


    public function activate(UserInterface|User $user): void
    {
        $user->setIsActive(true);

        $this->userRepo->save($user, true);
    }


    public function deactivate(UserInterface|User $user): void
    {
        $user->setIsActive(false);

        $this->userRepo->save($user, true);
    }


    public function verify(AccountCreationRequest $accountCreationRequest): void
    {
        if ($accountCreationRequest->getConfirmedAt() === null) {

            $accountCreationRequest->setConfirmedAt(new \DateTimeImmutable("now"));
            $accountCreationRequest->getUser()->setIsVerified(true);

            $this->accountCreationRepo->save($accountCreationRequest, true);
        }
    }

    public function updateEmail(UserInterface|User $user, string $email)
    {
        $user->setEmail($email);

        $this->userRepo->save($user, true);
    }


    public function updatePassword(UserInterface|User $user, string $plainPassword)
    {
        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);

        $this->userRepo->upgradePassword($user, $hashedPassword);

        return $plainPassword;
    }


    public function regeneratePassword(UserInterface|User $user): string
    {
        $plainPassword = $this->generateRandomPassword(20, true);
        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);

        $this->userRepo->upgradePassword($user, $hashedPassword);

        return $plainPassword;
    }


    public function delete(UserInterface|User $user): void
    {
        $this->userRepo->remove($user, true);
    }


    /**
     * Similar to isGranted() from controllers\
     * But allows to use a different `$user` than the current user
     */
    public function isGranted(UserInterface $user, $attribute, $object = null): bool
    {
        $token = new UsernamePasswordToken($user, 'none', $user->getRoles());

        return ($this->accessDecisionManager->decide($token, [$attribute], $object));
    }


    public function isMaster(UserInterface $user): bool
    {
        return $this->isGranted($user, self::MASTER);
    }


    public function isAdmin(UserInterface $user): bool
    {
        return $this->isGranted($user, self::ADMIN);
    }


    /**
     * @return bool __FALSE__ if the `$target` cannot be affected by the `$current` user\
     * __TRUE__ otherwise
     */
    public function protectSelf(UserInterface|User $target, UserInterface|User $current): bool
    {
        return ($target->getId() === $current->getId());
    }


    /**
     * @return bool __FALSE__ if the `$target` cannot be affected by the `$current` user\
     * __TRUE__ otherwise
     */
    public function protectSelfAndMaster(UserInterface|User $target, UserInterface|User $current): bool
    {
        if ($this->isMaster($current)) {

            return false;
        }
        if ($this->isMaster($target)) {

            return true;
        }
        if (($target->getId() === $current->getId())) {
            return true;
        }
        return false;
    }


    /**
     * @return bool __FALSE__ if the `$target` cannot be affected by the `$current` user\
     * __TRUE__ otherwise
     */
    public function protectAll(UserInterface|User $target, UserInterface|User $current): bool
    {
        if ($this->isMaster($target) && !$this->isMaster($current)) {

            return true;
        }
        if ($this->isAdmin($target) && !$this->isMaster($current)) {

            return true;
        }
        if (($target->getId() === $current->getId())) {
            return true;
        }
        return false;
    }
}
