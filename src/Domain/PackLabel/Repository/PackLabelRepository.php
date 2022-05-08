<?php

namespace App\Domain\PackLabel\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class PackLabelRepository
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
    public function insertPackLabelApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('pack_labels', $row)->execute()->lastInsertId();
    }

    public function deletePackLabel(int $sellID): void
    {
        $this->queryFactory->newDelete('pack_labels')->andWhere(['pack_id' => $sellID])->execute();
    }
    public function deletePackLabelID(int $labelID): void
    {
        $this->queryFactory->newDelete('pack_labels')->andWhere(['label_id' => $labelID])->execute();
    }

    public function deleteLabelInPackLabel(int $id): void
    {
        $this->queryFactory->newDelete('pack_labels')->andWhere(['id' => $id])->execute();
    }

    public function findPackLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('pack_labels');

        $query->select(
            [
                'pack_labels.id',
                'pack_labels.pack_id',
                'label_id',
                'label_no',
                'total_qty',
                'pack_status',
                'pack_date',
                'lb.quantity',
                'label_type',
                'std_pack',
                'std_box',
                'lb.status',
                'lb.lot_id',
                'prefer_lot_id'
                
            ]
        );

        $query->join([
            's' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 's.id = pack_labels.pack_id',
            ]
        ]);

        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.id = pack_labels.label_id',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = lb.product_id',
            ]
        ]);

        if (isset($params['pack_id'])) {
            $query->Where(['s.id' => $params["pack_id"]]);
        }
        if (isset($params['id'])) {
            $query->Where(['pack_labels.id' => $params["id"]]);
        }
        if (isset($params['label_id'])) {
            $query->Where(['lb.id' => $params["label_id"]]);
        }
        if (isset($params['lot_id'])) {
            $query->Where(['lb.lot_id' => $params["lot_id"]]);
        }
        if (isset($params['find_lot_id'])) {
            $query->Where(['s.id' => $params["pack_id"]]);
            $query->group('lb.lot_id');
        }
        if (isset($params['find_prefer_lot_id'])) {
            $query->Where(['s.id' => $params["pack_id"]]);
            $query->Where(['lb.lot_id' => 0]);
            $query->group('lb.prefer_lot_id');
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findPackLabelForLots(array $params): array
    {
        $query = $this->queryFactory->newSelect('pack_labels');

        $query->select(
            [
                'pack_labels.id',
                'pack_labels.pack_id',
                'label_id',
                'label_no',
                'lot_no',
                'lb.quantity',
                'lb.status',
                'label_type',
                'part_name',
                'part_code',
                'part_no',
                'l.product_id',
                'p.std_pack',
                'p.std_box',
                'issue_date',
            ]
        );

        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.id = pack_labels.label_id',
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

        if (isset($params['pack_id'])) {
            $query->Where(['pack_labels.pack_id' => $params["pack_id"]]);
        }
        if (isset($params['label_id'])) {
            $query->Where(['lb.id' => $params["label_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findPackLabelForMergePacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('pack_labels');

        $query->select(
            [
                'pack_labels.id',
                'pack_labels.pack_id',
                'label_id',
                'label_no',
                'lb.quantity',
                'lb.status',
                'label_type',
                'p.part_name',
                'part_no',
                'part_code',
                'mp.product_id',
                'p.std_pack',
                'p.std_box',
                'merge_no',
                'merge_date',
            ]
        );

        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.id = pack_labels.label_id',
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

        if (isset($params['pack_id'])) {
            $query->Where(['pack_labels.pack_id' => $params["pack_id"]]);
        }
        if (isset($params['label_id'])) {
            $query->Where(['lb.id' => $params["label_id"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function checkLabelInPackLabel(string $labelNo)
    {
        $query = $this->queryFactory->newSelect('pack_labels');
        $query->select(
            [
                'pack_labels.id',
                'label_id',
                'label_no',
            ]
        );

        $query->join([
            'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.id = pack_labels.label_id',
            ]
        ]);
        $query->andWhere(['label_no' => $labelNo]);

        $row = $query->execute()->fetch('assoc');
        if (!$row) {
            return null;
        } else {
            return $row;
        }
        return false;
    }
}
