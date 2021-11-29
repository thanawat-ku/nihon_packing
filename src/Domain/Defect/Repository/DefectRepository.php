<?php

namespace App\Domain\Defect\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class DefectRepository
{
    private $queryFactory;
    private $queryFactory2;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory,QueryFactory2 $queryFactory2)
    {
        $this->queryFactory = $queryFactory;
        $this->queryFactory2 = $queryFactory2;
        $this->session=$session;
    }

    public function findDefects(array $params): array
    {
        $query = $this->queryFactory->newSelect('defects');
        $query->select(
            [
                'id',
                'defect_code',
                'defect_description',
                'oqc_check',
            ]
        );

        if (isset($params['defect_code'])) {
            $query->andWhere(['defects.defect_code' => $params['defect_code']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function insertDefect(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('defects', $row)->execute()->lastInsertId();
    }

    public function updateDefect(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('defects', $data)->andWhere(['id' => $lotID])->execute();
    }

    public function deleteDefect(int $lotId, array $data): void
    {

        // Insert store
        $this->repository->deleteDefect($lotId);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    
    public function getMaxID(): array
    {
        $query = $this->queryFactory->newSelect('defects');
        $query->select(
            [
                'max_id'=> $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getSyncDefects(int $max_id): array
    {
        $query = $this->queryFactory2->newSelect('causes');
        $query->select(
            [
                'CauseID',
                'CauseName',
                'CauseDesc',
                'OqcCheck',
            ]);
        $query->andWhere(['CauseID >' => $max_id]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getLocalMaxDefectId():array
    {
        $query = $this->queryFactory->newSelect('defects');
        $query->select(
            [
                'max_id' => $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
