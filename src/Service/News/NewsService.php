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

    public function publish(int $id): ?News
    {
        $news = $this->newsRepo->find($id);

        if ($news !== null) {
            $news->setIsPublished(true);

            $this->newsRepo->save($news, true);
        }

        return $news;
    }

    public function unpublish(int $id): ?News
    {
        $news = $this->newsRepo->find($id);

        if ($news !== null) {
            $news->setIsPublished(false);

            $this->newsRepo->save($news, true);
        }

        return $news;
    }
}
