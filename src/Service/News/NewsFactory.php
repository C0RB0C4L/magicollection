<?php

namespace App\Service\News;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\News\NewsFactoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class NewsFactory implements NewsFactoryInterface
{
    private NewsRepository $newsRepo;

    private SluggerInterface $slugger;

    public function __construct(NewsRepository $newsRepo, SluggerInterface $slugger)
    {
        $this->newsRepo = $newsRepo;
        $this->slugger = $slugger;
    }

    public function create(News $news, bool $publish = false): News
    {
        $news->setCreatedAt(new \DateTimeImmutable("now"));

        $slugTitle = $this->slugger->slug($news->getTitle());
        $news->setSlug($slugTitle);

        if ($publish) {
            $news->setIsPublished($publish);
            $news->setPublishedOn(new \DateTimeImmutable("now"));
        }

        $this->newsRepo->save($news, true);

        return $news;
    }

    public function edit(News $news): News
    {
        $news->setLastModifiedAt(new \DateTimeImmutable("now"));

        $this->newsRepo->save($news, true);

        return $news;
    }
}
