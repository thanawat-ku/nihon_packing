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

    public function insertCustomer(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('customers', $row)->execute()->lastInsertId();
    }    

    public function updateCustomer(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('customers', $data)->andWhere(['id' => $lotID])->execute();
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
