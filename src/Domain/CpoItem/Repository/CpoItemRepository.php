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
    public function updateCpoItem(int $id, array $data): void
    {
        $this->queryFactory2->newUpdate('cpo_item', $data)->andWhere(['CpoItemID' => $id])->execute();
    }

    public function deleteCpoItem(int $id): void
    {
        $this->queryFactory->newDelete('pack_cpo_items')->andWhere(['id' => $id])->execute();
    }


    public function findCpoItem(array $params): array
    {
        $query = $this->queryFactory2->newSelect('cpo_item');

        $query->select(
            [
                'CpoItemID',
                'cpo_item.ProductID',
                'cpo_item.PONo',
                'Quantity',
                'DueDate',
                'PackingQty',
                'cpo_item.PONo'


            ]
        );
        $query->join([
            'c' => [
                'table' => 'cpo',
                'type' => 'INNER',
                'conditions' => 'c.CpoID = cpo_item.CpoID AND cpo_item.Quantity>cpo_item.PackingQty',
            ]
        ]);


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
            'p' => [
                'table' => 'product',
                'type' => 'INNER',
                'conditions' => 'p.ProductID = cpo_item.ProductID',
            ]
        ]);

        $query->join([
            'c' => [
                'table' => 'cpo',
                'type' => 'INNER',
                'conditions' => 'c.CpoID = cpo_item.CpoID AND cpo_item.Quantity>cpo_item.PackingQty',
            ]
        ]);

        if (isset($params['ProductID'])) {
            $query->andWhere(['p.ProductID ' => $params['ProductID']]);
            $query->orderAsc('DueDate');
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
                'PackingQty',
            ]
        );

        $query->Where(['ProductID' => $ProductID]);

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
