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

    public function __construct(Session $session, QueryFactory $queryFactory, QueryFactory2 $queryFactory2)
    {
        $this->queryFactory = $queryFactory;
        $this->queryFactory2 = $queryFactory2;
        $this->session = $session;
    }
    public function insertSell(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        $row['sell_status']="CREATED";
        $row['sell_date']=date('Y-m-d');
        $row['total_qty']=0;


        return (int)$this->queryFactory->newInsert('sells', $row)->execute()->lastInsertId();
    }
    public function insertSellApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        $row['sell_date']= Chronos::now()->toDateTimeString();
        $row['total_qty']=0;
        $row['sell_status']="CREATED";

        return (int)$this->queryFactory->newInsert('sells', $row)->execute()->lastInsertId();
    }
    public function updateSellApi(int $sellID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('sells', $data)->andWhere(['id' => $sellID])->execute();
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
                'part_name',
                'part_code'
            ]
        );

        if(isset($params['sell_id'])){
            $query->andWhere(['sells.id' => $params['sell_id']]);
        }
        if(isset($params['id'])){
            $query->andWhere(['sells.id' => $params['id']]);
        }
        else if (isset($params["startDate"])) {
            $query->andWhere(['sell_date <=' => $params['endDate'], 'sell_date >=' => $params['startDate']]);
        }

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = sells.product_id',
            ]
        ]);


        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findSellRow(int $sellID)
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
                'part_name',
                'part_code'
            ]
        );

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = sells.product_id',
            ]
        ]);

        $query->where(['sells.id' => $sellID]);

        $row = $query->execute()->fetch('assoc');

        if (!$row) {
            return null;
        } else {
            return $row;
        }
        return false;
    }
}
