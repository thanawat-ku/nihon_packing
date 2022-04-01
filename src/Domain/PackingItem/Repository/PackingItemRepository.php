<?php

namespace App\Domain\PackingItem\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;


final class PackingItemRepository
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
    public function insertPackingItem(array $row): int
    {
        return (int)$this->queryFactory2->newInsert('packing_item', $row)->execute()->lastInsertId();
    }
    public function deletePackingItemAll(int $id): void
    {
        $this->queryFactory2->newDelete('packing_item')->andWhere(['PackingID' => $id])->execute();
    }
    public function findPackingItem(array $params): array
    {
        $query = $this->queryFactory2->newSelect('packing_item');

        $query->select(
            [
                'PackingItemID',
                'packing_item.ProductID',
                'Quantity',
                'DueDate',
                'PackingQty',
                


            ]
        );
        if (isset($params['packing_item_id'])) {
            $query->join([
                'c' => [
                    'table' => 'cpo',
                    'type' => 'INNER',
                    'conditions' => 'c.CpoID = packing_item.CpoID',
                ]
            ]);
        } else {
            $query->join([
                'c' => [
                    'table' => 'cpo',
                    'type' => 'INNER',
                    'conditions' => 'c.CpoID = packing_item.CpoID AND packing_item.Quantity>packing_item.PackingQty',
                ]
            ]);
        }


        if (isset($params['product_id'])) {
            $query->andWhere(['packing_item.ProductID' => $params['product_id']]);
        }
        if (isset($params['packing_item_id'])) {
            $query->andWhere(['PackingItemID' => $params['packing_item_id']]);
        }


        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
