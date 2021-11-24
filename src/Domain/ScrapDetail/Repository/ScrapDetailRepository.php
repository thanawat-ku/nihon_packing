<?php

namespace App\Domain\ScrapDetail\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ScrapDetailRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }
    
    public function insertScrapDetail(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('scrap_details', $row)->execute()->lastInsertId();
    }
    
    public function updateScrapDetail(int $id, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] =  $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('scrap_details', $data)->andWhere(['id' => $id])->execute();
    }
    
    public function insertScrapDetailApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('scrap_details', $row)->execute()->lastInsertId();
    }
    public function updateScrapDetailApi(int $id, array $data,$user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('scrap_details', $data)->andWhere(['id' => $id])->execute();
    } 

    public function deleteScrapDetail(int $id): void
    {
        $this->queryFactory->newDelete('scrap_details')->andWhere(['id' => $id])->execute();
    } 
    public function deleteScrapDetailAll(int $scrapID): void
    {
        $this->queryFactory->newDelete('scrap_details')->andWhere(['scrap_id' => $scrapID])->execute();
    } 
    public function findScrapDetails(array $params): array
    {
        $query = $this->queryFactory->newSelect('scrap_details');
        $query->select(
            [
                'scrap_details.id',
                'scrap_id',
                'section_id',
                'product_id',
                'scrap_details.defect_id',
                'scrap_detail_qty'=>'scrap_details.quantity',
                'part_code',
                'part_name',
                'section_name',
                'defect_qty'=>'d.quantity'
                
            ]
        );

        $query->join([
            'd' => [
                'table' => 'lot_defects',
                'type' => 'INNER',
                'conditions' => 'd.id = scrap_details.defect_id',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.id = scrap_details.product_id',
            ]
        ]);

        $query->join([
            's' => [
                'table' => 'sections',
                'type' => 'INNER',
                'conditions' => 's.id = scrap_details.section_id',
            ]
        ]);

        $query->join([
            'sr' => [
                'table' => 'scraps',
                'type' => 'INNER',
                'conditions' => 'sr.id = scrap_details.scrap_id',
            ]
        ]);
        if(isset($params['scrap_id'])){
            $query->andWhere(['scrap_id' => $params['scrap_id']]);
        }
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

   

}
