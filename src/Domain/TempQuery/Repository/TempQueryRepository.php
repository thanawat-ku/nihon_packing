<?php

namespace App\Domain\TempQuery\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;


final class TempQueryRepository
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
    public function insertTempQuery(array $row): int
    {
        return (int)$this->queryFactory->newInsert('temp_query', $row)->execute()->lastInsertId();
    }
    public function updateTempQuery(int $CpoItemID, array $data): void
    {
        $this->queryFactory->newUpdate('temp_query', $data)->andWhere(['cpo_item_id' => $CpoItemID])->execute();
    }
    public function deleteTempQuery(int $productID): void
    {
        $this->queryFactory->newDelete('temp_query')->andWhere(['product_id' => $productID])->execute();
    }

    public function findTempQuery(array $params): array
    {
        $query = $this->queryFactory->newSelect('temp_query');
        
        $query->select(
            [
                
                'temp_query.cpo_item_id',
                'quantity',
                'packing_qty',
                'due_date',
                'pack_no',
                'part_code',
                'part_name',
                'sci.pack_qty',
                'sci.id',
                'sci.id',
                's.total_qty',
                
                
            ]
        );

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = temp_query.product_id',
            ]
        ]);

        $query->join([
            's' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 's.id = \''.$params['pack_id'].'\'',
            ]
        ]);

        $query->join([
            'sci' => [
                'table' => 'pack_cpo_items',
                'type' => 'INNER',
                'conditions' => 'sci.pack_id = s.id and temp_query.cpo_item_id=sci.cpo_item_id',
            ]
        ]);

        $query->group(['sci.id']);

        if(isset($params['pack_id'])){
            $query->Where(['sci.pack_id' => $params["pack_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findTempQueryCheck(array $params): array
    {
        $query = $this->queryFactory->newSelect('temp_query');
        
        $query->select(
            [

                'temp_query.cpo_item_id',
                'quantity',
                'packing_qty',
                'due_date',
                'pack_no',
                'part_code',
                'part_name',
                'quantity',
                'sci.pack_qty',
                'sci.id'
                
            ]
        );

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = temp_query.product_id',
            ]
        ]);

        $query->join([
            's' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 's.id = \''.$params['pack_id'].'\'',
            ]
        ]);

        $query->join([
            'sci' => [
                'table' => 'pack_cpo_items',
                'type' => 'INNER',
                'conditions' => 'sci.pack_id = s.id and temp_query.cpo_item_id=sci.cpo_item_id',
            ]
        ]);

        if(isset($params['product_id'])){
            $query->Where(['p.id' => $params["product_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findTempQueryCheckUpdate(array $params): array
    {
        $query = $this->queryFactory->newSelect('temp_query');
        
        $query->select(
            [

                'temp_query.cpo_item_id',
                'quantity',
                'packing_qty',
                'due_date',
                'part_code',
                'part_name',
                'sci.pack_qty',
                'sci.id'
                
            ]
        );

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = temp_query.product_id',
            ]
        ]);

        $query->join([
            'sci' => [
                'table' => 'pack_cpo_items',
                'type' => 'INNER',
                'conditions' => 'temp_query.cpo_item_id = sci.cpo_item_id',
            ]
        ]);

        if(isset($params['product_id'])){
            $query->Where(['p.id' => $params["product_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
