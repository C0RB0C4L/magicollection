<?php

namespace App\Security;

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
            self::ADMIN,
            self::TESTER,
            self::BASIC
        ];
    }


    public function updateRole(UserInterface|User $user, string $role, bool $save = false)
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


    public function updatePassword(UserInterface|User $user, string $plainPassword)
    {
        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);

        $this->userRepo->upgradePassword($user, $hashedPassword);
    }


    public function updateEmail(UserInterface|User $user, string $email)
    {
        $user->setEmail($email);

        $this->userRepo->save($user, true);
    }


    public function activate(UserInterface|User $user)
    {
        //if (!$this->isGranted($user, self::ADMIN)) {

        $user->setIsActive(true);

        $this->userRepo->save($user, true);
        //}
    }


    public function deactivate(UserInterface|User $user)
    {
        //if (!$this->isGranted($user, self::ADMIN)) {

        $user->setIsActive(false);

        $this->userRepo->save($user, true);
        //}
    }


    public function verify(UserInterface|User $user, int $accountCreationId)
    {
        $user->setIsVerified(true);

        $request = $this->accountCreationRepo->find($accountCreationId);
        if ($request && $request->getConfirmedAt() === null) {

            $request->setConfirmedAt(new \DateTimeImmutable("now"));
            $this->accountCreationRepo->save($request);

            $user->setIsActive(true);
        }

        $this->userRepo->save($user, true);
    }


    /**
     * Similar to isGranted() from controllers\
     * But allows to use a different `$user` from the current user
     */
    public function isGranted(UserInterface $user, $attribute, $object = null)
    {
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());

        return ($this->accessDecisionManager->decide($token, [$attribute], $object));
    }


    /**
     * @return bool __FALSE__ if the `$target` and the `$current` are not the same\
     * __TRUE__ if the user SHOULD NOT affect itself
     */
    public function preventSelfHarm(UserInterface|User $target, UserInterface|User $current)
    {
        return ($target->getId() === $current->getId());
    }


    /**
     * @return bool __FALSE__ if the target is not the master and could be affected\
     * __TRUE__ if the target is the master and SHOULD NOT be affected
     */
    public function protectMaster(UserInterface|User $target)
    {
        return in_array(self::MASTER, $target->getRoles());
    }


    /**
     * @return bool __FALSE__ if the `$target` and the `$current` are not brothers\
     * __TRUE__ if they are and SHOULD NOT affect eachothers
     */
    public function protectBrothers(UserInterface|User $target, UserInterface|User $current)
    {
        return ($this->isGranted($target, self::ADMIN) && $this->isGranted($current, self::ADMIN));
    }


    /**
     * @return bool __FALSE__ if the `$current` could affect the `$target`\
     * __TRUE__ if the `$target` SHOULD NOT be affected
     */
    public function protectBrothersAndMaster(UserInterface|User $target, UserInterface|User $current)
    {
        if ($this->protectMaster($target)) {
            return true;
        }
        if ($this->protectBrothers($target, $current)) {
            return true;
        }

        return false;
    }


    /**
     * @return string $newPassword the plain password (to be sent via email, for example)
     */
    public function generatePassword(): string
    {
        return $this->generateRandomPassword(24, true);
    }
}
