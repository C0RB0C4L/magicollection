<?php

namespace App\Service;

use App\Repository\NewsRepository;

final class HomeDataService implements HomeDataServiceInterface
{
    private NewsRepository $newsRepo;

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    public function getLastNews()
    {
        return $this->newsRepo->findBy(["isPublished" => true], ['createdAt' => "DESC"], 3);
    }
}
