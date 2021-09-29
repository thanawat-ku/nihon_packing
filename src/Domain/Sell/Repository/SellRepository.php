<?php

namespace App\Domain\Sell\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SellRepository
{
    private $queryFactory;
    private $queryFactory2;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory,QueryFactory2 $queryFactory2)
    {
        $this->queryFactory = $queryFactory;
        $this->queryFactory2 = $queryFactory2;
        $this->session=$session;
    }
    public function insertSellApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('sells', $row)->execute()->lastInsertId();
    }
    public function updateSell(int $productID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('sells', $data)->andWhere(['id' => $productID])->execute();
    }
    
    public function deleteSell(int $productID): void
    {
        $this->queryFactory->newDelete('sells')->andWhere(['id' => $productID])->execute();
    }
    

    public function findSells(array $params): array
    {
        $query = $this->queryFactory->newSelect('sells');
        
        $query->select(
            [
                'sells.id',
                'sell_no',
                'sell_date',
                'product_id',
                'total_qty',
                'sell_status',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
