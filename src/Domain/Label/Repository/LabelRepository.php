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
                'labels.split_label_id',
                'real_qty',
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
        }

        // if (isset($params['status_sp'])) {
        //     $query->andWhere(['labels.status !=' => "USED"]);
        //     $query->andWhere(['labels.status !=' => "VOID"]);
        // }

        else if (isset($params['label_id'])) {
            $query->andWhere(['labels.id' => $params['label_id']]);
        } 
        else if (isset($params["startDate"])) {
            $query->andWhere(['l.issue_date <=' => $params['endDate'], 'l.issue_date >=' => $params['startDate']]);
        }

        $query->andWhere(['l.is_delete' => 'N']);

               

        $get =  $query->execute()->fetchAll('assoc') ?: [];
        return $get; 
        // return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLabelsForScan(array $params): array
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
                'labels.split_label_id',
                'real_qty',
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
        //find label from label_no
        else if (isset($params['label_no'])) {
            $query->andWhere(['labels.label_no' => $params['label_no']]);
        }
        //find label from splitLabel
        else if (isset($params['split_label_id'])) {
            $query->andWhere(['split_label_id' => $params['split_label_id']]);
        } 
        
        $query->andWhere(['l.is_delete' => 'N']);

        $getdata = $query->execute()->fetchAll('assoc') ?: [];
        if ($getdata) {
            return  $getdata;
        } else {
            return null;
        }
    }

    public function findLabelNonfullys(array $params): array
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

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
