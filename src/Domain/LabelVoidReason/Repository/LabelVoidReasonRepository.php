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
    public function insertLabelVoidReasonApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('label_void_reasons', $row)->execute()->lastInsertId();
    }

    public function updateLabelVoidReason(int $voidReasonId, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('label_void_reasons', $data)->andWhere(['id' => $voidReasonId])->execute();
    }
    public function updateLabelVoidReasonApi(int $voidReasonId, array $data,  $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] =  $user_id;

        $this->queryFactory->newUpdate('label_void_reasons', $data)->andWhere(['id' => $voidReasonId])->execute();
    }

    public function deleteLabelVoidReason(int $voidReasonId): void
    {
        $this->queryFactory->newDelete('label_void_reasons')->andWhere(['id' => $voidReasonId])->execute();

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

    public function findLabelVoidReasonsForVoidLabel(array $params): array
    {
        $query = $this->queryFactory->newSelect('label_void_reasons');
        $query->select(
            [
                'id',
                'reason_name',
                'description',
            ]
        );

        $query->where(['label_void_reasons.id !=' => '1']);
        $query->where(['label_void_reasons.id !=' => '2']);
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
