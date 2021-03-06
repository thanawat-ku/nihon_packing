<?php

namespace App\Domain\SplitLabelDetail\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SplitLabelDetailRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }
    public function insertSplitLabelDetail(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('split_label_details', $row)->execute()->lastInsertId();
    }



    public function insertSplitLabelDetailDeatilApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('split_label_details', $row)->execute()->lastInsertId();
    }


    public function updateSplitLabelDetail(int $splitLabelID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('split_label_details', $data)->andWhere(['id' => $splitLabelID])->execute();
    }

    public function updateSplitLabelDetailApi(int $splitID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('split_label_details', $data)->andWhere(['id' => $splitID])->execute();
    }

    public function deleteSplitLabelDetail(int $splitLabelID): void
    {
        $this->queryFactory->newDelete('split_label_details')->andWhere(['id' => $splitLabelID])->execute();
    }

    public function deleteSplitLabelDetailAll(int $splitLabelID): void
    {
        $this->queryFactory->newDelete('split_label_details')->andWhere(['split_label_id' => $splitLabelID])->execute();
    }

    public function findSplitLabelDetail(array $params): array
    {
        $query = $this->queryFactory->newSelect('split_label_details');
        $query->select(
            [
                'split_label_details.id',
                'split_label_details.split_label_id',
                'split_label_details.label_id',
                'lot_id',
                'label_no',
                'label_type',
                'merge_pack_id',
                'quantity',
                'status',

            ]
        );
        $query->join([
            'l' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'l.id = split_label_details.label_id',
            ]
        ]);

        if (isset($params['split_label_id'])) {
            $query->andWhere(['split_label_details.split_label_id' => $params['split_label_id']]);
        }
        $query->andWhere(['l.is_delete' => 'N']);

        $get = $query->execute()->fetchAll('assoc') ?: [];
        return $get;
    }

    public function findSplitLabelDetailsForscan(array $params): array
    {
        $query = $this->queryFactory->newSelect('split_label_details');
        $query->select(
            [
                'split_label_details.id',
                'split_label_details.split_label_id',
                'split_label_details.label_id',
                'label_no',
                'label_type',
                'merge_pack_id',
                'quantity',
                'status',
                'product_id'

            ]
        );
        $query->join([
            'l' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'l.id = split_label_details.label_id',
            ]
        ]);

        if (isset($params['label_id'])) {
            $query->andWhere(['split_label_details.label_id' => $params['label_id']]);
        } else if (isset($params['label_no'])) {
            $query->andWhere(['label_no' => $params['label_no']]);
        }
        $query->andWhere(['l.is_delete' => 'N']);

        $get = $query->execute()->fetchAll('assoc') ?: [];
        if ($get) {
            return $get;
        } else {
            return null;
        }
    }
}
