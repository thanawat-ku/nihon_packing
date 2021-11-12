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

    public function insertMergePackDetail(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];


        return (int)$this->queryFactory->newInsert('merge_pack_details', $row)->execute()->lastInsertId();
    }

    public function insertMergePackDetailCheckApi(array $row, $user_id): int
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

    public function deleteLabelMergePackApi(int $id): void
    {
        $this->queryFactory->newDelete('merge_pack_details')->andWhere(['id' => $id])->execute();
    }

    public function deleteMergePackDetailApi(int $id): void
    {
        $this->queryFactory->newDelete('merge_pack_details')->andWhere(['merge_pack_id' => $id])->execute();
    }

    public function deleteMergePackDetailFromLabel(int $labelId): void
    {
        $this->queryFactory->newDelete('merge_pack_details')->andWhere(['label_id' => $labelId])->execute();
    }

    public function deleteMergePackDetail(int $mergeId): void
    {
        $this->queryFactory->newDelete('merge_pack_details')->andWhere(['merge_pack_id' => $mergeId])->execute();
    }

    public function findMergePackDetailFromLots(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_pack_details'); // focus that
        $query->select(
            [
                'merge_pack_details.id',
                'merge_pack_details.merge_pack_id',     
                'label_no', /// 
                'lot_no',
                'lb.quantity',
                'l.product_id',
                'p.part_name',
                'p.part_code',
                'label_id',
                'lb.status',
                'lb.label_type',
                'std_pack',
                'std_box',
         
            ]
        );

        $query->join([  
            'lb' => [
                'table' => 'labels',  //table name
                'type' => 'INNER',
                'conditions' => 'lb.id = merge_pack_details.label_id',
            ],
        ]);
        $query->join([ 
            'l' => [
                'table' => 'lots',  //table name
                'type' => 'INNER',
                'conditions' => 'l.id = lb.lot_id',
            ],
        ]);
        $query->join([   
            'p' => [
                'table' => 'products',  //table name
                'type' => 'INNER',
                'conditions' => 'p.id = l.product_id',
            ],
        ]);

        $query->andWhere(['lot_id !='=>0]);

        $query->group(
            'merge_pack_details.id'
        );

        // if(isset($params['merge_pack_id'])){
        //     $query->Where(['merge_pack_details.id' => $params["merge_pack_id"]]);
        // }
        if(isset($params['merge_pack_id'])){
            $query->Where(['merge_pack_details.merge_pack_id' => $params["merge_pack_id"]]);
        }
        
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findMergePackDetailFromMergePacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_pack_details'); // focus that
        $query->select(
            [
                'merge_pack_details.id',
                'merge_pack_details.merge_pack_id',  
                'label_no',  
                'lb.quantity',
                'mp.product_id',
                'label_id',
                'lb.status',
                'lb.label_type',
                'p.part_name',
                'p.part_code',
                'mid_from_mp'=>'mp.id',
                'mp.merge_no',
                'mp.merge_status',
                'std_pack',
                'std_box',
                
                
            ]
        );
        $query->join([      //focus that!!!!!!!!!!!!
            'lb' => [
                'table' => 'labels',  //table name
                'type' => 'INNER',
                'conditions' => 'lb.id = merge_pack_details.label_id',
            ],
        ]);
        $query->join([
            'mp' => [
                'table' => 'merge_packs',
                'type' => 'INNER',
                'conditions' => 'mp.id = lb.merge_pack_id',
            ]]);
        $query->join([   
            'p' => [
                'table' => 'products',  //table name
                'type' => 'INNER',
                'conditions' => 'p.id = mp.product_id',
            ],
        ]);

        $query->group(
            'merge_pack_details.id'
        );

        $query->andWhere(['lot_id'=>0]);

        // if(isset($params['register_mp'])){
        //     if($params['register_mp'] == true){
        //         $query->andWhere(['lot_id'=>0]);
        //     }
        // }

        if(isset($params['merge_pack_id'])){
            $query->Where(['merge_pack_details.id' => $params["merge_pack_id"]]);
        }
        if(isset($params['merge_pack_id'])){
            $query->Where(['mp.id' => $params["merge_pack_id"]]);
        }
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function findMergePackDetailForRegisters(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_pack_details'); // focus that
        $query->select(
            [
                'merge_pack_details.id',
                'merge_pack_details.merge_pack_id',  
                'label_no',  
                'lb.quantity',
                'label_id',
                'lb.status',
                'lb.label_type',
            
                
            ]
        );
        $query->join([      //focus that!!!!!!!!!!!!
            'lb' => [
                'table' => 'labels',  //table name
                'type' => 'INNER',
                'conditions' => 'lb.id = merge_pack_details.label_id',
            ],
        ]);

        $query->group(
            'merge_pack_details.id'
        );

        $query->andWhere(['lot_id'=>0]);
        $query->andWhere(['OR' => [['label_type' => "MERGE_FULLY"], ['label_type' => "MERGE_NONFULLY"]]]);
        $query->andWhere(['status'=>"PACKED"]);

        if(isset($params['merge_pack_id'])){
            $query->Where(['merge_pack_details.merge_pack_id' => $params["merge_pack_id"]]);
        }
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findMergePackDetails(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_pack_details'); // focus that
        $query->select(
            [
                'merge_pack_details.id',
                'merge_pack_details.merge_pack_id',  
                'label_no',  
                'lb.quantity',
                'label_id',
                'lb.status',
                'lb.label_type',
                'lb_id'=>'lb.id',
            
                
            ]
        );
        $query->join([      //focus that!!!!!!!!!!!!
            'lb' => [
                'table' => 'labels',  //table name
                'type' => 'INNER',
                'conditions' => 'lb.id = merge_pack_details.label_id',
            ],
        ]);

        // $query->group(
        //     'merge_pack_details.id'
        // );

        // $query->andWhere(['lot_id'=>0]);
        // $query->andWhere(['OR' => [['label_type' => "MERGE_FULLY"], ['label_type' => "MERGE_NONFULLY"]]]);
        // $query->andWhere(['status'=>"PACKED"]);

        if(isset($params['merge_pack_id'])){
            $query->Where(['merge_pack_details.merge_pack_id' => $params["merge_pack_id"]]);
        }
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findMergePackDetailsForMerge(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_pack_details'); // focus that
        $query->select(
            [
                'merge_pack_details.id',
                'merge_pack_details.merge_pack_id', 
                'label_id',         
            ]
        );
        $query->join([      
            'lb' => [
                'table' => 'labels',  
                'type' => 'INNER',
                'conditions' => 'lb.id = merge_pack_details.label_id',
            ],
        ]);
        if(isset($params['merge_pack_id'])){
            $query->Where(['merge_pack_details.merge_pack_id' => $params["merge_pack_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
