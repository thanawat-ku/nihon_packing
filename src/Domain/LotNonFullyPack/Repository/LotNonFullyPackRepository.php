<?php

namespace App\Domain\LotNonFullyPack\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LotNonFullyPackRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function insertLotNonFullyPack(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('lot_non_fully_packs', $row)->execute()->lastInsertId();
    }

    public function insertLotNonFullyPackApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('lot_non_fully_packs', $row)->execute()->lastInsertId();
    }

    public function updateLotNonFullyPack(int $labelID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('lot_non_fully_packs', $data)->andWhere(['label_id' => $labelID])->execute();
    }
    // public function deleteLotNonFullyPackApi(int $labelID): void
    // {
    //     $this->queryFactory->newDelete('labels')->andWhere(['id' => $labelID])->execute();
    // }

    public function deleteLotNonFullyPack(int $labelId): void
    {
        $this->queryFactory->newDelete('lot_non_fully_packs')->andWhere(['label_id' => $labelId])->execute();
    }

    public function deleteLotNonFullyPackAll(int $lotID): void
    {
        $this->queryFactory->newDelete('lot_non_fully_packs')->andWhere(['lot_id' => $lotID])->execute();
    }

    public function findLotNonFullyPacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('lot_non_fully_packs');


        $query->select(
            [
                'lot_non_fully_packs.id',
                'lot_non_fully_packs.lot_id',
                'lb.label_no',
                'l.generate_lot_no',
                'lb.label_type',
                'l.real_qty',
                'l.real_lot_qty',
                'lb.wait_print',
                'label_id',
                'date',
                'is_register',
                'lb.quantity',
                'lot_id_in_label' => 'lb.lot_id',


            ]
        );

        $query->join(
            [
                'lb' => [
                    'table' => 'labels',
                    'type' => 'INNER',
                    'conditions' => 'lb.id = lot_non_fully_packs.label_id',
                ]
            ]
        );
        //เนื่องจากในระบบยังมีการทำงานของ label ที่ยังเป็น MERGE_NONFULY และ MERGE_FULLY อยู่ จึงมีการใช้ prefer_lot_id join
        if (isset($params['search_prefer_lot_id'])) {
            $query->join(
                [
                    'l' => [
                        'table' => 'lots',
                        'type' => 'INNER',
                        'conditions' => 'l.id = lb.prefer_lot_id',
                    ]
                ]
            );
        } else {
            $query->join(
                [
                    'l' => [
                        'table' => 'lots',
                        'type' => 'INNER',
                        'conditions' => 'l.id = lb.lot_id',
                    ]
                ]
            );
        }


        if (isset($params['prefer_lot_id'])) {
            $query->andWhere(['lot_non_fully_packs.lot_id' => $params['prefer_lot_id']]);
            $query->andWhere(['lot_non_fully_packs.is_register' => 'N']);
        }
        if (isset($params['lot_id'])) {
            $query->andWhere(['lot_non_fully_packs.lot_id' => $params['lot_id']]);
            $query->andWhere(['lot_non_fully_packs.is_register' => 'N']);
        }
        if (isset($params['label_no'])) {
            $query->andWhere(['lb.label_no' => $params['label_no']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    //เอาไว้ตรวจสอบว่ามี label ที่ทำการ merge ซ้ำกันหรือไม่ ในกรณีที่ทำพร้อมกัน 2 คน
    public function checkLabelInLotNonFullyPacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('lot_non_fully_packs');


        $query->select(
            [
                'lot_non_fully_packs.id',
                'lot_non_fully_packs.lot_id',
                'label_id',
                'date',
                'is_register',
            ]
        );

        if (isset($params['label_id'])) {
            $query->andWhere(['label_id' => $params['label_id']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
