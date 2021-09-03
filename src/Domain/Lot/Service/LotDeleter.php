<?php

namespace App\Domain\Lot\Service;

use App\Domain\Lot\Repository\LotRepository;

/**
 * Service.
 */
final class LotDeleter
{
    /**
     * @var LotRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param LotRepository $repository The repository
     */
    public function __construct(LotRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Delete lot.
     *
     * @param int $lotId The lot id
     *
     * @return void
     */
    // public function deleteLot(int $lotId): void
    // {
    //     // Input validation
    //     // ...
    //     $this->repository->deleteLotById($lotId);
    // }
}
