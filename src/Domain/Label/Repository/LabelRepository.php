<?php

namespace App\Domain\Label\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LabelRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function insertLabel(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }

    public function insertLabelApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }

    public function insertLabelerror(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('merge_packs', $row)->execute()->lastInsertId();
    }

    public function insertLabelMergePackApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }

    public function updateLabelMergePackApi(int $labelID, array $data, $user_id): void
    {

        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }
    public function updateLabelApi(int $labelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }

    public function updateLabelStrApi(String $labelNo, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['label_no' => $labelNo])->execute();
    }

    public function updateLabel(int $labelID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }

    public function deleteLabel(int $labelID): void
    {
        $this->queryFactory->newDelete('labels')->andWhere(['id' => $labelID])->execute();
    }

    public function deleteLabelAll(int $lotId): void
    {
        $this->queryFactory->newDelete('labels')->andWhere(['lot_id' => $lotId])->execute();
    }

    public function findLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'label_no',
                'merge_pack_id',
                'split_label_id',
                'lot_id',
                'product_id',
                'label_type',
                'labels.quantity',
                'lot_no',
                'part_name',
                'part_code',
                'labels.status',
                'std_pack',
                'std_box',
                'labels.split_label_id'
            ]
        );
        $query->join([
            'l' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'l.id = labels.lot_id',
            ]
        ]);
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = l.product_id',
            ]
        ]);
        //find label from lot
        if (isset($params['lot_id'])) {
            $query->andWhere(['lot_id' => $params['lot_id']]);
        }
        //find label from splitLabel
        if (isset($params['split_label_id'])) {
            $query->andWhere(['split_label_id' => $params['split_label_id']]);
        }
        if (isset($params['status'])) {
            $query->andWhere(['labels.status' => $params['status']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelNonfullys(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'label_no',
                // 'l.product_id',
                'label_type',
                'labels.quantity',
                // 'lot_id',
                'labels.merge_pack_id',
                'labels.status',
                // 'part_code',
                // 'part_name',
                // 'std_pack',
                // 'std_box',
                // 'lot_no'

            ]
        );
        // $query->join([
        //     'l' => [
        //         'table' => 'lots',
        //         'type' => 'INNER',
        //         'conditions' => 'l.id = labels.lot_id',
        //     ]
        // ]);
        // $query->join([
        //     'p' => [
        //         'table' => 'products',
        //         'type' => 'INNER',
        //         'conditions' => 'p.id = l.product_id',
        //     ]
        // ]);
        // $query->join([
        //     'mp' => [
        //         'table' => 'merge_packs',
        //         'type' => 'INNER',
        //         'conditions' => 'mp.id = labels.merge_pack_id',
        //     ]
        // ]);
        $query->group([
            'labels.id'
        ]);

        // $query->where(['OR' => [['published' => false], ['published' => true]]]);

        $query->where(['OR' => [['labels.label_type' => 'NONFULLY'], ['labels.label_type' => 'MERGENONFULLY']]]);

        // if (isset($params['product_id'])) {
        //     $query->andWhere(['product_id' => $params['product_id']]);
        // }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function checkLabel(string $labelNO)
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'label_no',
                'product_id',
                'label_type',
                'labels.quantity',
                'lot_id',
                'labels.merge_pack_id',
                'labels.status',
                'part_code',
                'part_name',
                'std_pack',
                'std_box',
                'lot_no'

            ]
        );
        $query->join([
            'l' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'l.id = labels.lot_id',
            ]
        ]);
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = l.product_id',
            ]
        ]);
        $query->group([
            'labels.id'
        ]);

        $query->Where(['label_no' => $labelNO]);

        $row = $query->execute()->fetch('assoc');


        if (!$row) {
            return null;
        } else {
            return $row;
        }
        return false;
    }

    public function findCreateMergeNoFromLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'lot_id',
                'labels.merge_pack_id',
                'label_no',
                'l.product_id',
                'label_type',
                'labels.quantity',
                'labels.status',
                'std_pack',
                'part_code',
            ]
        );

        $query->join([
            'l' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'l.id = labels.lot_id',
            ]
        ]);
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = l.product_id',
            ]
        ]);

        return $query->execute()->fetchAll('assoc') ?: [];
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
                'part_code',
                'part_name'

            ]
        );


        $query->join([
            'mp' => [
                'table' => 'merge_packs',
                'type' => 'INNER',
                'conditions' => 'mp.id = labels.merge_pack_id',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = mp.product_id',
            ]
        ]);

        $query->where(
            ['label_type !=' => "FULLY"]
        );

        $query->group([
            'labels.label_no'
        ]);

        if (isset($params['merge_pack_id'])) {
            $query->andWhere(['merge_pack_id' => $params['merge_pack_id']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
