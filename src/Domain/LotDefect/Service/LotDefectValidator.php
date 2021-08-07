<?php

namespace App\Domain\LotDefect\Service;

use App\Domain\LotDefect\Repository\LotDefectRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class LotDefectValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(LotDefectRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('lot_id', 'Input required')
            ->notEmptyString('defect_id', 'Input required')
            ->notEmptyString('quantity', 'Input required');
    }
<<<<<<< HEAD
=======
    
>>>>>>> tae
    public function validateLotDefect(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

<<<<<<< HEAD
    public function validateLotDefectUpdate(string $lotNo, array $data): void
=======
    public function validateLotDefectUpdate(array $data): void
>>>>>>> tae
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateLotDefect($data);
    }
    public function validateLotDefectInsert( array $data): void
    {
        $this->validateLotDefect($data);
    }
}
