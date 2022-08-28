<?php

namespace App\Domain\ReportAll\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReportAllRepository
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
    
    public function insertReportAll(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('ReportAlls', $row)->execute()->lastInsertId();
    }
    
    public function findReportAlls(array $params): array
    {
        $query = $this->queryFactory->newSelect('ReportAlls');
        $query->select(
            [
                'ReportAlls.id',
                'ReportAll_name',
                'ReportAll_description',
                'is_vendor',
                'is_scrap'
                
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    
    public function getMaxID(): array
    {
        $query = $this->queryFactory->newSelect('ReportAlls');
        $query->select(
            [
                'max_id'=> $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getSyncReportAlls(int $max_id): array
    {
        $query = $this->queryFactory2->newSelect('ReportAlls');
        $query->select(
            [
                'ReportAllID',
                'ReportAllName',
                'ReportAllDesc',
                'IsVendor',
                'IsScrap',
            ]);
        $query->andWhere(['ReportAllID >' => $max_id]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getLocalMaxReportAllId():array
    {
        $query = $this->queryFactory->newSelect('ReportAlls');
        $query->select(
            [
                'max_id' => $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

   

}
