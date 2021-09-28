<?php

namespace App\Domain\Product\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ProductRepository
{
    private $queryFactory;
    private $queryFactory2;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory,QueryFactory2 $queryFactory2)
    {
        $this->queryFactory = $queryFactory;
        $this->queryFactory2 = $queryFactory2;
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
    

    public function findProduct(array $params): array
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

    public function findProducts(array $params): array
    {
        $query = $this->queryFactory2->newSelect('product');
        
        $query->select(
            [
                'id'=>'ProductID',
                'part_code'=>'PartCode',
                'part_name'=>'PartName',
                'std_pack'=>'PackingStd',
                'std_box'=>'BoxStd',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
