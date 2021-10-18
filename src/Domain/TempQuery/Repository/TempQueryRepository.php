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

    public function findTempQuery(array $params): array
    {
        $query = $this->queryFactory->newSelect('temp_query');
        
        $query->select(
            [
                'temp_query.cpo_item_id',
                'cpo_id',
                'cpo_no',
                'quantity',
                'packing_qty',
                'due_date',
                'sell_no',
                'part_code',
                'part_name',
                'sci.sell_qty',
                'sci.id',
                
                
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
                'table' => 'sells',
                'type' => 'INNER',
                'conditions' => 's.id = \''.$params['sell_id'].'\'',
            ]
        ]);

        $query->join([
            'sci' => [
                'table' => 'sell_cpo_items',
                'type' => 'INNER',
                'conditions' => 'sci.sell_id = s.id and temp_query.cpo_item_id=sci.cpo_item_id',
            ]
        ]);

        // if(isset($params['uuid'])){
        //     $query->Where(['merge_pack_details.merge_pack_id' => $params["uuid"]]);
        // }

        return $query->execute()->fetchAll('assoc') ?: [];
    }


    public function findTempQueryCheck(array $params): array
    {
        $query = $this->queryFactory->newSelect('temp_query');
        
        $query->select(
            [

                'temp_query.cpo_item_id',
                'cpo_id',
                'cpo_no',
                'quantity',
                'packing_qty',
                'due_date',
                // 'sell_no',
                // 'part_code',
                // 'part_name',
                // 'quantity',
                // 'sci.sell_qty',
                // 'sci.id'
                
            ]
        );

        if(isset($params['ProductID'])){
            $query->Where(['product_id' => $params["ProductID"]]);
        }



        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
