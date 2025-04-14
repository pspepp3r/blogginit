<?php

declare(strict_types=1);

namespace Src\Services;

use Doctrine\ORM\EntityManager;
use Src\Data_objects\UpdateUserData;
use Src\Entities\User;
use Src\Providers\UserProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SettingsService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly HashService $hashService,
        private readonly SessionInterface $sessionInterface,
        private readonly UserProvider $userProvider
    ) {}
    public function updateProfile(UpdateUserData $data): void
    {
        /**
         * @var int
         */
        $userId = $this->sessionInterface->get('user');

        /**
         * @var User
         */
        $user = $this->userProvider->getById($userId);

        if ($data->name) {
            $user->setName($data->name);
        }

        if ($data->gender) {
            $user->setGender($data->gender);
        }

        if ($data->introduction) {
            $user->setIntroduction($data->introduction);
        }

        if ($data->country) {
            $user->setLocation($data->country);
        }

        if ($data->occupation) {
            $user->setOccupation($data->occupation);
        }

        // if ($data->profile_picture) {
        //     $user->setPicture($data->profile_picture);
        // }

        $this->sessionInterface->set('userEntity', $user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updateSecurity(?string $password, ?string $is2fa): void
    {
        /**
         * @var int
         */
        $userId = $this->sessionInterface->get('user');

        $user = $this->userProvider->getById($userId);

        if($password){
            $user->setPassword($this->hashService->hashPassword($password)); }

        if($is2fa){
            $user->setTwoFactor(true);
        } else {
            $user->setTwoFactor(false);
        }

        $this->sessionInterface->set('userEntity', $user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
