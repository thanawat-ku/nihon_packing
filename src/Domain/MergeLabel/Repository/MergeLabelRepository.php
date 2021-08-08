<?php

namespace App\Domain\MergeLabel\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class MergeLabelRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    // public function insertMergeLabel(array $row): int
    // {
    //     $row['created_at'] = Chronos::now()->toDateTimeString();
    //     $row['created_user_id'] = $this->session->get('user')["id"];
    //     $row['updated_at'] = Chronos::now()->toDateTimeString();
    //     $row['updated_user_id'] = $this->session->get('user')["id"];

    //     return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    // }
    // public function updateMergeLabel(int $labelID, array $data): void
    // {
    //     $data['updated_at'] = Chronos::now()->toDateTimeString();
    //     $data['updated_user_id'] = $this->session->get('user')["id"];

    //     $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    // }    
    
    // public function deleteMergeLabel(int $labelID): void
    // {
    //     $this->queryFactory->newDelete('labels')->andWhere(['id' => $labelID])->execute();
    // }

    public function findMergeLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'label_no',
                // 'product_id',
                // 'label_type',
                'labels.quantity',
                'lot_no',
                // 'product_name',
                // 'product_code',
                'labels.merge_pack_id'
            ]
        );
        $query->join([
            'l' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'l.id = labels.lot_id',
            ]]);
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
                'conditions' => 'p.id = l.product_id',
            ]]);

        if(isset($params['merge_pack_id'])){
            $query->andWhere(['merge_pack_id' => $params['merge_pack_id']]);
        }
        return $query->execute()->fetchAll('assoc') ?: [];

        
    }

}
