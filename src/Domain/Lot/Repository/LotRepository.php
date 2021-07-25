<?php

namespace App\Domain\Lot\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LotRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    

    public function findLots(array $params): array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                'lot_no',
                'product_id',
                'quantity',
                'product_code',
                'product_name',
                'std_pack',
                'std_box',
            ]
        );
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = lots.product_id',
            ]]);

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
