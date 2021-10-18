<?php

<<<<<<< HEAD:src/Domain/SplitLabelDetail/Service/SplitLabelDetailValidator.php
namespace App\Domain\SplitLabelDetail\Service;

use App\Domain\SplitLabelDetail\Repository\SplitLabelDetailRepository;
=======
namespace App\Domain\SellCpoItem\Service;

use App\Domain\SellCpoItem\Repository\SellCpoItemRepository;
>>>>>>> racha05:src/Domain/SellCpoItem/Service/SellCpoItemValidator.php
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

<<<<<<< HEAD:src/Domain/SplitLabelDetail/Service/SplitLabelDetailValidator.php
final class SplitLabelDetailValidator
=======
final class SellCpoItemValidator
>>>>>>> racha05:src/Domain/SellCpoItem/Service/SellCpoItemValidator.php
{
    private $repository;
    private $validationFactory;

<<<<<<< HEAD:src/Domain/SplitLabelDetail/Service/SplitLabelDetailValidator.php
    public function __construct(SplitLabelDetailRepository $repository, ValidationFactory $validationFactory)
=======
    public function __construct(SellCpoItemRepository $repository, ValidationFactory $validationFactory)
>>>>>>> racha05:src/Domain/SellCpoItem/Service/SellCpoItemValidator.php
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
<<<<<<< HEAD:src/Domain/SplitLabelDetail/Service/SplitLabelDetailValidator.php
            ->notEmptyString('split_label_no', 'Input required')
            ->notEmptyString('label_id', 'Input required')
            ->notEmptyString('status', 'Input required');
    }
    public function validateSplitLabelDetail(array $data): void
=======
            ->notEmptyString('sell_id', 'Input required')
            ->notEmptyString('cpo_item_id', 'Input required')
            ->notEmptyString('remain_qty', 'Input required')
            ->notEmptyString('sell_qty', 'Input required');
           
    }
    public function validateSellCpoItem(array $data): void
>>>>>>> racha05:src/Domain/SellCpoItem/Service/SellCpoItemValidator.php
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

<<<<<<< HEAD:src/Domain/SplitLabelDetail/Service/SplitLabelDetailValidator.php
    public function validateSplitLabelDetailUpdate(string $splitLabelID ,array $data): void
    {
        /*
        if (!$this->repository->existsSplitLabelDetailNo($splitLabelNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stosplitLabelNoreId));
        }
        */
        $this->validateSplitLabelDetail($data);
    }
    public function validateSplitLabelDetailInsert( array $data): void
    {
        $this->validateSplitLabelDetail($data);
=======
    public function validateSellCpoItemUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsSellCpoItemNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validateSellCpoItem($data);
    }
    public function validateSellCpoItemInsert( array $data): void
    {
        $this->validateSellCpoItem($data);
>>>>>>> racha05:src/Domain/SellCpoItem/Service/SellCpoItemValidator.php
    }
}
