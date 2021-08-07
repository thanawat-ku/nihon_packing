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

    public function insertLot(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('lots', $row)->execute()->lastInsertId();
    }
    
    public function updateLot(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }    
    public function printLot(int $lotID): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];
        $data['printed_user_id'] = $this->session->get('user')["id"];
        $data['status'] = "PRINTED";

        $this->queryFactory->newUpdate('lots', $data)->andWhere(['id' => $lotID])->execute();
    }
    public function deleteLot(int $lotID): void
    {
        $this->queryFactory->newDelete('lots')->andWhere(['id' => $lotID])->execute();
    }

    public function findLots(array $params): array
    {
        $query = $this->queryFactory->newSelect('lots');
        $query->select(
            [
                'lots.id',
                'lot_no',
                'product_id',
                'quantity',
                'product_code',
                'product_name',
                'std_pack',
                'std_box',
                'status',
            ]
        );
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = lots.product_id',
            ]]);

        if(isset($params['lot_id'])){
            $query->andWhere(['lots.id'=>$params["lot_id"]]);
        }
        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
