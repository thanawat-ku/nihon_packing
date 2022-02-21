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


    public function __construct(Session $session, QueryFactory $queryFactory, QueryFactory2 $queryFactory2)
    {
        $this->queryFactory = $queryFactory;
        $this->queryFactory2 = $queryFactory2;
        $this->session = $session;
    }
    // public function insertCpoItem(array $row): int
    // {
    //     $row['created_at'] = Chronos::now()->toDateTimeString();
    //     $row['created_user_id'] = $this->session->get('user')["id"];
    //     $row['updated_at'] = Chronos::now()->toDateTimeString();
    //     $row['updated_user_id'] = $this->session->get('user')["id"];

    //     return (int)$this->queryFactory->newInsert('cpo_item', $row)->execute()->lastInsertId();
    // }
    public function updateCpoItem(int $id, array $data): void
    {
        $this->queryFactory2->newUpdate('[nsp_pack].[dbo].[cpo_item]', $data)->andWhere(['CpoItemID' => $id])->execute();
    }

    public function deleteCpoItem(int $id): void
    {
        $this->queryFactory->newDelete('sell_cpo_items')->andWhere(['id' => $id])->execute();
    }


    public function findCpoItem(array $params): array
    {
        $query = $this->queryFactory2->newSelect('cpo_item');

        $query->select(
            [
                'CpoNo',
                'cpo_item.CpoID',
                'CpoItemID',
                'cpo_item.ProductID',
                'Quantity',
                'DueDate',
                'PackingQty',


            ]
        );
        if (isset($params['cpo_item_id'])) {
            $query->join([
                'c' => [
                    'table' => 'cpo',
                    'type' => 'INNER',
                    'conditions' => 'c.CpoID = cpo_item.CpoID',
                ]
            ]);
        } else {
            $query->join([
                'c' => [
                    'table' => 'cpo',
                    'type' => 'INNER',
                    'conditions' => 'c.CpoID = cpo_item.CpoID AND cpo_item.Quantity>cpo_item.PackingQty',
                ]
            ]);
        }


        if (isset($params['product_id'])) {
            $query->andWhere(['cpo_item.ProductID' => $params['product_id']]);
        }
        if (isset($params['cpo_item_id'])) {
            $query->andWhere(['CpoItemID' => $params['cpo_item_id']]);
        }


        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findCpoItemSelect(array $params): array
    {

        $query = $this->queryFactory2->newSelect('cpo_item');

        $query->select(
            [
                'CpoNo',
                'cpo_item.CpoID',
                'CpoItemID',
                'cpo_item.ProductID',
                'Quantity',
                'DueDate',
                'PackingQty',
                'PartName',
                'PartCode',
                'PONo',

            ]
        );

        $query->join([
            'c' => [
                'table' => 'cpo',
                'type' => 'INNER',
                'conditions' => 'c.CpoID = cpo_item.CpoID AND cpo_item.Quantity>cpo_item.PackingQty',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'product',
                'type' => 'INNER',
                'conditions' => 'p.ProductID = cpo_item.ProductID',
            ]
        ]);

        if (isset($params['ProductID'])) {
            $query->andWhere(['p.ProductID ' => $params['ProductID']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['DueDate <=' => $params['endDate'], 'DueDate >=' => $params['startDate']]);
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
