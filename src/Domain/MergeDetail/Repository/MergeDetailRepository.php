<?php

namespace App\Domain\MergeDetail\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class MergeDetailRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }


    public function insertMerge(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('labels', $row)->execute()->lastInsertId();
    }
    public function updateMerge(int $labelID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('labels', $data)->andWhere(['id' => $labelID])->execute();
    }

    

    public function findMerges(array $params): array
    {
        $query = $this->queryFactory->newSelect('merge_pack_details'); // focus that
        $query->select(
            [
                'merge_pack_details.id',
                'merge_pack_details.merge_pack_id',
                'merge_no',
                // 'product_id',
                // 'part_code',
                // 'part_name',
                // 'label_type',
                'lb.status',
                // 'std_pack',
                'label_no',
                // 'labels.lot_id',
                // 'lots_id',
                'lot_no',
                'lb.quantity'
            ]
        );
        // $query->join([      //focus that!!!!!!!!!!!!
        //     'p' => [
        //         'table' => 'porducts',  //table name
        //         'type' => 'INNER',
        //         'conditions' => 'p.id = merge_pack_details.product_id',
        //     ],
        // ]);
        $query->join([
            'mp' => [
                'table' => 'merge_packs',
                'type' => 'INNER',
                'conditions' => 'mp.id = merge_pack_details.merge_pack_id',
            ]]);
        $query->join([      //focus that!!!!!!!!!!!!
            'lb' => [
                'table' => 'labels',  //table name
                'type' => 'INNER',
                'conditions' => 'lb.id = merge_pack_details.label_id',
            ],
        ]);
        $query->join([      //focus that!!!!!!!!!!!!
            'l' => [
                'table' => 'lots',  //table name
                'type' => 'INNER',
                'conditions' => 'l.id = lb.lot_id',
            ],
        ]);

        // $query->join([
        //     'ln' =>[
        //         'table' => 'lots',
        //         'type' => 'INNER',
        //         'conditions' => 'ln.id = labels.lots_id',
        //     ]]);
        $query->Where(['merge_pack_details.merge_pack_id' => $params["id"]]);

        // if(isset($params['label_id'])){
        //     $query->andWhere(['labels.id'=>$params["label_id"]]);
        // }
        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
