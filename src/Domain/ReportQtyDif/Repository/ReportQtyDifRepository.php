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
        
        // SELECT P.part_code,P.part_no,LT.lot_no,LT.generate_lot_no,LT.quantity,LT.real_lot_qty,LT.real_lot_qty-LT.quantity AS Diff
        // FROM lots LT 
        // INNER JOIN products P ON LT.product_id=P.id
        // WHERE LT.`status`='PACKED'

        $query = $this->queryFactory->newSelect('lots'); #select from table title
        $query->select( [
            
            'PD.part_code',
            'PD.part_no',
            'lots.lot_no',
            'labels.label_no',
            'lots.generate_lot_no ',
            'labels.quantity',
            'lots.real_lot_qty',
            'lots.real_lot_qty-lots.quantity AS diff',
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
