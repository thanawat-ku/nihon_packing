<?php

namespace App\Domain\Product\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ProductRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }
    public function insertProduct(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('products', $row)->execute()->lastInsertId();
    }
    public function updateProduct(int $productID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('products', $data)->andWhere(['id' => $productID])->execute();
    }
    
    public function deleteProduct(int $productID): void
    {
        $this->queryFactory->newDelete('products')->andWhere(['id' => $productID])->execute();
    }
    

    public function findProducts(array $params): array
    {
        $query = $this->queryFactory->newSelect('products');
        $query->select(
            [
                'id',
                'part_code',
                'part_name',
                'std_pack',
                'std_box',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
