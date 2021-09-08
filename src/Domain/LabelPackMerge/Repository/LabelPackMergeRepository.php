<?php

namespace App\Domain\LabelPackMerge\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LabelPackMergeRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function insertLabelPackMerge(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }
    public function updateLabelPackMergeApi(string $labelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['label_no' => $labelID])->execute();
    }    
    
    public function deleteLabelPackMergeApi(int $labelID): void
    {
        $this->queryFactory->newDelete('labels')->andWhere(['id' => $labelID])->execute();
    }

    public function deleteLabelMergePackApi(int $labelID): void
    {
        $this->queryFactory->newDelete('merge_pack_details')->andWhere(['label_id' => $labelID])->execute();
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
                'product_code',
                'product_name'
              
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