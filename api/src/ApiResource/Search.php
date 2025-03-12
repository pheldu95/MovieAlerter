<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\SearchController;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/search'
        )
    ],
//    outputFormats: ['json'],
    controller: SearchController::class,
)]

class Search implements SearchInterface
{
    public string $q;
}