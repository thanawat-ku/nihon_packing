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

        return (int)$this->queryFactory2->newInsert('[nsp_pack].[dbo].[packing]', $row)->execute()->lastInsertId();
    }
    public function insertPackingApi(array $row, $user_id): int
    {
        $row['UserID'] = $user_id;

        return (int)$this->queryFactory2->newInsert('[nsp_pack].[dbo].[packing]', $row)->execute()->lastInsertId();
    }
    // public function updatePacking(int $id, array $data): void
    // {
    //     $this->queryFactory2->newUpdate('[nsp_pack].[dbo].[packing]', $data)->andWhere(['PackingID' => $id])->execute();
    // }

    // public function deletePacking(int $id): void
    // {
    //     $this->queryFactory->newDelete('sell_packings')->andWhere(['id' => $id])->execute();
    // }

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
}
