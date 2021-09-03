<?php

namespace App\Domain\Merge\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class MergeRepository
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
        $query = $this->queryFactory->newSelect('merge_packs'); // focus that
        $query->select(
            [
                'merge_packs.id',
                
                'merge_no',
                'product_id',
                'product_code',
                'product_name',
                'merge_status',
                'std_pack',
                'quantity',
                
            ]
        );
        // $query->join([      //focus that!!!!!!!!!!!!
        //     'm' => [
        //         'table' => 'merge_packs',  //table name
        //         'type' => 'INNER',
        //         'conditions' => 'm.id = labels.merge_pack_id',
        //     ],
        // ]);
        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = merge_packs.product_id',
            ]]);

        if(isset($params['label_id'])){
            $query->andWhere(['labels.id'=>$params["label_id"]]);
        }
        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
