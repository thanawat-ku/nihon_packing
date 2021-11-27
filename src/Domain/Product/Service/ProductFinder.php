<?php

namespace App\Domain\Product\Service;

use App\Domain\Product\Repository\ProductRepository;

/**
 * Service.
 */
final class ProductFinder
{
    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param ProductRepository $repository The repository
     */
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find products.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findProducts(array $params): array
    {
        return $this->repository->findProducts($params);
    }
    
    public function getLocalMaxProductId():int
    {
        $data=$this->repository->getLocalMaxProductId()[0]["max_id"];
        if(is_null($data)){
            return 0;
        }
        else{
            return $data;
        }
    }
    public function getSyncProducts(int $maxId):array
    {
        return $this->repository->getSyncProducts($maxId);
    }
}
