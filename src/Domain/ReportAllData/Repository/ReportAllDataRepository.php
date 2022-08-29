<?php

namespace App\Domain\ReportAllData\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;
//use App\Domain\SparePartStore\Repository\SparePartStoreRepository;

final class ReportAllDataRepository
{

    private $queryFactory;
    private $session;
    private $spare_part_stores;

    public function __construct(Session $session,QueryFactory $queryFactory)//,SparePartStoreRepository $spare_part_stores)
    {
        $this->queryFactory = $queryFactory;
        //$this->spare_part_stores=$spare_part_stores;
        $this->session=$session;
    }

    public function findReportAllData(array $params): array
    {
        $query = $this->queryFactory->newSelect('spare_parts');
        $query->select(
            [
                'spare_parts.id',
                'spare_part_code',
                'spare_part_name',
                'category_id',
                'category_name',
                'last_price',
                'ay.balance_qty AS ay_balance_qty',
                'kk.balance_qty AS kk_balance_qty',
                'image_url',
            ]
        );
        $query->join([
            'c' => [
                'table' => 'spare_categories',
                'type' => 'INNER',
                'conditions' => 'c.id = spare_parts.category_id',
            ]]);
        
        $query->join([
            'ay' => [
                'table' => 'spare_part_stores',
                'type' => 'INNER',
                'conditions' => 'ay.spare_part_id = spare_parts.id AND ay.store_id=1',
            ]]);   
        
        $query->join([
            'kk' => [
                'table' => 'spare_part_stores',
                'type' => 'INNER',
                'conditions' => 'kk.spare_part_id = spare_parts.id AND kk.store_id=2',
            ]]);  
            
        $query->andWhere(['spare_parts.is_delete' => 'N']);
        if(isset($params['search_category_id']) && $params['search_category_id']!=0){
            $query->andWhere(['spare_parts.category_id' => $params['search_category_id']]);
        }        
        if(isset($params['spare_part_id'])){
            $query->andWhere(['spare_parts.is_delete' => 'N','spare_parts.id'=>$params['spare_part_id']]);
        }else if(isset($params['spare_part_code'])){
            $query->andWhere(['spare_parts.is_delete' => 'N','spare_parts.spare_part_code'=>$params['spare_part_code']]);
        }else{
            $query->andWhere(['spare_parts.is_delete' => 'N']);
        }
        if(isset($params['category_id'])){
            $query->andWhere(['spare_parts.category_id' => $params['category_id']]);
        }
        $query->orderAsc('spare_part_code');
        
        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findSparePartData(array $params): array
    {
        $query = $this->queryFactory->newSelect('spare_parts');
        $query->select(
            [
                'spare_parts.id',
                'spare_part_code',
                'spare_part_name',
                'category_id',
                'category_name',
                'ay.balance_qty AS ay_balance_qty',
                'kk.balance_qty AS kk_balance_qty',
                'image_url',
            ]
        );
        $query->join([
            'c' => [
                'table' => 'spare_categories',
                'type' => 'INNER',
                'conditions' => 'c.id = spare_parts.category_id',
            ]]);
        
        $query->join([
            'ay' => [
                'table' => 'spare_part_stores',
                'type' => 'INNER',
                'conditions' => 'ay.spare_part_id = spare_parts.id AND ay.store_id=1',
            ]]);   
        
        $query->join([
            'kk' => [
                'table' => 'spare_part_stores',
                'type' => 'INNER',
                'conditions' => 'kk.spare_part_id = spare_parts.id AND kk.store_id=2',
            ]]);  
        $query->andWhere(['spare_parts.is_delete' => 'N','spare_parts.id'=>$params['spare_part_id']]);
        

        return $query->execute()->fetchAll('assoc') ?: [];
    }
    
    public function findNotInLayoutSpareParts(array $params): array
    {
        $query = $this->queryFactory->newSelect('spare_parts');
        $query->select(
            [
                'spare_parts.id',
                'spare_part_code',
                'spare_part_name',
                'category_id',
                'category_name',
                'image_url',
            ]
        );
        $query->join([
            'c' => [
                'table' => 'spare_categories',
                'type' => 'INNER',
                'conditions' => 'c.id = spare_parts.category_id',
            ]]);
        $query->join([
            'td' => [
                'table' => 'tool_layout_details',
                'type' => 'LEFT',
                'conditions' => 'td.spare_part_id=spare_parts.id AND td.tool_layout_standard_id ='.$params['tool_layout_standard_id'],
            ]]);
        $query->andWhere(['td.tool_layout_standard_id is'=>null,'spare_parts.is_delete' => 'N']);

        return $query->execute()->fetchAll('assoc') ?: [];
    }
    
    public function deleteSparePartById(int $spare_partId): void
    {
        $statement = $this->queryFactory->newDelete('spare_parts')->andWhere(['id' => $spare_partId])->execute();

        if (!$statement->count()) {
            throw new DomainException(sprintf('Cannot delete spare_part: %s', $spare_partId));
        }
    }

    public function insertSparePart(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('spare_parts', $row)->execute()->lastInsertId();
    }

    public function disableSparePart(int $spare_partId): void
    {
        $data['is_delete'] = 'Y';
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('spare_parts', $data)->andWhere(['id' => $spare_partId])->execute();
    }

    public function updateSparePart(int $spare_partId, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('spare_parts', $data)->andWhere(['id' => $spare_partId])->execute();
    }
    
    public function updateSparePartQty(int $spare_partId, array $data): void
    {
        $data1['updated_at'] = Chronos::now()->toDateTimeString();
        $data1['updated_user_id'] = $this->session->get('user')["id"];

        $param1['spare_part_id']=$spare_partId;
        $spare_part_storeAYKK = $this->findSpareParts($param1);

        $data1['balance_qty']=$data['ay_balance_qty'];
        $this->queryFactory->newUpdate('spare_part_stores', $data1)->andWhere(['spare_part_id' => $spare_partId, 'store_id'=>'1'])->execute();

        $data1['balance_qty']=$data['kk_balance_qty'];
        $this->queryFactory->newUpdate('spare_part_stores', $data1)->andWhere(['spare_part_id' => $spare_partId, 'store_id'=>'2'])->execute();
        
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];
        $row['spare_part_id'] = $spare_partId;
        $row['store_id'] = '1';
        $row['operation_type'] = "CHANGE";
        $row['update_qty'] = $data["ay_balance_qty"]-$spare_part_storeAYKK[0]["ay_balance_qty"];
        $row['ref_id'] = $spare_partId;
        $this->queryFactory->newInsert('spare_part_qty_logs', $row)->execute();

        $row['store_id'] = '2';        
        $row['update_qty'] = $data["kk_balance_qty"]-$spare_part_storeAYKK[0]["kk_balance_qty"];
        $this->queryFactory->newInsert('spare_part_qty_logs', $row)->execute();
        return;
    }

