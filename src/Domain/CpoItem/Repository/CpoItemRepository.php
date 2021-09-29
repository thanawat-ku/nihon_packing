<?php

namespace App\Domain\CpoItem\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;


final class CpoItemRepository
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
    // public function insertCpoItem(array $row): int
    // {
    //     $row['created_at'] = Chronos::now()->toDateTimeString();
    //     $row['created_user_id'] = $this->session->get('user')["id"];
    //     $row['updated_at'] = Chronos::now()->toDateTimeString();
    //     $row['updated_user_id'] = $this->session->get('user')["id"];

    //     return (int)$this->queryFactory->newInsert('cpo_item', $row)->execute()->lastInsertId();
    // }
    public function updateCpoItem(int $cpo_itemID, array $data): void
    {
        // $data['updated_at'] = Chronos::now()->toDateTimeString();
        // $data['updated_user_id'] = $this->session->get('user')["id"];

        // $this->queryFactory->newUpdate('cpo_item', $data)->andWhere(['id' => $cpo_itemID])->execute();
    }
    
    public function deleteCpoItem(int $cpo_itemID): void
    {
        // $this->queryFactory->newDelete('cpo_item')->andWhere(['id' => $cpo_itemID])->execute();
    }
    

    public function findCpoItem(array $params): array
    {
        
        $query = $this->queryFactory2->newSelect('cpo_item');
        
        $query->select(
            [
                'CpoItemID',
                'ProductID',
                'Quantity',
                // 'DueDate',
                'PackingQty',
                // 'part_code',
                // 'part_name',
                // 'std_pack',
                // 'std_box',
            ]
        );

        // $query->andWhere(['ProductID ' => 533]);

        if(isset($params['ProductID'])){
            $query->andWhere(['ProductID ' => $params['ProductID']]);
        }
        

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findIDFromProductName(array $params, int $ProductID): array
    {
        $query = $this->queryFactory2->newSelect('cpo_item');
        
        $query->select(
            [
                'CpoItemID',
                'ProductID',
                'Quantity',
                // 'DueDate',
                'PackingQty',
            ]
        );

        // if(isset($params['PackingQty'])){
        //     $query->andWhere(['Quantity >' => $params['PackingQty']]);
        // }

        $query->Where(['ProductID' => $ProductID]);

        // $row = $query->execute()->fetch('assoc');


        // if (!$row) {
        //     return null;
        // }
        // else{
        //     return $row;
        // }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
