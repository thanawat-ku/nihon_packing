<?php

namespace App\Domain\Tag\Service;

use App\Domain\Tag\Repository\TagRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class TagValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(TagRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('tag_no', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('quantity', 'Input required');
    }
    
    public function validateTag(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateTagUpdate(string $tagNo, array $data): void
    {
        /*
        if (!$this->repository->existsTagNo($tagNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stotagNoreId));
        }
        */
        $this->validateTag($data);
    }
    public function validateTagInsert( array $data): void
    {
        $this->validateTag($data);
    }
}
