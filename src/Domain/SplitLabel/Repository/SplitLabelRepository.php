<?php

namespace App\Domain\SplitLabel\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SplitLabelRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }
    public function insertSplitLabel(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('split_labels', $row)->execute()->lastInsertId();
    }

    public function insertSplitLabelApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;
        $row['status'] = "PRINTED";

        return (int)$this->queryFactory->newInsert('split_labels', $row)->execute()->lastInsertId();
    }

    public function insertSplitLabelDeatilApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('split_label_details', $row)->execute()->lastInsertId();
    }

    public function insertSplitLabelDetail(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('split_labels', $row)->execute()->lastInsertId();
    }

    public function updateSplitLabel(int $splitLabelID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('split_labels', $data)->andWhere(['id' => $splitLabelID])->execute();
    }

    public function updateSplitLabelApi(int $splitID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('split_labels', $data)->andWhere(['id' => $splitID])->execute();
    }

    public function deleteSplitLabel(int $splitLabelID): void
    {
        $this->queryFactory->newDelete('split_labels')->andWhere(['id' => $splitLabelID])->execute();
    }

    public function findSplitLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('split_labels');
        $query->select(
            [
                'split_labels.id',
                'split_label_no',
                'label_id',
                'split_labels.status',
                'label_no',
                'label_type',
                'l.quantity',
                'merge_pack_id',
                'split_label_id',
                'split_date',
                'l.product_id',
            ]
        );
        $query->join([
            'l' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'l.id = split_labels.label_id',
            ]
        ]);


        if (isset($params['label_id'])) {
            $query->andWhere(['split_labels.label_id' => $params['label_id']]);
        }
        if (isset($params['split_label_id'])) {
            $query->andWhere(['split_labels.id' => $params['split_label_id']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['split_labels.split_date <=' => $params['endDate'], 'split_labels.split_date >=' => $params['startDate']]);
        }
        if (isset($params['search_product_id']) && $params['search_product_id']!=0) {
            $query->andWhere(['l.product_id' => $params['search_product_id']]);
        }
        if (isset($params["search_status"])) {
            if ($params["search_status"] != "ALL") {
                $query->andWhere(['split_labels.status' => $params['search_status']]);
            }
        }

        $query->andWhere(['split_labels.is_delete' => 'N']);
        $query->andWhere(['l.is_delete' => 'N']);

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
