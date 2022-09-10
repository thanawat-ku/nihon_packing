<?php

namespace App\Domain\ReportAllData\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReportAllDataRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function getReportAllData(array $params): array #focus that!!! get regrind
    {
        
        $query = $this->queryFactory->newSelect('packs'); #select from table title
        $query->select( [
            
            'PD.part_code',
            'PD.part_no',
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
            'PD' =>[
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'packs.product_id=PD.id',
            ]
        ]);
        $query->join([
            'PCI' =>[
                'table' => 'pack_cpo_items',
                'type' => 'INNER',
                'conditions' => 'packs.id=PCI.pack_id',
            ]
        ]);
        $query->join([
            'PL' =>[
                'table' => 'pack_labels',
                'type' => 'INNER',
                'conditions' => 'packs.id=PL.pack_id',
            ]
        ]);
        $query->join([
            'L' =>[
                'table' => 'labels',
                'type' => 'INNER',
                'conditions' => 'PL.label_id=L.id',
            ]
        ]);
        $query->join([
            'LT' =>[
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'L.prefer_lot_id=LT.id',
            ]
        ]);
        $query->join([
            'IV' =>[
                'table' => 'invoices',
                'type' => 'LEFT', // get data from left side
                'conditions' => 'packs.invoice_id=IV.id',
            ]
        ]); 
        if ($params["part_id"]!="0") {
            $query->andWhere(['packs.product_id' => $params['part_id']]);
        }
        if ($params["invoice_id"]!="0") {
            $query->andWhere(['packs.invoice_id' => $params['invoice_id']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['issue_date <=' => $params['endDate'], 'issue_date >=' => $params['startDate']]);
        }
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
