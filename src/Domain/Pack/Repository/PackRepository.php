<?php

namespace App\Domain\Pack\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class PackRepository
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
    public function insertPack(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        $row['pack_status'] = "CREATED";
        $row['pack_date'] = date('Y-m-d');
        $row['total_qty'] = 0;


        return (int)$this->queryFactory->newInsert('packs', $row)->execute()->lastInsertId();
    }
    public function insertPackApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        $row['pack_date'] = Chronos::now()->toDateTimeString();
        $row['total_qty'] = 0;
        $row['pack_status'] = "CREATED";

        return (int)$this->queryFactory->newInsert('packs', $row)->execute()->lastInsertId();
    }
    public function updatePackApi(int $packID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('packs', $data)->andWhere(['id' => $packID])->execute();
    }

    public function deletePack(int $productID): void
    {
        $this->queryFactory->newDelete('packs')->andWhere(['id' => $productID])->execute();
    }


    public function findPacks(array $params): array
    {
        $query = $this->queryFactory->newSelect('packs');

        $query->select(
            [
                'packs.id',
                'pack_no',
                'pack_date',
                'product_id',
                'total_qty',
                'pack_status',
                'part_name',
                'part_code',
                'part_no',
                'std_pack',
                'std_box',
                'invoice_id',
                'packing_id'

            ]
        );

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = packs.product_id',
            ]
        ]);

        $query->andWhere(['packs.is_delete' => 'N']);

        if (isset($params['pack_id'])) {
            $query->andWhere(['packs.id' => $params['pack_id']]);
        }
        if (isset($params['id'])) {
            $query->andWhere(['packs.id' => $params['id']]);
        }
        if (isset($params['ProductID'])) {
            $query->andWhere(['packs.product_id' => $params['ProductID']]);
        }
        if (isset($params['action']) == "REGISTER") {
            $query->where(['OR' => [['pack_status' => 'TAGGED'], ['pack_status' => 'INVOICED'], ['pack_status' => 'COMPLETE']]]);
        }
        if (isset($params['sync'])) {
            $query->andWhere(['packs.pack_status' => "TAGGED"]);
        }
        if (isset($params["startDate"])) {
            $query->andWhere(['pack_date <=' => $params['endDate'], 'pack_date >=' => $params['startDate']]);
        }
        if (isset($params['packing_id'])) {
            $query->andWhere(['packs.packing_id' => $params['packing_id']]);
        }
        if (isset($params['search_pack_status'])) {
            if ($params['search_pack_status'] != 'ALL') {
                $query->andWhere(['packs.pack_status' => $params['search_pack_status']]);
            }
        }
        if (isset($params['search_product_id'])) {
            $query->andWhere(['p.id' => $params['search_product_id']]);
        }


        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findPackInvoices(array $params): array
    {
        $query = $this->queryFactory->newSelect('packs');

        $query->select(
            [
                'packs.id',
                'pack_no',
                'pack_date',
                'product_id',
                'total_qty',
                'pack_status',
                'invoice_id',
                'packing_id'

            ]
        );

        $query->join([
            'i' => [
                'table' => 'invoices',
                'type' => 'INNER',
                'conditions' => 'i.id = packs.invoice_id',
            ]
        ]);

        $query->andWhere(['packs.is_delete' => 'N']);


        if (isset($params['sync'])) {
            $query->andWhere(['packs.pack_status' => "TAGGED"]);
            $query->andWhere(['i.invoice_no' => $params['invoice_no']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findPackRow(int $packID)
    {
        $query = $this->queryFactory->newSelect('packs');
        $query->select(
            [
                'packs.id',
                'pack_no',
                'pack_date',
                'product_id',
                'total_qty',
                'pack_status',
                'p.part_name',
                'p.part_code',
                'p.part_no',
                'p.is_completed',
                'packing_id',
                // 'sci.cpo_item_id'
            ]
        );

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = packs.product_id',
            ]
        ]);

        // $query->join([
        //     'sci' => [
        //         'table' => 'pack_cpo_items',
        //         'type' => 'INNER',
        //         'conditions' => 'packs.id = sci.pack_id',
        //     ]
        // ]);

        if (isset($params['checkPackIvoice'])) {
            $query->andWhere(['packs.packing_id' => $params['packing_id']]);
            $query->andWhere(['packs.pack_status' => 'TAGGED']);
        }

        $query->where(['packs.id' => $packID]);

        $row = $query->execute()->fetch('assoc');

        if (!$row) {
            return null;
        } else {
            return $row;
        }
        return false;
    }

    public function findPackTag(array $params): array
    {
        $query = $this->queryFactory->newSelect('packs');

        $query->select(
            [
                'packs.id',
                'pack_no',
                'pack_date',
                'total_qty',
                'pack_status',
                'invoice_id'

            ]
        );

        $query->join([
            't' => [
                'table' => 'tags',
                'type' => 'INNER',
                'conditions' => 'packs.id = t.pack_id',
            ]
        ]);

        $query->andWhere(['packs.is_delete' => 'N']);

        if (isset($params['pack_id'])) {
            $query->andWhere(['packs.id' => $params['pack_id']]);
        }


        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findPackLabel(array $params): array
    {
        $query = $this->queryFactory->newSelect('packs');

        $query->select(
            [
                'packs.id',
                'pack_no',
                'pack_date',
                'total_qty',
                'pack_status',
                'invoice_id'

            ]
        );

        $query->join([
            'sl' => [
                'table' => 'pack_labels',
                'type' => 'INNER',
                'conditions' => 'packs.id = sl.pack_id',
            ]
        ]);

        $query->andWhere(['packs.is_delete' => 'N']);

        if (isset($params['pack_id'])) {
            $query->andWhere(['packs.id' => $params['pack_id']]);
        }


        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
