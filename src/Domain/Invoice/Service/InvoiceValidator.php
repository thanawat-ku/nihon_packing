<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Repository\InvoiceRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class InvoiceValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(InvoiceRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('PackingQty', 'Input required');
    }
    public function validateInvoice(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateInvoiceUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsInvoiceNo($invoiceNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoinvoiceNoreId));
        }
        */
        $this->validateInvoice($data);
    }
    public function validateInvoiceInsert( array $data): void
    {
        $this->validateInvoice($data);
    }
}
