<?php

namespace App\Domain\ReportAllExcel\Repository;

use App\Domain\SparePart\Service\SparePartFinder;
use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;
use PDO;

/**
 * Repository.
 */
final class ReportAllExcel
{
    /**
     * @var QueryFactory The query factory
     */
    private $queryFactory;
    private $session;
    private $reportAllExcelFinder;
    private $connection;

    /**
     * The constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     */
    public function __construct(Session $session,QueryFactory $queryFactory,ReportAllExcelFinder $reportAllExcelFinder,PDO $connection)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
        $this->reportAllExcelFinder=$reportAllExcelFinder;
        $this->connection=$connection;
    }



    /**
     * Load data table entries.
     *
     * @param array<mixed> $params The regrind_spare_store
     *
     * @return array<mixed> The list view data
     */
    public function findRegrindSpareStores(array $params): array
    {
        $query = $this->queryFactory->newSelect('regrind_spare_stores');
        $query->select(
            [
                'id',
                'spare_part_id',
                'store_id',
                'balance_qty',
            ]
        );
            

        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function findRegrindSpareStoreData(array $params): array
    {
        $query = $this->queryFactory->newSelect('regrind_spare_stores');
        $query->select(
            [
                'id',
                'spare_part_id',
                'regrind_store_id',
                'balance_qty',
            ]
        );

        $query->andWhere(['spare_part_id'=>$params['spare_part_id'],'regrind_store_id'=>$params["regrind_store_id"]]);
        

        return $query->execute()->fetchAll('assoc') ?: [];
    }
   
    public function deleteRegrindSpareStoreById(int $regrind_spare_store_id): void
    {
        $statement = $this->queryFactory->newDelete('regrind_spare_stores')->andWhere(['id' => $regrind_spare_store_id])->execute();

        if (!$statement->count()) {
            throw new DomainException(sprintf('Cannot delete regrind_spare_store: %s', $regrind_spare_store_id));
        }
    }

    /**
     * Insert regrind_spare_store row.
     *
     * @param array<mixed> $row The regrind_spare_store data
     *
     * @return int The new ID
     */
    public function insertRegrindSpareStore(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('regrind_spare_stores', $row)->execute()->lastInsertId();
    }

    
    public function updateRegrindSpareStore(int $regrind_spare_store_id, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('regrind_spare_stores', $data)->andWhere(['id' => $regrind_spare_store_id])->execute();
    }

    public function updateBalance($data): void
    {
        $data1['updated_at'] = Chronos::now()->toDateTimeString();
        $data1['updated_user_id'] = $this->session->get('user')["id"];
        $update_qty=$data["count_qty"]-$data["cal_qty"];
        $params['spare_part_id']=$data['spare_part_id'];
        $sparePart=$this->sparePartFinder->findSpareParts($params);


        if(count($sparePart)>0){

            $param1['spare_part_id']=$sparePart[0]["id"];
            $param1['store_id']=$data["store_id"];
            $regrind_spare_store = $this->findRegrindSpareStoreData($param1);
            $data1["balance_qty"]=$regrind_spare_store[0]["balance_qty"] + $update_qty;
            $this->queryFactory->newUpdate('regrind_spare_stores', $data1)->andWhere(['spare_part_id' => $sparePart[0]["id"],'store_id'=>$data["store_id"]])->execute();
            $row['created_at'] = $data['count_stock_compare_time'];
            $row['created_user_id'] = $this->session->get('user')["id"];
            $row['updated_at'] = $data['count_stock_compare_time'];
            $row['updated_user_id'] = $this->session->get('user')["id"];
            $row['spare_part_id'] = $sparePart[0]["id"];
            $row['store_id'] = $data["store_id"];
            $row['operation_type'] = "COUNT";
            $row['update_qty'] = $update_qty;
            $row['ref_id'] = $data["ref_id"];
            $this->queryFactory->newInsert('spare_part_qty_logs', $row)->execute();
        }else{
            return;
        }
    }

    public function getReportRegrind(array $params): array #focus that!!! get regrind
    {
        $sql =  " SELECT sp.spare_part_code,sp.spare_part_name,rss.balance_qty,IFNULL(req_qty,0) AS req_qty,IFNULL(ret_qty,0) AS ret_qty";
        $sql .= " FROM regrind_spare_stores rss";
        $sql .= " INNER JOIN spare_parts sp ON sp.category_id=8 AND sp.id=rss.spare_part_id AND rss.regrind_store_id=" . $params['regrind_store_id'];
        $sql .= " LEFT OUTER JOIN (";
        $sql .= "  SELECT spare_part_id,SUM(quantity) AS req_qty";
        $sql .= "  FROM regrind_requests rq ";
        $sql .= "  INNER JOIN regrind_request_details rqd ON rq.id=rqd.regrind_request_id";
        $sql .= "  WHERE DATE(rq.regrind_request_date)>='" . $params['from_date'] . "'";
        $sql .= "    AND DATE(rq.regrind_request_date)<='" . $params['to_date'] . "'";
        $sql .= "    AND rq.regrind_request_status='APPROVED'";
        $sql .= "    AND rq.regrind_store_id=" . $params['regrind_store_id'];
        $sql .= "  GROUP BY spare_part_id) AS req ON req.spare_part_id=rss.spare_part_id";
        $sql .= " LEFT OUTER JOIN (";
        $sql .= "  SELECT spare_part_id,SUM(quantity) AS ret_qty";
        $sql .= "  FROM regrind_returns rt ";
        $sql .= "  INNER JOIN regrind_return_details rtd ON rt.id=rtd.regrind_return_id";
        $sql .= "  WHERE DATE(rt.regrind_return_date)>='" . $params['from_date'] . "'";
        $sql .= "    AND DATE(rt.regrind_return_date)<='" . $params['to_date'] . "'";
        $sql .= "    AND rt.regrind_return_status='APPROVED'";
        $sql .= "    AND rt.regrind_store_id=" . $params['regrind_store_id'];
        $sql .= "  GROUP BY spare_part_id) AS ret ON ret.spare_part_id=rss.spare_part_id";
        $sql .= " ORDER BY sp.spare_part_code";

        return $this->connection->query($sql)->fetchAll() ?: [];
    }
}
