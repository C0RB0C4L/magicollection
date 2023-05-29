<?php

namespace App\Service\Album;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final class AlbumFactory implements AlbumFactoryInterface
{
    private AlbumRepository $albumRepo;

    public function __construct(AlbumRepository $albumRepo)
    {
        $this->albumRepo = $albumRepo;
    }

    public function createAlbum(Album $album, UserInterface $user): ?Album
    {
        $album->setUser($user);
        $album->setCreatedAt(new \DateTimeImmutable('now'));
        $album->setCapacity(1000);
        $album->setOccupiedSlots(0);
        $album->setEntries(0);
        $album->setOccupancyRate(0);

        $this->albumRepo->save($album, true);

        return $album;
    }
}
