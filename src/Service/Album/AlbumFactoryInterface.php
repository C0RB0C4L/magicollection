<?php

namespace App\Service\Album;

use App\Entity\Album;
use Symfony\Component\Security\Core\User\UserInterface;

interface AlbumFactoryInterface
{
    public function createAlbum(Album $album, UserInterface $user): ?Album;
}
