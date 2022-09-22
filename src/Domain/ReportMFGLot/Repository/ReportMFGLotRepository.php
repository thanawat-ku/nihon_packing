<?php

namespace App\Domain\ReportMFGLot\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReportMFGLotRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function getReportMFGLot(array $params): array 
    {

        $query = $this->queryFactory->newSelect('packs'); #select from table title
        $query->select(
            [

                'PD.part_code',
                'PD.part_no',
                'CM.customer_name',
                'IV.invoice_no',
                'packs.pack_date',
                'PCI.cpo_item_id',
                'LT.lot_no',
                'packs.pack_no',
                'L.label_no',
                'L.quantity',
            ]
        );
        $query->join([
            'PD' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'packs.product_id=PD.id',
            ]
        ]);
        $query->join([
            'CM' => [
                'table' => 'customers',
                'type' => 'INNER',
                'conditions' => 'PD.customer_id=CM.id',
            ]
        ]);
        $query->join([
            'PCI' => [
                'table' => 'pack_cpo_items',
                'type' => 'INNER',
                'conditions' => 'packs.id=PCI.pack_id',
            ]
        ]);
        $query->join([
            'PL' => [
                'table' => 'pack_labels',
                'type' => 'INNER',
                'conditions' => 'packs.id=PL.pack_id',
            ]
        ]);
        $query->join([
            'L' => [
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'PL.label_id=L.id',
            ]
        ]);
        $query->join([
            'LT' => [
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'L.prefer_lot_id=LT.id',
            ]
        ]);
        $query->join([
            'IV' => [
                'table' => 'invoices',
                'type' => 'LEFT', // get data from left side
                'conditions' => 'packs.invoice_id=IV.id',
            ]
        ]);
        if ($params["customer_id"] != "0") {
            $query->andWhere(['PD.customer_id' => $params['customer_id']]);
        }
        if ($params["lot_no"] != "") {
            $query->andWhere(['LT.lot_no' => $params['lot_no']]);
        }
        if ($params["invoice_no"] != "") {
            $query->andWhere(['IV.invoice_no' => $params['invoice_no']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['IV.date <=' => $params['endDate'], 'IV.date >=' => $params['startDate']]);
        }
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}