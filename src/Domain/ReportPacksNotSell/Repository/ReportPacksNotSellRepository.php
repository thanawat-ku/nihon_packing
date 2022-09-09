<?php

namespace App\Domain\ReportPacksNotSell\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReportPacksNotSellRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function getReportPacksNotSell(array $params): array #focus that!!! get regrind
    {
        
        $query = $this->queryFactory->newSelect('labels'); #select from table title
        $query->select( [
            
            'PD.part_code',
            'PD.part_no',
            'LT.lot_no',
            'labels.label_no',
            'LT.generate_lot_no',
            'labels.quantity',
        ]
        );
        $query->join([
            'LT' =>[
                'table' => 'lots',
                'type' => 'INNER',
                'conditions' => 'labels.lot_id=LT.id',
            ]
        ]);
        $query->join([
            'PD' =>[
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'LT.product_id=PD.id',
            ]
        ]);
        // $query->join([
        //     'PCI' =>[
        //         'table' => 'pack_cpo_items',
        //         'type' => 'INNER',
        //         'conditions' => 'packs.id=PCI.pack_id',
        //     ]
        // ]);
        // $query->join([
        //     'PL' =>[
        //         'table' => 'pack_labels',
        //         'type' => 'INNER',
        //         'conditions' => 'packs.id=PL.pack_id',
        //     ]
        // ]);
        // $query->join([
        //     'L' =>[
        //         'table' => 'labels',
        //         'type' => 'INNER',
        //         'conditions' => 'PL.label_id=L.id',
        //     ]
        // ]);
        // $query->join([
        //     'IV' =>[
        //         'table' => 'invoices',
        //         'type' => 'INNER',
        //         'conditions' => 'packs.invoice_id=IV.id',
        //     ]
        // ]); 
        if ($params["part_id"]!="0") {
            $query->andWhere(['LT.product_id' => $params['part_id']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['issue_date <=' => $params['endDate'], 'issue_date >=' => $params['startDate']]);
        }
        $query->andWhere(['labels.status' => 'PACKED']);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
