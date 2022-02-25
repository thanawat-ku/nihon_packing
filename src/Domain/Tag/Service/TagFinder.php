<?php

namespace App\Domain\Tag\Service;

use App\Domain\Tag\Repository\TagRepository;

/**
 * Service.
 */
final class TagFinder
{
    private $repository;
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findTags(array $params): array
    {
        return $this->repository->findTags($params);
    }

    public function findTagSingleTable(array $params): array
    {
        return $this->repository->findTagSingleTable($params);
    }
}
