<?php

namespace App\Domain\SellLabel\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SellLabelRepository
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
    public function insertSellLabelApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('sell_labels', $row)->execute()->lastInsertId();
    }

    public function deleteSellLabel(int $sellID): void
    {
        $this->queryFactory->newDelete('sell_labels')->andWhere(['sell_id' => $sellID])->execute();
    }
    public function deleteSellLabelID(int $labelID): void
    {
        $this->queryFactory->newDelete('sell_labels')->andWhere(['label_id' => $labelID])->execute();
    }

    public function deleteLabelInSellLabel(int $id): void
    {
        $this->queryFactory->newDelete('sell_labels')->andWhere(['id' => $id])->execute();
    }

    public function findSellLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('sell_labels');

        $query->select(
            [
                'sell_labels.id',
                'sell_labels.sell_id',
                'label_id',
                'label_no',
                'total_qty',
                'sell_status',
                'sell_date',
                'lb.quantity',
                'label_type',
                // 'part_name',
                // 'part_code'
            ]
        );

        $query->join([
            's' => [
                'table' => 'sells',
                'type' => 'INNER',
                'conditions' => 's.id = sell_labels.sell_id',
            ]
        ]);

        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.id = sell_labels.label_id',
            ]
        ]);

        if(isset($params['sell_id'])){
            $query->Where(['sell_labels.sell_id' => $params["sell_id"]]);
        }if(isset($params['id'])){
            $query->Where(['sell_labels.id' => $params["id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findSellLabelForLots(array $params): array
    {
        $query = $this->queryFactory->newSelect('sell_labels');

        $query->select(
            [
                'sell_labels.id',
                'sell_labels.sell_id',
                'label_id',
                'label_no',
                'lot_no',
                'lb.quantity',
                'lb.status',
                'label_type',
                'part_name',
                'part_code',
                'l.product_id',
                'p.std_pack',
                'p.std_box',
            ]
        );

        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.id = sell_labels.label_id',
            ]
        ]);

        $query->join([
            'l' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'l.id = lb.lot_id',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = l.product_id',
            ]
        ]);

        $query->andWhere(['l.is_delete' => 'N']);

        if(isset($params['sell_id'])){
            $query->Where(['sell_labels.sell_id' => $params["sell_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findSellLabelForMergePacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('sell_labels');

        $query->select(
            [
                'sell_labels.id',
                'sell_labels.sell_id',
                'label_id',
                'label_no',
                'lb.quantity',
                'lb.status',
                'label_type',
                'p.part_name',
                'part_code',
                'product_id',
                'p.std_pack',
                'p.std_box',
            ]
        );

        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.id = sell_labels.label_id',
            ]
        ]);

        $query->join([
            'mp' => [
                'table' => 'merge_packs',
                'type' => 'INNER',
                'conditions' => 'mp.id = lb.merge_pack_id',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = mp.product_id',
            ]
        ]);

        if(isset($params['sell_id'])){
            $query->Where(['sell_labels.sell_id' => $params["sell_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}