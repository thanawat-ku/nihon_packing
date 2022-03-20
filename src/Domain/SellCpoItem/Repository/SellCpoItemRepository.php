<?php

namespace App\Domain\SellCpoItem\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SellCpoItemRepository
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
    public function insertSellCpoItemApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('sell_cpo_items', $row)->execute()->lastInsertId();
    }
    public function updateSellCpoItemApi(int $id, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('sell_cpo_items', $data)->andWhere(['id' => $id])->execute();
    }
    public function updateSellCpoItem(int $id, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('sell_cpo_items', $data)->andWhere(['id' => $id])->execute();
    }

    public function deleteSellCpoItem(int $id): void
    {
        $this->queryFactory->newDelete('sell_cpo_items')->andWhere(['id' => $id])->execute();
    }
    public function deleteCpoItemInSellCpoItemApi(int $sellID): void
    {
        $this->queryFactory->newDelete('sell_cpo_items')->andWhere(['sell_id' => $sellID])->execute();
    }


    public function findSellCpoItems(array $params): array
    {
        $query = $this->queryFactory->newSelect('sell_cpo_items');

        $query->select(
            [
                'sell_cpo_items.id',
                'sell_no',
                'sell_date',
                'product_id',
                'total_qty',
                'sell_status',
                'sell_id',
                'cpo_item_id',
                'remain_qty',
                'sell_qty'
            ]
        );

        $query->join([
            's' => [
                'table' => 'sells',
                'type' => 'INNER',
                'conditions' => 's.id = sell_cpo_items.sell_id',
            ]
        ]);

        if(isset($params['sell_id'])){
            $query->andWhere(['sell_cpo_items.sell_id ' => $params['sell_id']]);
        }
        if(isset($params['id'])){
            $query->andWhere(['sell_cpo_items.id ' => $params['id']]);
        }
        if(isset($params['find_cpo_item_id'])){
            $query->andWhere(['sell_cpo_items.sell_id ' => $params['sell_id']]);
            $query->group('cpo_item_id');
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findSellCpoItemProductID(int $product_id)
    {
        $query = $this->queryFactory->newSelect('sells');
        $query->select(
            [
                'sells.id',
                'sell_no',
                'sell_date',
                'product_id',
                'total_qty',
                'sell_status',
            ]
        );

        $query->andWhere(['product_id' => $product_id]);

        $row = $query->execute()->fetch('assoc');

        if (!$row) {
            return null;
        } else {
            return $row;
        }
        return false;
    }
}
