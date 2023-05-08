<?php

namespace App\Service\News;

use App\Entity\News;

interface NewsFactoryInterface
{
    public function create(News $news, bool $publish): News;

    public function edit(News $news): News;
}
