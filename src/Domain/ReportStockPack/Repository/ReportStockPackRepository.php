<?php

namespace App\Domain\ReportStockPack\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReportStockPackRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function getReportStockPack(array $params): array #focus that!!! get regrind
    {
        
        $query = $this->queryFactory->newSelect('labels'); #select from table title
        $query->select( [
            
            'PD.part_code',
            'PD.part_no',
            'LT.lot_no', //MFG lot no
            'labels.label_no',
            'LT.generate_lot_no', //pack lot No
            'labels.quantity',
            'LT.issue_date', //pack lot No
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
        if ($params["part_id"]!="0") {
            $query->andWhere(['LT.product_id' => $params['part_id']]);
        }
        if ($params["lot_no"] != "") {
            $query->andWhere(['LT.lot_no' => $params['lot_no']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['issue_date <=' => $params['endDate'], 'issue_date >=' => $params['startDate']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['LT.issue_date <=' => $params['endDate'], 'LT.issue_date >=' => $params['startDate']]);
        }
        $query->andWhere(['labels.status' => 'PACKED']);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
