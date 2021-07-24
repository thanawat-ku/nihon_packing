<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserRepository;

/**
 * Service.
 */
final class UserLoginChecker
{
    /**
     * @var UserReaderRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param UserReaderRepository $repository The repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Read a user.
     *
     * @param int $userId The user id
     *
     * @return array The user data
     */
    public function checkLogin(string $username,string $password)
    {
        // Input validation
        // ...

        // Fetch data from the database
        $userRow = $this->repository->checkLogin($username,$password);

        // Optional: Add or invoke your complex business logic here
        // ...

        // Optional: Map result
        // ...

        return $userRow;
    }
}
