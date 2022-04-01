<?php

namespace App\Domain\Packing\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Symfony\Component\HttpFoundation\Session\Session;


final class PackingRepository
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
    public function insertPacking(array $row): int
    {
        $row['UserID'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory2->newInsert('packing', $row)->execute()->lastInsertId();
    }
    public function insertPackingApi(array $row, $user_id): int
    {
        $row['UserID'] = $user_id;
        return (int)$this->queryFactory2->newInsert('packing', $row)->execute()->lastInsertId();
    }
    public function deletePacking(int $id): void
    {
        $this->queryFactory2->newDelete('packing')->andWhere(['PackingID' => $id])->execute();
    }

    public function findPacking(array $params): array
    {
        $query = $this->queryFactory2->newSelect('packing');

        $query->select(
            [
                'PackingID',
                'PackingNo',
                'PackingNum',
                'IssueDate',
                'UpdateTime',
                'UserID',
            ]
        );

        $query->orderDesc('PackingID');

        if (isset($params["startDate"])) {
            $query->andWhere(['IssueDate <=' => $params['endDate'], 'IssueDate >=' => $params['startDate']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findPackingItem(array $params): array
    {
        $query = $this->queryFactory2->newSelect('packing');

        $query->select(
            [
                'packing.PackingID',
                'PackingNo',
                'PackingNum',
                'CpoItemID',
                'Quantity'

            ]
        );

        $query->join([
            'pt' => [
                'table' => 'packing_item',
                'type' => 'INNER',
                'conditions' => 'packing.PackingID = pt.PackingID',
            ]
        ]);

        if (isset($params["PackingID"])) {
            $query->andWhere(['packing.PackingID' => $params['PackingID']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
