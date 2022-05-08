<?php

namespace App\Domain\Tag\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class TagRepository
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

    public function insertTag(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('tags', $row)->execute()->lastInsertId();
    }

    public function insertTagApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('tags', $row)->execute()->lastInsertId();
    }


    public function updateTagApi(int $tagID, array $row, $user_id): void
    {
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('tags', $row)->andWhere(['id' => $tagID])->execute();
    }

    public function updateTagAllFromPackIDApi(int $sellID, array $row, $user_id): void
    {
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('tags', $row)->andWhere(['pack_id' => $sellID])->execute();
    }

    public function registerTag(int $tagID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];
        $data['packed_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('tags', $data)->andWhere(['id' => $tagID])->execute();
    }


    public function confirmTagApi(int $tagID, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;
        $data['printed_user_id'] = $user_id;

        $this->queryFactory->newUpdate('tags', $data)->andWhere(['id' => $tagID])->execute();
    }

    public function updateTag(int $tagID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('tags', $data)->andWhere(['id' => $tagID])->execute();
    }
    public function updateTagPrintFromPackID(int $sellID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('tags', $data)->andWhere(['pack_id' => $sellID])->execute();
    }
    public function updateTagPrintFromPackIDApi(int $sellID, array $data, int $userID): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $userID;

        $this->queryFactory->newUpdate('tags', $data)->andWhere(['pack_id' => $sellID])->execute();
    }

    public function updateTagFronPackID(int $sellID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('tags', $data)->andWhere(['pack_id' => $sellID])->execute();
    }

    public function deleteTag(int $tagID): void
    {
        $this->queryFactory->newDelete('tags')->andWhere(['id' => $tagID])->execute();
    }

    public function findTags(array $params): array
    {
        $query = $this->queryFactory->newSelect('tags');
        $query->select(
            [
                'tags.id',
                'tag_no',
                's_cpo_item.pack_id',
                'pack_no',
                'tags.quantity',
                'box_no',
                'total_box',
                'wait_print',
                'tags.status',
                'part_code',
                'part_name',
                'part_no',
                'pack_status',
                'total_qty',
                's_cpo_item.cpo_item_id'


            ]
        );
        $query->join([
            's' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 's.id = tags.pack_id',
            ]
        ]);
        $query->join([
            's_cpo_item' => [
                'table' => 'pack_cpo_items',
                'type' => 'INNER',
                'conditions' => 's_cpo_item.pack_id = s.id',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = s.product_id',
            ]
        ]);

        if (isset($params['tag_no'])) {
            $query->andWhere(['tag_no' => $params["tag_no"]]);
            // $query->andWhere(['tags.wait_print' => 'Y']);
        }
        if (isset($params['tag_id'])) {
            $query->andWhere(['tags.id' => $params["tag_id"]]);
        }
        if (isset($params['pack_id'])) {
            $query->andWhere(['s_cpo_item.pack_id' => $params["pack_id"]]);
        }

        if (isset($params["startDate"])) {
            $query->andWhere(['issue_date <=' => $params['endDate'], 'issue_date >=' => $params['startDate']]);
        }



        return $query->execute()->fetchAll('assoc') ?: [];
    }
   
    public function findTagSingleTable(array $params): array
    {
        $query = $this->queryFactory->newSelect('tags');
        $query->select(
            [
                'tags.id',
                'tag_no',
                'tags.quantity',
                'box_no',
                'total_box',
                'wait_print',
                'tags.status',

            ]
        );

        if (isset($params['tag_no'])) {
            $query->andWhere(['tag_no' => $params["tag_no"]]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
