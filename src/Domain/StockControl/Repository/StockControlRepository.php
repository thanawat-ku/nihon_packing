<?php

namespace App\Domain\StockControl\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;


final class StockControlRepository
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

    public function insertStockControl(array $row): int
    {
        $row['SectionID'] = 0;
        $row['VendorID'] = 0;
        $row['DocNum'] = 0;
        $row['issueDate'] = Chronos::now()->toDateTimeString();
        $row['UpdateTime'] = Chronos::now()->toDateTimeString();
        $row['UserID'] = $this->session->get('user')["id"];
        $row['IsPrint'] = 'N';

        return (int)$this->queryFactory2->newInsert('stock_control', $row)->execute()->lastInsertId();
    }

    public function insertStockControlApi(array $row, $user_id): int
    {
        $row['SectionID'] = 0;
        $row['VendorID'] = 0;
        $row['DocNum'] = 0;
        $row['issueDate'] = Chronos::now()->toDateTimeString();
        $row['UpdateTime'] = Chronos::now()->toDateTimeString();
        $row['UserID'] = $user_id;
        $row['IsPrint'] = 'N';

        return (int)$this->queryFactory2->newInsert('stock_control', $row)->execute()->lastInsertId();
    }

    public function findStockControl(array $params): array
    {
        $query = $this->queryFactory2->newSelect('stock_control');

        $query->select(
            [
                'StockControlID',
                'SectionID',
                'VendorID',
                'DocNo',
                'DocNum',
                'issueDate',
                'UpdateTime',
                'UserID',
                'IsPrint',

            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
