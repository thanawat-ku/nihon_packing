<?php

namespace App\Domain\MergePackDetail\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class MergePackDetailRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function insertMergePackDetailApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('merge_pack_details', $row)->execute()->lastInsertId();
    }
    public function updateMergePackDetailApi(int $labelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }   
    
    public function updateLabelApi(int $labelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    } 

    public function deleteLabelMergePackApi(int $labelID): void
    {
        $this->queryFactory->newDelete('merge_pack_details')->andWhere(['label_id' => $labelID])->execute();
    }

    public function findMergePackDetails(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_pack_details');
        $query->select(
            [
                'merge_pack_details.id',
                // 'merge_pack.id',
                'merge_pack_details.merge_pack_id',
                // 'merge_no',
                'label_id',
                // 'label_type',
                
                // 'label_no',
                // 'quantity',
                // 'product_name',
                // 'product_code',
            ]
        );
        
        // $query->join([
        //     'mp' => [
        //         'table' => 'merge_packs',
        //         'type' => 'INNER',
        //         'conditions' => 'mp.id = merge_pack_details.merge_pack_id',
        //     ],
        // ]);

        // $query->join([
        //     'lb' => [
        //         'table' => 'labels',
        //         'type' => 'INNER',
        //         'conditions' => 'lb.id = merge_pack_details.label_id',
        //     ]
        // ]);
        // $query->group([
        // 'lb.id'
        // ]

        // );
       
        // $query->join([
        //     'p' => [
        //         'table' => 'products',
        //         'type' => 'INNER',
        //         'conditions' => 'p.id = mp.product_id',
        //     ]
        // ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelNonfullys(array $params): array
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
                'labels.status'
                
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
        if(isset($params['product_id'])){
            $query->andWhere(['product_id' => $params['product_id']]);
        }
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelNonfully(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'label_no',
                'label_type',
                'labels.quantity',
                'labels.merge_pack_id',
                'labels.status'
            ]
        );
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
