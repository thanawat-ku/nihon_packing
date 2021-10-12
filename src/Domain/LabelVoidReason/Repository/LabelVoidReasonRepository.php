<?php

namespace App\Domain\LabelVoidReason\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class LabelVoidReasonRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function insertLabelVoidReason(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('label_void_reasons', $row)->execute()->lastInsertId();
    }

    public function updateLabelVoidReason(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('label_void_reasons', $data)->andWhere(['id' => $lotID])->execute();
    }

    public function deleteLabelVoidReason(int $lotId): void
    {
        $this->queryFactory->newDelete('label_void_reasons')->andWhere(['id' => $lotId])->execute();

    }

    public function findLabelVoidReasons(array $params): array
    {
        $query = $this->queryFactory->newSelect('label_void_reasons');
        $query->select(
            [
                'id',
                'reason_name',
                'description',
            ]
        );
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
