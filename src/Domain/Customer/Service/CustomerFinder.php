<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerRepository;

/**
 * Service.
 */
final class CustomerFinder
{
    private $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findCustomers(array $params): array
    {
        return $this->repository->findCustomers($params);
    }
    
    public function getLocalMaxCustomerId():int
    {
        $data=$this->repository->getLocalMaxCustomerId()[0]["max_id"];
        if(is_null($data)){
            return 0;
        }
        else{
            return $data;
        }
    }
    public function getSyncCustomers(int $maxId):array
    {
        return $this->repository->getSyncCustomers($maxId);
    }
}
