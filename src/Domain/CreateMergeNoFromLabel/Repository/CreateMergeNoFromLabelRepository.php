<?php

namespace App\Domain\CreateMergeNoFromLabel\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class CreateMergeNoFromLabelRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function insertCreateMergeNoFromLabelMergePackApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }

    public function insertMergePackApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('merge_packs', $row)->execute()->lastInsertId();
    }
    
    public function updateCreateMergeNoFromLabelMergePackApi(int $CreateMergeNoFromLabelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $CreateMergeNoFromLabelID])->execute();
    }    
    
    public function deleteCreateMergeNoFromLabel(int $CreateMergeNoFromLabelID): void
    {
        $this->queryFactory->newDelete('labels')->andWhere(['id' => $CreateMergeNoFromLabelID])->execute();
    }

    public function findCreateMergeNoFromLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'lot_id',
                'labels.merge_pack_id',
                'label_no',
                'l.product_id',
                'label_type',
                'labels.quantity',
                'labels.status',
                // 'lot_no',
                'std_pack',
                // 'product_name',
                'product_code',
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
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findMergePacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_packs');
        $query->select(
            [
                'id',
                'merge_no',
                'product_id',
                'merge_status',
            ]
        ); 

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
