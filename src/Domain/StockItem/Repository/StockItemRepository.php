<?php

namespace App\Domain\StockItem\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;


final class StockItemRepository
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

    public function insertStockItem(array $row): int
    {
        $row['ProcessID'] = 0;
        $row['Good'] = 0;
        $row['NG'] = 0;
        $row['Cost'] = 0.0000;
        $row['Box'] = 0;
        $row['OutStockControlID'] = 0;

        return (int)$this->queryFactory2->newInsert('stock_item', $row)->execute()->lastInsertId();
    }

    public function updateStockItem(int $id, array $data): void
    {
        $this->queryFactory2->newUpdate('stock_item', $data)->andWhere(['LotID' => $id, 'ProcessID' => 12])->execute();
    }

    public function findStockItem(array $params): array
    {
        $query = $this->queryFactory2->newSelect('stock_item');

        $query->select(
            [
                'StockControlID',
                'LotID',
                'ProcessID',
                'Quantity',
                'OutStockControlID',

            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
