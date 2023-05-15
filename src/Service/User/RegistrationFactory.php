<?php

namespace App\Service\User;

use App\Entity\AccountCreationRequest;
use App\Entity\User;
use App\Repository\AccountCreationRequestRepository;
use App\Security\UserSecurityManager;
use Symfony\Component\Security\Core\User\UserInterface;

final class RegistrationFactory implements RegistrationFactoryInterface
{
    private AccountCreationRequestRepository $accountCreationRepo;

    private UserFactoryInterface $userFactory;

    public function __construct(UserFactoryInterface $userFactory, AccountCreationRequestRepository $accountCreationRepo)
    {
        $this->userFactory = $userFactory;
        $this->accountCreationRepo = $accountCreationRepo;
    }

    public function createAccountRequest(UserInterface|User $user): ?AccountCreationRequest
    {
        $accountRequest = null;

        // creates an unverified and inactive user account first
        $user = $this->userFactory->createUser($user, [UserSecurityManager::BASIC], false, false);

        if ($user !== null) {

            $accountRequest = new AccountCreationRequest();
            $accountRequest->setUser($user);
            $accountRequest->setCreatedAt(new \DateTimeImmutable("now"));

            $rand = random_int(100000, 999999);
            $token = bin2hex(random_bytes(36));
            $selector = md5($user->getUserIdentifier() . $rand);

            $accountRequest->setToken($token);
            $accountRequest->setSelector($selector);

            $this->accountCreationRepo->save($accountRequest, true);
        }

        return $accountRequest;
    }
}
