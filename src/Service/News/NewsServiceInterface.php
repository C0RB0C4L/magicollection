<?php

namespace App\Service\News;

use App\Entity\News;

interface NewsServiceInterface
{
    public function publish(int $id): ?News;

    public function unpublish(int $id): ?News;
}
