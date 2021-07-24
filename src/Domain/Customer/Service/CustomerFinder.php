<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerRepository;

/**
 * Service.
 */
final class CustomerFinder
{
    /**
     * @var CustomerRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param CustomerRepository $repository The repository
     */
    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find customers.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findCustomers(array $params): array
    {
        return $this->repository->findCustomers($params);
    }
}
