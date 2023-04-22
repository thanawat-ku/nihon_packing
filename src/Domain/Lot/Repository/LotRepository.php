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
    public function updateLotNsp(int $lotID, array $data): void
    {
        $this->queryFactory2->newUpdate('lot', $data)->andWhere(['LotID' => $lotID])->execute();
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
                'real_lot_qty',
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

    public function findLotProduct(array $params): array
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
                'real_lot_qty',
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
        if (isset($params['search_product_id'])) {
            $query->andWhere(['p.id' => $params['search_product_id']]);
        }
        if (isset($params["search_status"])) {
            if($params["search_status"]!="ALL"){
                $query->andWhere(['status' => $params['search_status']]);
            }
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['issue_date <=' => $params['endDate'], 'issue_date >=' => $params['startDate']]);
        }

        $query->andWhere(['lots.is_delete' => 'N']);

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLotNsps(array $params): array
    {
        $query = $this->queryFactory2->newSelect('lot');
        $query->select(
            [
                'PackingQty',
                'CurrentQty'
            ]
        );

        if (isset($params['LotID'])) {
            $query->andWhere(['LotID' => $params["LotID"]]);
        }

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
    public function getSyncLots($max_id): array
    {
        $query = $this->queryFactory2->newSelect('lot');
        $query->select(
            [
                'lot.LotID',
                'lot.ProductID',
                'lot.LotNo',
                'StockControlID',
                'lot.IssueDate',
                'lot.CurrentQty',
            ]);
        $query->join([
            's' => [
                'table' => 'stock_item',
                'type' => 'INNER',
                'conditions' => 's.LotID = lot.LotID AND s.ProcessID=12 and s.OutStockControlID=0 and s.StockControlID>'.$max_id,
            ]
        ]);
        $query->join([
            'p' => [
                'table' => 'product',
                'type' => 'INNER',
                'conditions' => 'lot.ProductID=p.ProductID AND p.CustomerID IN (245,243,99)',
            ]
        ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLotsNo(array $params): array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                // 'lots.id' => 'LotsID',
                // 'lots.lot_no' => 'LotsNo',
                'lots.id',
                'lots.lot_no',
            ]
        );

        if(isset($params['LotsID'])){
            $query->andWhere(['lots.id' => $params['LotsID']]);
        }
        if(isset($params['LotsNo'])){
            $query->andWhere(['lots.lot_no ' => $params['LotsNo']]);
        }

        $query->andWhere(['lots.is_delete' => 'N']);
        $query->orderAsc('lot_no');

        return $query->execute()->fetchAll('assoc') ?: [];
    }
    
    public function getLocalMaxLotId():array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                'max_id' => $query->func()->max('stock_control_id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findLotLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('lots');
        $subquery = $this->queryFactory->newSelect('pack_labels');
        $subquery->select(
            [
                
                'pack_labels.label_id',
                'pack_no',
                'invoice_no',
                'po_no',
                'pack_date',
                'pack_status'
            ]
        );
        $subquery->join([
            'p' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 'p.id = pack_labels.pack_id',
            ]
        ]);
        $subquery->join([
            'i' => [
                'table' => 'invoices',
                'type' => 'INNER',
                'conditions' => 'i.id = p.invoice_id',
            ]
        ]);
        $query->select(
            [
                'lots.id',
                'lots.generate_lot_no',
                'lots.lot_no',
                'lb.label_no',
                'lb.quantity',
                'lb.status',
                'sq.pack_no',
                'sq.invoice_no',
                'sq.po_no',
                'sq.pack_date',
                'sq.pack_status'
            ]
        );
        $query->join([
                'lb' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'lb.lot_id = lots.id',
            ]
        ]);
        $query->join([
            'sq' => [
                'table' => $subquery,
                'type' => 'LEFT',
                'conditions' => 'sq.label_id=lb.id',
            ]
        ]);
        $query->where(['OR' => [['lots.lot_no' => $params['lot_no']],['lots.id' => $params['lot_no']]]]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
