<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserRepository;

/**
 * Service.
 */
final class UserFinder
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param UserRepository $repository The repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find users.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findUsers(array $params): array
    {
        return $this->repository->findUsers($params);
    }
}
