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

    public function insertLotDefectApi(array $row,$user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('lot_defects', $row)->execute()->lastInsertId();
    }
    public function updateLotDefectApi(int $id, array $data,$user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('lot_defects', $data)->andWhere(['id' => $id])->execute();
    } 
    public function deleteLotDefect(int $id): void
    {
        $this->queryFactory->newDelete('lot_defects')->andWhere(['id' => $id])->execute();
    } 

}
