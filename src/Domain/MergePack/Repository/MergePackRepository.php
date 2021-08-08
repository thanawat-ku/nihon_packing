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

    public function insertMergePack(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('merge_packs', $row)->execute()->lastInsertId();
    }
    public function updateMergePack(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('merge_packs', $data)->andWhere(['id' => $lotID])->execute();
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
    
    public function findMergePacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_packs');
        $query->select(
            [
                'merge_packs.id',
                'merge_no',
                // 'product_id',
                'product_code',
                'merge_status',
                'label_no',
                
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
        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'merge_packs.id = lb.merge_pack_id',
            ]
        ]);
        $query->group([
            'merge_packs.id'
            ]
    
            );
        

        

        // if(isset($params['lot_id'])){
        //     $query->andWhere(['lot_id' => $params['lot_id']]);
        // }
        

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    
}
