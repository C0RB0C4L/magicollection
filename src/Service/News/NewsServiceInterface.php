<?php

namespace App\Service\News;

use App\Entity\News;

interface NewsServiceInterface
{
    public function publish(News $news): ?News;

    public function unpublish(News $news): ?News;
}
