<?php

namespace App\Domain\Label\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LabelRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function insertLabel(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }

    public function insertLabelMergePackApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }
    
    public function updateLabelMergePackApi(int $labelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }    
    
    public function deleteLabel(int $labelID): void
    {
        $this->queryFactory->newDelete('labels')->andWhere(['id' => $labelID])->execute();
    }

    public function findLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'lot_id',
                'labels.merge_pack_id',
                'label_no',
                'mp.product_id',
                'label_type',
                'labels.quantity',
                'labels.status',
                // 'lot_no',
                'std_pack',
                // 'product_name',
                'product_code',
            ]
        ); 

        // $query->join([
        //     'l' => [
        //         'table' => 'lots',
        //         'type' => 'INNER',
        //         'conditions' => 'l.id = labels.lot_id',
        //     ]]);
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
                'labels.status'
              
            ]
        );
        $query->join([
            'l' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'l.id = labels.lot_id',
            ]]);
        // $query->join([
        //     'm' => [
        //         'table' => 'merge_packs',
        //         'type' => 'INNER',
        //         'condition' => 'm.id=labels.merge_pack_id'
        //     ]
        //     ]);

        // $query->where(
        //     ['label_type' => "MERGE_FULLY"],
        //     ['label_type' => "MERGE_NONFULLY"]
        // );
        

        // $query->join([
        //     'l' => [
        //         'table' => 'lots',
        //         'type' => 'INNER',
        //         'conditions' => 'l.id = labels.lot_id',
        //     ]]);
        // $query->join([
        //     'p' => [
        //         'table' => 'products',
        //         'type' => 'INNER',
        //         'conditions' => 'p.id = l.product_id',
        //     ]]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findCheckLabels(array $params): array
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
                
                
            ]
        );
       
        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
