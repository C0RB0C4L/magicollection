<?php

namespace App\Service\User;

use App\Entity\AccountCreationRequest;
use App\Entity\User;
use App\Repository\AccountCreationRequestRepository;
use App\Repository\UserRepository;
use App\Security\UserSecurityManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserFactory implements UserFactoryInterface
{
    private UserRepository $userRepo;

    private UserPasswordHasherInterface $hasher;

    private AccountCreationRequestRepository $accountCreationRepo;

    public function __construct(UserRepository $userRepo, UserPasswordHasherInterface $hasher, AccountCreationRequestRepository $accountCreationRepo)
    {
        $this->userRepo = $userRepo;
        $this->hasher = $hasher;
        $this->accountCreationRepo = $accountCreationRepo;
    }

    public function createSimpleUser(string $username, string $email, string $plainPassword, bool $verified, bool $active): ?User
    {
        if ($this->checkIfExists($username, $email)) {

            return null;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([UserSecurityManager::BASIC]);
        $user->setIsActive($active);
        $user->setIsVerified($verified);
        $user->setRegisteredAt(new \DateTimeImmutable("now"));

        $encodedPassword = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $this->userRepo->save($user, true);

        return $user;
    }

    public function createMasterUser(string $username, string $email, string $plainPassword, bool $verified, bool $active): ?User
    {
        if ($this->checkIfExists($username, $email)) {

            return null;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([UserSecurityManager::MASTER]);
        $user->setIsActive($active);
        $user->setIsVerified($verified);
        $user->setRegisteredAt(new \DateTimeImmutable("now"));

        $encodedPassword = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $this->userRepo->save($user, true);

        return $user;
    }

    private function checkIfExists(string $username, string $email)
    {
        $user = $this->userRepo->findExistingUsers($username, $email);

        return $user === [] ? false : true;
    }

    public function accountCreationRequest(UserInterface|User $user)
    {
        $username = $user->getUserIdentifier();
        $rand = random_int(100000, 999999);

        $token = bin2hex(random_bytes(36));
        $selector = md5($username . $rand);

        $accountCreationRequest = new AccountCreationRequest();
        $accountCreationRequest->setToken($token);
        $accountCreationRequest->setSelector($selector);
        $accountCreationRequest->setCreatedAt(new \DateTimeImmutable("now"));
        $accountCreationRequest->setUser($user);

        $this->accountCreationRepo->save($accountCreationRequest, true);
    }
}
