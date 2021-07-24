<?php

namespace App\Domain\Customer\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class CustomerRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    

    public function findCustomers(array $params): array
    {
        $query = $this->queryFactory->newSelect('customers');
        $query->select(
            [
                'customers.id',
                'customer_name',
                'tel_no',
                'address',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
