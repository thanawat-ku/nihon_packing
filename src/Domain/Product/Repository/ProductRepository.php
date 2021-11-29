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
    

    public function findProducts_(array $params): array
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

        if(isset($params['PartCode'])){
            $query->andWhere(['PartCode ' => $params['PartCode']]);
        }
        if(isset($params['ProductID'])){
            $query->andWhere(['ProductID' => $params['ProductID']]);
        }

        $query->orderAsc('part_code');

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    //
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

        if(isset($params['PartCode'])){
            $query->andWhere(['part_code ' => $params['PartCode']]);
        }
        if(isset($params['ProductID'])){
            $query->andWhere(['id' => $params['ProductID']]);
        }

        $query->orderAsc('part_code');

        return $query->execute()->fetchAll('assoc') ?: [];
    }
    //

    public function findProduct(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'label_no',
                'product_id',
                'label_type',
                'labels.quantity',
                'lot_id',
                'labels.merge_pack_id',
                'labels.status',
                'part_code',
                'part_name',
                'std_pack',
                'std_box',
                'lot_no'
                
            ]
        );
        $query->join([
            'l' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'l.id = labels.lot_id',
            ]]);
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = l.product_id',
            ]]);
        $query->group([
            'labels.id'
            ]);

        // $query->Where(['label_no' => $labelNO]);

        $row = $query->execute()->fetch('assoc');


        if (!$row) {
            return null;
        }
        else{
            return $row;
        }
        return false;
    }

    
    public function getMaxID(): array
    {
        $query = $this->queryFactory->newSelect('products');
        $query->select(
            [
                'max_id'=> $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getSyncProducts(int $max_id): array
    {
        $query = $this->queryFactory2->newSelect('product');
        $query->select(
            [
                'ProductID',
                'PartNo',
                'PartName',
                'PartCode',
                'CustomerID',
                'PackingStd',
                'BoxStd',
            ]);
        $query->andWhere(['ProductID >' => $max_id]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getLocalMaxProductId():array
    {
        $query = $this->queryFactory->newSelect('products');
        $query->select(
            [
                'max_id' => $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
