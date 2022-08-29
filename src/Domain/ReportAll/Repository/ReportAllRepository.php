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
    
    // public function insertReportAll(array $row): int
    // {
    //     $row['created_at'] = Chronos::now()->toDateTimeString();
    //     $row['created_user_id'] = $this->session->get('user')["id"];
    //     $row['updated_at'] = Chronos::now()->toDateTimeString();
    //     $row['updated_user_id'] = $this->session->get('user')["id"];

    //     return (int)$this->queryFactory->newInsert('reportAll', $row)->execute()->lastInsertId();
    // }
    
    public function findReportAll(array $params): array
    {
        $query = $this->queryFactory->newSelect('products');
        $query->select(
            [
                'id',
                'part_code'
            ]
        );
        if(isset($params['product_id'])){
            $query->andWhere(['id'=>$params['product_id']]);
        }
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
