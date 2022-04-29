<?php

namespace App\Domain\Lot\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LotRepository
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

    public function insertLot(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('lots', $row)->execute()->lastInsertId();
    }

    public function registerLotApi(int $lotID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;
        $data['packed_user_id'] = $user_id;

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }

    public function updateLotApi(int $lotID, array $data ,$user_id): void
    {
        $data['updated_user_id'] = $user_id;
        $data['packed_user_id'] = $user_id;

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }

    public function registerLot(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];
        $data['packed_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }
    

    public function confirmLotApi(int $lotID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;
        $data['printed_user_id'] = $user_id;

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }

    public function updateLot(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }
    public function printLot(int $lotID,array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];
        $data['printed_user_id'] = $this->session->get('user')["id"];
        $data['status'] = "PRINTED";

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }
    public function deleteLot(int $lotID): void
    {
        $this->queryFactory->newDelete('lots')->andWhere(['id' => $lotID])->execute();
    }

    public function findLots(array $params): array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                'lots.id',
                'lot_no',
                'generate_lot_no',
                'product_id',
                'quantity',
                'part_code',
                'part_no',
                'part_name',
                'std_pack',
                'std_box',
                'status',
                'real_qty',
                'issue_date',

            ]
        );
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = lots.product_id',
            ]
        ]);

        if (isset($params['lot_id'])) {
            $query->andWhere(['lots.id' => $params["lot_id"]]);
        }
        else if (isset($params['lot_no'])) {
            $query->andWhere(['lot_no' => $params['lot_no']]);
        }
        else if (isset($params["startDate"])) {
            $query->andWhere(['issue_date <=' => $params['endDate'], 'issue_date >=' => $params['startDate']]);
        }

        $query->andWhere(['lots.is_delete' => 'N']);

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLotsSingleTalbe(array $params): array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                'lots.id',
                'lot_no',
                'generate_lot_no',
                'product_id',
                'quantity',
                'status',
                'real_qty',
                'issue_date',
            ]
        );
        if (isset($params['lot_id'])) {
            $query->andWhere(['lots.id' => $params["lot_id"]]);
        }
        if (isset($params['lot_no'])) {
            $query->andWhere(['lot_no' => $params['lot_no']]);
        }
        $query->andWhere(['lots.is_delete' => 'N']);

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function getMaxID(): array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                'max_id'=> $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getSyncLots(): array
    {
        $query = $this->queryFactory2->newSelect('lot');
        $query->select(
            [
                'lot.LotID',
                'lot.ProductID',
                'lot.LotNo',
                'lot.IssueDate',
                'lot.CurrentQty',
            ]);
        $query->join([
            'p' => [
                'table' => 'lot_process',
                'type' => 'INNER',
                'conditions' => 'p.LotID = lot.LotID AND p.ProcessID=12',
            ]
        ]);
        $query->join([
            'b' => [
                'table' => 'lot_process',
                'type' => 'INNER',
                'conditions' => 'b.LotID = lot.LotID AND b.SeqNo=p.SeqNo-1 AND lot.ProcessID=b.ProcessID',
            ]
        ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getLocalMaxLotId():array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                'max_id' => $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