    public function transferAy2KkSparePartQty(int $spare_partId, array $data): void
    {
        $data1['updated_at'] = Chronos::now()->toDateTimeString();
        $data1['updated_user_id'] = $this->session->get('user')["id"];
        $data1['balance_qty']=$data['kk_balance_qty'];
        $this->queryFactory->connection->execute('UPDATE spare_part_stores SET balance_qty=balance_qty-'.$data['kk_balance_qty']
            ." WHERE spare_part_id=$spare_partId AND store_id=1");

        $this->queryFactory->connection->execute('UPDATE spare_part_stores SET balance_qty=balance_qty+'.$data['kk_balance_qty']
            ." WHERE spare_part_id=$spare_partId AND store_id=2");

        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];
        $row['spare_part_id'] = $spare_partId;
        $row['store_id'] = '1';
        $row['operation_type'] = "TRANSFER";
        $row['update_qty'] = $data["kk_balance_qty"]*-1;
        $row['ref_id'] = $spare_partId;
        $this->queryFactory->newInsert('spare_part_qty_logs', $row)->execute();

        $row['store_id'] = '2';        
        $row['update_qty'] = $data["kk_balance_qty"];
        $this->queryFactory->newInsert('spare_part_qty_logs', $row)->execute();
        return;
    }
    
    public function transferKk2AySparePartQty(int $spare_partId, array $data): void
    {
        $data1['updated_at'] = Chronos::now()->toDateTimeString();
        $data1['updated_user_id'] = $this->session->get('user')["id"];
        $data1['balance_qty']=$data['ay_balance_qty'];
        $this->queryFactory->connection->execute('UPDATE spare_part_stores SET balance_qty=balance_qty-'.$data['ay_balance_qty']
            ." WHERE spare_part_id=$spare_partId AND store_id=2");

        $this->queryFactory->connection->execute('UPDATE spare_part_stores SET balance_qty=balance_qty+'.$data['ay_balance_qty']
            ." WHERE spare_part_id=$spare_partId AND store_id=1");

        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];
        $row['spare_part_id'] = $spare_partId;
        $row['store_id'] = '2';
        $row['operation_type'] = "TRANSFER";
        $row['update_qty'] = $data["ay_balance_qty"]*-1;
        $row['ref_id'] = $spare_partId;
        $this->queryFactory->newInsert('spare_part_qty_logs', $row)->execute();

        $row['store_id'] = '1';        
        $row['update_qty'] = $data["ay_balance_qty"];
        $this->queryFactory->newInsert('spare_part_qty_logs', $row)->execute();
        return;
    }

    public function existsSparePartId(int $spare_partId): bool
    {
        $query = $this->queryFactory->newSelect('spare_parts');
        $query->select('id')->andWhere(['id' => $spare_partId]);

        return (bool)$query->execute()->fetch('assoc');
    }

    public function updatePrice(string $sparePartCode, $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('spare_parts', $data)->andWhere(['spare_part_code' => $sparePartCode])->execute();
    }
}
