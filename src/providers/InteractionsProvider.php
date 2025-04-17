<?php

declare(strict_types=1);

namespace Src\Providers;

use Src\Entities\Blog;
use Doctrine\ORM\EntityManager;
use Src\Entities\Interactions;
use Src\Enums\Interactions as EnumsInteractions;
use Src\Entities\User;

class InteractionsProvider
{
    public function __construct(
        private readonly EntityManager $entityManager
    ) {}

    public function getByBlog(Blog $blog): ?Interactions
    {
        return $this->entityManager->getRepository(Interactions::class)->findOneBy(['blog' => $blog->getId()]);
    }

    public function createTickInteraction(Blog $blog, User $user): Interactions
    {
        $interaction = new Interactions();

        $interaction->setBlog($blog)
            ->setUser($user)
            
            ->setInteraction(EnumsInteractions::Tick);

        $this->sync($interaction);

        return $interaction;
    }

    public function deleteInteraction(Interactions $interactions)
    {
        $this->entityManager->remove($interactions);

        $this->entityManager->flush();
    }

    public function sync(Interactions $interactions)
    {
        $this->entityManager->persist($interactions);

        $this->entityManager->flush();
    }
}
