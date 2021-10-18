<?php

<<<<<<< HEAD:src/Domain/LabelVoidReason/Service/LabelVoidReasonValidator.php
namespace App\Domain\LabelVoidReason\Service;

use App\Domain\LabelVoidReason\Repository\LabelVoidReasonRepository;
=======
namespace App\Domain\SellLabel\Service;

use App\Domain\SellLabel\Repository\SellLabelRepository;
>>>>>>> racha05:src/Domain/SellLabel/Service/SellLabelValidator.php
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

<<<<<<< HEAD:src/Domain/LabelVoidReason/Service/LabelVoidReasonValidator.php
final class LabelVoidReasonValidator
=======
final class SellLabelValidator
>>>>>>> racha05:src/Domain/SellLabel/Service/SellLabelValidator.php
{
    private $repository;
    private $validationFactory;

<<<<<<< HEAD:src/Domain/LabelVoidReason/Service/LabelVoidReasonValidator.php
    public function __construct(LabelVoidReasonRepository $repository, ValidationFactory $validationFactory)
=======
    public function __construct(SellLabelRepository $repository, ValidationFactory $validationFactory)
>>>>>>> racha05:src/Domain/SellLabel/Service/SellLabelValidator.php
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
<<<<<<< HEAD:src/Domain/LabelVoidReason/Service/LabelVoidReasonValidator.php
            ->notEmptyString('reason_name', 'Input required')
            ->notEmptyString('description', 'Input required');
    }
    public function validateLabelVoidReason(array $data): void
=======
            ->notEmptyString('sell_id', 'Input required')
            ->notEmptyString('label_id', 'Input required');
    }
    public function validateSellLabel(array $data): void
>>>>>>> racha05:src/Domain/SellLabel/Service/SellLabelValidator.php
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

<<<<<<< HEAD:src/Domain/LabelVoidReason/Service/LabelVoidReasonValidator.php
    public function validateLabelVoidReasonUpdate(string $lotNo, array $data): void
=======
    public function validateSellLabelUpdate(string $part_code, array $data): void
>>>>>>> racha05:src/Domain/SellLabel/Service/SellLabelValidator.php
    {
        /*
        if (!$this->repository->existsSellLabelNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
<<<<<<< HEAD:src/Domain/LabelVoidReason/Service/LabelVoidReasonValidator.php
        $this->validateLabelVoidReason($data);
    }
    public function validateLabelVoidReasonInsert( array $data): void
    {
        $this->validateLabelVoidReason($data);
=======
        $this->validateSellLabel($data);
    }
    public function validateSellLabelInsert( array $data): void
    {
        $this->validateSellLabel($data);
>>>>>>> racha05:src/Domain/SellLabel/Service/SellLabelValidator.php
    }
}
