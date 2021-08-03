<?php

namespace App\Domain\LotDefect\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LotDefectRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    

    public function findLotDefects(array $params): array
    {
        $query = $this->queryFactory->newSelect('lot_defects');
        $query->select(
            [
                'lot_defects.id',
                'lot_id',
                'defect_id',
                'defect_code',
                'quantity',
            ]
        );

        $query->join([
            'd' => [
                'table' => 'defects',
                'type' => 'INNER',
                'conditions' => 'd.id = lot_defects.defect_id',
            ]
        ]);

        $query->andWhere(['lot_id' => $params['lot_id']]);

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
