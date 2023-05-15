<?php

namespace App\Service\News;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\News\NewsFactoryInterface;

final class NewsFactory implements NewsFactoryInterface
{
    private NewsRepository $newsRepo;

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    public function create(News $news, bool $publish = false): News
    {
        $news->setCreatedAt(new \DateTimeImmutable("now"));
        $news->setIsPublished($publish);

        $this->newsRepo->save($news, true);

        return $news;
    }

    public function edit(News $news): News
    {
        $news->setLastModifiedAt(new \DateTimeImmutable("now"));

        $this->newsRepo->save($news, true);

        return $news;
    }

    public function delete(News $news): void
    {
        $this->newsRepo->remove($news, true);
    }
}
