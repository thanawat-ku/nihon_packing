<?php

namespace App\Domain\Defect\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class DefectRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function findDefects(array $params): array
    {
        $query = $this->queryFactory->newSelect('defects');
        $query->select(
            [
                'id',
                'defect_code',
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

        return (int)$this->queryFactory->newInsert('lot_defects', $row)->execute()->lastInsertId();
    }

    public function updateDefect(int $lotID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('lot_defects', $data)->andWhere(['id' => $lotID])->execute();
    }

    public function deleteDefect(int $lotId, array $data): void
    {

        // Insert store
        $this->repository->deleteDefect($lotId);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

}
