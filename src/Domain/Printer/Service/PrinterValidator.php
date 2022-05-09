<?php

namespace App\Domain\Printer\Service;

use App\Domain\Printer\Repository\PrinterRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class PrinterValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(PrinterRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('printer_name', 'Input required')
            ->notEmptyString('printer_address', 'Input required')
            ->notEmptyString('printer_type', 'Input required');
    }
    public function validatePrinter(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validatePrinterUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsPackLabelNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validatePrinter($data);
    }
    public function validatePrinterInsert( array $data): void
    {
        $this->validatePrinter($data);
    }
}
