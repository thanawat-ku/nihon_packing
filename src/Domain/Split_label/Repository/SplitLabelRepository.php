<?php

namespace App\Domain\SplitLabel\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SplitLabelRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }
    public function insertSplitLabel(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('split_labels', $row)->execute()->lastInsertId();
    }

    public function insertSplitLabelApi(array $row,$user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('split_labels', $row)->execute()->lastInsertId();
    }

    public function insertSplitLabelDetail(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('split_labels', $row)->execute()->lastInsertId();
    }

    public function updateSplitLabel(int $splitLabelID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('split_labels', $data)->andWhere(['id' => $splitLabelID])->execute();
    }
    
    public function deleteSplitLabel(int $splitLabelID): void
    {
        $this->queryFactory->newDelete('split_labels')->andWhere(['id' => $splitLabelID])->execute();
    }

    public function findSplitLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('split_labels');
        $query->select(
            [
                'id',
                'splitLabel_name',
                'tel_no',
                'address',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}