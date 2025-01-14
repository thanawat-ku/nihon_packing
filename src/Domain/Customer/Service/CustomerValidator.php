<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class CustomerValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(CustomerRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
        ->notEmptyString('customer_code', 'Input required')
        ->notEmptyString('customer_name', 'Input required')
        ->notEmptyString('is_sync', 'Input required');
    }
    public function validateCustomer(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateCustomerUpdate(string $customer_name, array $data): void
    {
        /*
        if (!$this->repository->existsCustomerNo($customerNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stocustomerNoreId));
        }
        */
        $this->validateCustomer($data);
    }
    public function validateCustomerInsert( array $data): void
    {
        $this->validateCustomer($data);
    }
}
