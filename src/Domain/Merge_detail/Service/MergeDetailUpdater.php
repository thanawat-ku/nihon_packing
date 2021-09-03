<?php // dont finish!!!!!!

namespace App\Domain\Merge_detail\Service;

use App\Domain\Merge_detail\Repository\MergeDetailRepository;

/**
 * Service.
 */
final class MergeDetailUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        MergeDetailRepository $repository,
        MergeDetailValidator $validator,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertMerge( array $data): int  //focus that!!!!!
    {
        // Input validation
        $this->validator->validateMergeInsert($data);

        // Map form data to row
        $mergeRow = $this->mapToMergeRow($data);  

        // Insert transferStore
        $id=$this->repository->insertMerge($mergeRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateMerge(int $mergeId, array $data): void
    {
        // Input validation
        $this->validator->validateMergeUpdate($mergeId, $data);

        // Map form data to row
        $storeRow = $this->mapToMergeRow($data);

        // Insert store
        $this->repository->updateMerge($mergeId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToMergeRow(array $data): array
    {
        $result = [];

        if (isset($data['label_id'])) {
            $result['label_id'] = (string)$data['label_id'];
        }
        if (isset($data['merge_packs_id'])) {
            $result['merge_packs_id'] = (string)$data['merge_packs_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }


        return $result;
    }
}
