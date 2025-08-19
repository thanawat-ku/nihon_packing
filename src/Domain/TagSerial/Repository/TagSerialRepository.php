<?php

namespace App\Domain\TagSerial\Repository;

use App\Factory\QueryFactory;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class TagSerialRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function insertTagSerial(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('tag_serials', $row)->execute()->lastInsertId();
    }

    public function updateTagSerial(int $id, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] =  $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('tag_serials', $data)->andWhere(['id' => $id])->execute();
    }

    public function deleteTagSerial(int $id): void
    {
        $this->queryFactory->newDelete('tag_serials')->andWhere(['id' => $id])->execute();
    }

    public function findTagSerials(array $params): array
    {
        $query = $this->queryFactory->newSelect('tag_serials');
        $query->select(
            [
                'scraps.id',
                'tag_id',
                'customer_id',
                'serial_no',

            ]
        );
        if (isset($params['tag_serial_id'])) {
            $query->andWhere(['tag_serials.id' => $params['tag_serial_id']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function getMaxNo($customerId,$ym): array
    {
        $query = $this->queryFactory->newSelect('tag_serials');
        $query->select(
            [
                'MaxNo' => $query->func()->max('serial_no')
            ]
        );
        $query->andWhere(['customer_id' => $customerId,'serial_no LIKE' => "$ym%"]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
