<?php  //dont finish!!!!

namespace App\Domain\MergeDetail\Service;

use App\Domain\MergeDetail\Repository\MergeDetailRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class MergeDetailValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(MergeDetailRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('label_id', 'Input required')
            ->notEmptyString('merge_packs_id', 'Input required')
            ->notEmptyString('quantity', 'Input required');
    }
    public function validateMerge(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateMergeUpdate(string $mergeNo, array $data): void  //focus that!!!!!!!!!!
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateMerge($data);
    }
    public function validateMergeInsert( array $data): void
    {
        $this->validateMerge($data);
    }
}
