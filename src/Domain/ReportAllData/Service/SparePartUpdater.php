<?php

namespace App\Domain\SparePart\Service;

use App\Domain\SparePart\Repository\SparePartRepository;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Service.
 */
final class SparePartUpdater
{
    /**
     * @var SparePartRepository
     */
    private $repository;

    /**
     * @var SparePartValidator
     */
    private $sparePartValidator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param SparePartRepository $repository The repository
     * @param SparePartValidator $sparePartValidator The validator
     * @param LoggerFactory $loggerFactory The logger factory
     */
    public function __construct(
        SparePartRepository $repository,
        SparePartValidator $sparePartValidator,
        LoggerFactory $loggerFactory
    ) {
        $this->repository = $repository;
        $this->sparePartValidator = $sparePartValidator;
        $this->logger = $loggerFactory
            ->addFileHandler('spare_part_updater.log')
            ->createInstance();
    }

    /**
     * Disable spare_part.
     *
     * @param int $sparePartId The spare_part id
     * @param array<mixed> $data The request data
     *
     * @return void
     */
    public function disableSparePart(int $sparePartId): void
    {

        // Insert spare_part
        $this->repository->disableSparePart($sparePartId);

        // Logging
        $this->logger->info(sprintf('SparePart disabled successfully: %s', $sparePartId));
    }
    
    /**
     * Update spare_part.
     *
     * @param int $sparePartId The spare_part id
     * @param array<mixed> $data The request data
     *
     * @return void
     */
    public function updateSparePart(int $sparePartId, array $data): void
    {
        // Input validation
        $this->sparePartValidator->validateSparePartUpdate($sparePartId, $data);

        // Map form data to row
        $sparePartRow = $this->mapToSparePartRow($data);

        // Insert spare_part
        $this->repository->updateSparePart($sparePartId, $sparePartRow);

        // Logging
        $this->logger->info(sprintf('SparePart updated successfully: %s', $sparePartId));
    }

    public function updateSparePartQty(int $sparePartId, array $data): void
    {
        // Input validation
        $this->sparePartValidator->validateSparePartUpdate($sparePartId, $data);

        // Map form data to row
        $sparePartRow = $this->mapToSparePartRow($data);

        // Insert spare_part
        $this->repository->updateSparePartQty($sparePartId, $sparePartRow);

        // Logging
        $this->logger->info(sprintf('SparePart updated successfully: %s', $sparePartId));
    }

    
    public function transferAy2KkSparePartQty(int $sparePartId, array $data): void
    {

        // Insert spare_part
        $this->repository->transferAy2KkSparePartQty($sparePartId, $data);

        // Logging
        $this->logger->info(sprintf('SparePart updated successfully: %s', $sparePartId));
    }
    
    public function transferKk2AySparePartQty(int $sparePartId, array $data): void
    {

        // Insert spare_part
        $this->repository->transferKk2AySparePartQty($sparePartId, $data);

        // Logging
        $this->logger->info(sprintf('SparePart updated successfully: %s', $sparePartId));
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToSparePartRow(array $data): array
    {
        $result = [];

        if (isset($data['spare_part_name'])) {
            $result['spare_part_name'] = (string)$data['spare_part_name'];
        }

        if (isset($data['spare_part_code'])) {
            $result['spare_part_code'] = (string)$data['spare_part_code'];
        }

        if (isset($data['category_id'])) {
            $result['category_id'] = (string)$data['category_id'];
        }

        if (isset($data['last_price'])) {
            $result['last_price'] = (string)$data['last_price'];
        }

        if (isset($data['image_url'])) {
            $result['image_url'] = (string)$data['image_url'];
        }

        if (isset($data['is_delete'])) {
            $result['is_delete'] = (string)$data['is_delete'];
        }

        if (isset($data['ay_balance_qty'])) {
            $result['ay_balance_qty'] = (string)$data['ay_balance_qty'];
        }

        if (isset($data['kk_balance_qty'])) {
            $result['kk_balance_qty'] = (string)$data['kk_balance_qty'];
        }

        return $result;
    }
    public function updatePrice(string $sparePartCode, array $data): void
    {

        // Insert spare_part
        $this->repository->updatePrice($sparePartCode, $data);

        // Logging
        $this->logger->info(sprintf('SparePart updated successfully: %s', $sparePartCode));
    }
}
