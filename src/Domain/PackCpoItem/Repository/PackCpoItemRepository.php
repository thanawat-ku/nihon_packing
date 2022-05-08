<?php

namespace App\Domain\PackCpoItem\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class PackCpoItemRepository
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
    public function insertPackCpoItemApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('pack_cpo_items', $row)->execute()->lastInsertId();
    }
    public function updatePackCpoItemApi(int $id, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('pack_cpo_items', $data)->andWhere(['id' => $id])->execute();
    }
    public function updatePackCpoItem(int $id, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('pack_cpo_items', $data)->andWhere(['id' => $id])->execute();
    }

    public function deletePackCpoItem(int $id): void
    {
        $this->queryFactory->newDelete('pack_cpo_items')->andWhere(['id' => $id])->execute();
    }
    public function deleteCpoItemInPackCpoItemApi(int $sellID): void
    {
        $this->queryFactory->newDelete('pack_cpo_items')->andWhere(['pack_id' => $sellID])->execute();
    }


    public function findPackCpoItems(array $params): array
    {
        $query = $this->queryFactory->newSelect('pack_cpo_items');

        $query->select(
            [
                'pack_cpo_items.id',
                'pack_no',
                'pack_date',
                'product_id',
                'total_qty',
                'pack_status',
                'pack_id',
                'cpo_item_id',
                'remain_qty',
                'pack_qty'
            ]
        );

        $query->join([
            's' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 's.id = pack_cpo_items.pack_id',
            ]
        ]);

        if(isset($params['pack_id'])){
            $query->andWhere(['pack_cpo_items.pack_id ' => $params['pack_id']]);
        }
        if(isset($params['id'])){
            $query->andWhere(['pack_cpo_items.id ' => $params['id']]);
        }
        if(isset($params['find_cpo_item_id'])){
            $query->andWhere(['pack_cpo_items.pack_id ' => $params['pack_id']]);
            $query->group('cpo_item_id');
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findPackCpoItemProductID(int $product_id)
    {
        $query = $this->queryFactory->newSelect('packs');
        $query->select(
            [
                'packs.id',
                'pack_no',
                'pack_date',
                'product_id',
                'total_qty',
                'pack_status',
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
