<?php

namespace App\Domain\LabelNonfully\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LabelNonfullyRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function insertLabelNonfully(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }
    public function updateLabelNonfullyApi(int $labelID, array $data,$user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }    
    
    public function deleteLabelNonfully(int $labelID): void
    {
        $this->queryFactory->newDelete('labels')->andWhere(['id' => $labelID])->execute();
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
                'labels.status',
                'product_code',
                'product_name',
                'std_pack',
                'std_box',
                'lot_no'
                
            ]
        );
        // $query->join([
        //     'mp' => [
        //         'table' => 'merge_packs',
        //         'type' => 'INNER',
        //         'conditions' => 'mp.id = labels.merge_pack_id',
        //     ]]);
        // $query->join([
        //     'mpd' => [
        //         'table' => 'merge_pack_details',
        //         'type' => 'INNER',
        //         'conditions' => 'mpd.merge_pack_id = mp.product_id',
        //     ]]);
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

    public function findMergePacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_packs');
        $query->select(
            [
                'merge_packs.id',
                'merge_no',
                'product_id',
                'product_code',
                'merge_status',
                // 'label_no',
                'merge_packs.created_user_id',
                'std_pack',
                
            ]
        );

        $query->join(
            [
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = merge_packs.product_id',
            ]
            ]
        );
        // $query->join([
        //     'lb' => [
        //         'table' => 'labels',
        //         'type' => 'INNER',
        //         'conditions' => 'merge_packs.id = lb.merge_pack_id',
        //     ]
        // ]);
        // $query->group([
        //     'merge_packs.merge_no'
        //     ]
    
        //     );
        

        // $quantity=$params[][];

        // if (isset($params['merge_no'])) {
        //     $query->where(
        //         // ['merge_packs.id' => 1],
        //         ['merge_no' => "gg"]
        //     );
        // }

        // if(isset($params['lot_id'])){
        //     $query->andWhere(['lot_id' => $params['lot_id']]); 
        // }
        

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
