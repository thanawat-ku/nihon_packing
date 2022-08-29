<?php

namespace App\Domain\SparePart\Service;

use App\Domain\SparePart\Repository\SparePartRepository;
use App\Domain\SparePart\Type\SparePartRoleType;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

/**
 * Service.
 */
final class SparePartValidator
{
    /**
     * @var SparePartRepository
     */
    private $repository;

    /**
     * @var ValidationFactory
     */
    private $validationFactory;

    /**
     * The constructor.
     *
     * @param SparePartRepository $repository The repository
     * @param ValidationFactory $validationFactory The validation
     */
    public function __construct(SparePartRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    /**
     * Create validator.
     *
     * @return Validator The validator
     */
    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('spare_part_name', 'Input required')
            ->notEmptyString('spare_part_name', 'Input required');
    }

    /**
     * Validate new customer.
     *
     * @param array<mixed> $data The data
     *
     * @throws ValidationException
     *
     * @return void
     */
    public function validateSparePart(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    /**
     * Validate update.
     *
     * @param int $customerId The customer id
     * @param array<mixed> $data The data
     *
     * @return void
     */
    public function validateSparePartUpdate(int $customerId, array $data): void
    {
        if (!$this->repository->existsSparePartId($customerId)) {
            throw new ValidationException(sprintf('SparePart not found: %s', $customerId));
        }

        $this->validateSparePart($data);
    }
}
