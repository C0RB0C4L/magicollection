<?php

namespace App\Service\News;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\News\NewsServiceInterface;

final class NewsService implements NewsServiceInterface
{
    private NewsRepository $newsRepo;

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    public function publish(News $news): ?News
    {
        $news->setIsPublished(true);

        $this->newsRepo->save($news, true);

        return $news;
    }

    public function unpublish(News $news): ?News
    {
        $news->setIsPublished(false);

        $this->newsRepo->save($news, true);

        return $news;
    }
}
