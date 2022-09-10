<?php

namespace App\Domain\ReportQtyDif\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReportQtyDifRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function getReportQtyDif(array $params): array #focus that!!! get regrind
    {
    
        $query = $this->queryFactory->newSelect('lots'); #select from table title
        $query->select( [
            
            'PD.part_code',
            'PD.part_no',
            'lots.issue_date',
            'lots.lot_no ', //MFG lot
            'lots.quantity',
            'lots.real_lot_qty',
            // 'diff'=>$query->func()->concat(['lots.real_lot_qty'=>'identifier' ,'-', 'lots.quantity'=>'identifier']),
        ]
        );
        $query->join([
            'PD' =>[
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'lots.product_id=PD.id',
            ]
        ]);
        if ($params["part_id"]!="0") {
            $query->andWhere(['lots.product_id' => $params['part_id']]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['issue_date <=' => $params['endDate'], 'issue_date >=' => $params['startDate']]);
        }
        $query->andWhere(['lots.status' => 'PACKED']);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
