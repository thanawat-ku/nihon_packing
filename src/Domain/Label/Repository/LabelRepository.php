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

    public function registerLabel(int $lot_id, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['lot_id' => $lot_id])->execute();
    }

    public function registerLabelApi(int $lot_id, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['lot_id' => $lot_id])->execute();
    }

    public function registerMerge(int $mergeId, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['merge_pack_id' => $mergeId])->execute();
    }

    public function registerSpiteLabelApi(int $lot_id, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $lot_id])->execute();
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

    public function updateLabelMerging(int $labelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }

    public function updateLabeldefault(int $mergePackID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['merge_pack_id' => $mergePackID])->execute();
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
                'labels.product_id',
                'label_type',
                'labels.quantity',
                'lot_no',
                'generate_lot_no',
                'part_name',
                'part_code',
                'labels.status',
                'std_pack',
                'std_box',
                'labels.split_label_id',
                'real_qty',
                'label_void_reason_id',
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
        else if (isset($params['split_label_id'])) {
            $query->andWhere(['split_label_id' => $params['split_label_id']]);
        } else if (isset($params['status'])) {
            $query->andWhere(['labels.status' => $params['status']]);
        } else if (isset($params['label_no'])) {
            $query->andWhere(['labels.label_no' => $params['label_no']]);
        } else if (isset($params['label_id'])) {
            $query->andWhere(['labels.id' => $params['label_id']]);
        } else if (isset($params["startDate"])) {
            $query->andWhere(['l.issue_date <=' => $params['endDate'], 'l.issue_date >=' => $params['startDate']]);
        }

        $query->andWhere(['l.is_delete' => 'N']);
        $query->andWhere(['labels.is_delete' => 'N']);


        $get =  $query->execute()->fetchAll('assoc') ?: [];
        return $get;
        // return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelForLotZero(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'label_no',
                'merge_pack_id',
                'split_label_id',
                'lot_id',
                'labels.product_id',
                'label_type',
                'labels.quantity',
                'part_name',
                'part_code',
                'labels.status',
                'std_pack',
                'std_box',
                'labels.split_label_id',
                'merge_no',
            ]
        );
        $query->join([
            'm' => [
                'table' => 'merge_packs',
                'type' => 'INNER',
                'conditions' => 'm.id = labels.merge_pack_id',
            ]
        ]);
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = m.product_id',
            ]
        ]);
        //find label from lot
        if (isset($params['product_id'])) {
            $query->andWhere(['product_id' => $params['product_id']]);
        } else if (isset($params['split_label_id'])) {
            $query->andWhere(['split_label_id' => $params['split_label_id']]);
        } else if (isset($params['label_id'])) {
            $query->andWhere(['labels.id' => $params['label_id']]);
        } else if (isset($params['merge_pack_id'])) {
            $query->andWhere(['merge_pack_id' => $params['merge_pack_id']]);
        } else if (isset($params["lot_zero"])) {
            $query->andWhere(['lot_id' => $params['lot_zero']]);
        } else if (isset($params["startDate"])) {
            $query->andWhere(['m.merge_date <=' => $params['endDate'], 'm.merge_date >=' => $params['startDate']]);
        }

        $query->andWhere(['labels.is_delete' => 'N']);
        $getdata = $query->execute()->fetchAll('assoc') ?: [];
        return $getdata;
    }

    public function findLabelsForScan(array $params): array
    {
        $query = $this->queryFactory->newSelect('labels');
        $query->select(
            [
                'labels.id',
                'lot_id',
                'merge_pack_id',
                'split_label_id',
                'label_no',
                'label_type',
                'quantity',
                'status',
            ]
        );
        if (isset($params['label_no'])) {
            $query->andWhere(['labels.label_no' => $params['label_no']]);
        }
        //find label from LabelId 
        else if (isset($params['label_id'])) {
            $query->andWhere(['labels.id' => $params['label_id']]);
        }
        $query->andWhere(['labels.is_delete' => 'N']);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelForMerge(array $params): array
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
                'labels.status',
            ]
        );
        
        if (isset($params['product_id'])) {
            $query->andWhere(['product_id' => $params['product_id']]);
        }
        $query->andWhere(['labels.status ' => 'PACKED']);
        $query->andWhere(['label_type in' => ['NONFULLY', 'MERGE_NONFULLY']]);
        $query->andWhere(['labels.is_delete' => 'N']);
        $getdata = $query->execute()->fetchAll('assoc') ?: [];
        return $getdata;
    }

    // public function findLabelForMergeLotZero(array $params): array
    // {
    //     $query = $this->queryFactory->newSelect('labels');
    //     $query->select(
    //         [
    //             'labels.id',
    //             'label_no',
    //             'merge_pack_id',
    //             'split_label_id',
    //             'lot_id',
    //             'product_id',
    //             'label_type',
    //             'labels.quantity',
    //             'part_name',
    //             'part_code',
    //             'labels.status',
    //             'std_pack',
    //             'std_box',
    //             'labels.split_label_id',
    //             'merge_no',
    //         ]
    //     );
    //     $query->join([
    //         'm' => [
    //             'table' => 'merge_packs',
    //             'type' => 'INNER',
    //             'conditions' => 'm.id = labels.merge_pack_id',
    //         ]
    //     ]);
    //     $query->join([
    //         'p' => [
    //             'table' => 'products',
    //             'type' => 'INNER',
    //             'conditions' => 'p.id = m.product_id',
    //         ]
    //     ]);
    //     //find label from lot
    //     if (isset($params['product_id'])) {
    //         $query->andWhere(['product_id' => $params['product_id']]);
    //     }
    //     $query->andWhere(['labels.status ' => 'PACKED']);
    //     $query->andWhere(['label_type in' => ['NONFULLY', 'MERGE_NONFULLY']]);

    //     $getdata = $query->execute()->fetchAll('assoc') ?: [];
    //     return $getdata;
    //     //find label from splitLabel
    //     if (isset($params['split_label_id'])) {
    //         $query->andWhere(['split_label_id' => $params['split_label_id']]);
    //     }
    //     if (isset($params['status'])) {
    //         $query->andWhere(['labels.status' => $params['status']]);
    //     }
    //     if (isset($params['label_no'])) {
    //         $query->andWhere(['label_no' => $params['label_no']]);
    //     }
    //     if (isset($params['id'])) {
    //         $query->andWhere(['labels.id' => $params['id']]);
    //     }
    //     if (isset($params['merge_pack_id'])) {
    //         $query->andWhere(['labels.merge_pack_id' => $params['merge_pack_id']]);
    //     }
    //     $query->andWhere(['labels.is_delete' => 'N']);
    //     return $query->execute()->fetchAll('assoc') ?: [];
    // }

    public function findLabelSingleTable(array $params): array
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
                'split_label_id',

            ]
        );

        if (isset($params['label_no'])) {
            $query->andWhere(['label_no' => $params['label_no']]);
        }
        if (isset($params['merge_pack_id'])) {
            $query->andWhere(['merge_pack_id' => $params['merge_pack_id']]);
        }
        if (isset($params['label_id'])) {
            $query->andWhere(['id' => $params['label_id']]);
        }
        if (isset($params['lot_id'])) {
            $query->andWhere(['lot_id' => $params['lot_id']]);
        }
        $query->andWhere(['labels.is_delete' => 'N']);
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

        // $query->andWhere(['product_id' => $params['product_id']]);

        $query->where(['labels.status !=' => 'CREATED']);

        if (isset($params['product_id'])) {
            $query->andWhere(['product_id' => $params['product_id']]);
        }
        $query->Where(['label_no' => $labelNO]);
        $query->andWhere(['labels.is_delete' => 'N']);
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
                'label_type',
                'std_pack',
                'std_box',
                'part_code',
                'part_name',
                'lot_no',
                'generate_lot_no',
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
        if (isset($params['find_label_for_sell'])) {
            $query->andWhere(['labels.status' => $params['find_label_for_sell']]);
        }

        if (isset($params['id'])) {
            $query->andWhere(['labels.id' => $params['id']]);
        }
        if (isset($params['check_sell_label'])) {
            if (isset($params['ProductID'])) {
                $query->andWhere(['l.product_id' => $params['ProductID']]);
                $query->andWhere(['l.is_delete' => 'N']);
                $query->andWhere(['OR' => [['labels.status' => 'PACKED'], ['labels.status' => 'SELLING']]]);
            }
        }
        if (isset($params['lot_id'])) {
            $query->Where(['l.id' => $params["lot_id"]]);
        }

        $query->andWhere(['labels.is_delete' => 'N']);

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelCreateFromMerges(array $params): array
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
                'part_name',
                'mp.product_id'

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

        if (isset($params['merge_pack_id'])) {
            $query->andWhere(['merge_pack_id' => $params['merge_pack_id']]);
        }

        if (isset($params['label_id'])) {
            $query->andWhere(['labels.id' => $params['label_id']]);
        }
        if (isset($params['lot_id'])) {
            $query->Where(['l.id' => $params["lot_id"]]);
        }

        $query->andWhere(['labels.is_delete' => 'N']);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelFromMergePacks(array $params): array
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
                'part_name',
                'merge_no',
                'mp.product_id',
                'merge_status',
                'std_pack',
                'std_box',

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
        if (isset($params['find_label_for_sell'])) {
            $query->andWhere(['labels.status' => $params['find_label_for_sell']]);
        }

        if (isset($params['merge_pack_id'])) {
            $query->andWhere(['merge_pack_id' => $params['merge_pack_id']]);
        }
        if (isset($params['id'])) {
            $query->andWhere(['labels.id' => $params['id']]);
        }
        if (isset($params['check_sell_label'])) {
            if (isset($params['ProductID'])) {
                $query->andWhere(['mp.product_id' => $params['ProductID']]);
                $query->andWhere(['OR' => [['labels.status' => 'PACKED'], ['labels.status' => 'SELLING']]]);
            }
        }
        $query->andWhere(['labels.is_delete' => 'N']);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
