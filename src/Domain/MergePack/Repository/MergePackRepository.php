<?php

namespace App\Domain\MergePack\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class MergePackRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function insertMergePackApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        $row['merge_status']="CREATED";

        return (int)$this->queryFactory->newInsert('merge_packs', $row)->execute()->lastInsertId();
    }
    public function updateMergePackApi(int $lotID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;


        $this->queryFactory->newUpdate('merge_packs', $data)->andWhere(['id' => $lotID])->execute();
    }    

    public function updateLabelPackMergeApi(string $labelNo, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['label_no' => $labelNo])->execute();
    } 

    public function updateMergingApi(string $mergeNO, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;


        $this->queryFactory->newUpdate('merge_packs', $data)->andWhere(['merge_no' => $mergeNO])->execute();
    }  


    // public function printMergePack(int $lotID): void
    // {
    //     $data['updated_at'] = Chronos::now()->toDateTimeString();
    //     $data['updated_user_id'] = $this->session->get('user')["id"];
    //     $data['printed_user_id'] = $this->session->get('user')["id"];
    //     $data['status'] = "PRINTED";

    //     $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    // }
    // public function deleteMergePack(int $lotID): void
    // {
    //     $this->queryFactory->newDelete('lots')->andWhere(['id' => $lotID])->execute();
    // }

    public function deleteMergePackApi(int $labelID): void
    {
        $this->queryFactory->newDelete('labels')->andWhere(['id' => $labelID])->execute();
    }
    
    public function findMergePacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_packs');
        $query->select(
            [
                'merge_packs.id',
                'merge_no',
                'product_id',
                'part_code',
                'part_name',
                'merge_status',
                // 'label_no',
                'merge_packs.created_user_id',
                'std_pack'
                
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
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelPackMerges(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'lot_id',
                'merge_pack_id',
                'label_no',
                'label_type',
                'labels.quantity',
                'labels.status',
                'part_code',
                'part_name'
              
            ]
        );
       

        $query->join([
            'mp' => [
                'table' => 'merge_packs',
                'type' => 'INNER',
                'conditions' => 'mp.id = labels.merge_pack_id',
            ]]);

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = mp.product_id',
            ]]);
       
        $query->where(
            ['label_type !=' => "FULLY"]  
        );

        $query->group([
            'labels.label_no'
            ]);
       
        if(isset($params['merge_pack_id'])){
            $query->andWhere(['merge_pack_id' => $params['merge_pack_id']]);
        }
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    
}
