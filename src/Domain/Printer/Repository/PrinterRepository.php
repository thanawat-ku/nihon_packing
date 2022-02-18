<?php

namespace App\Domain\Printer\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class PrinterRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function insertPrinter(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('printers', $row)->execute()->lastInsertId();
    }

    public function updatePrinter(int $printerID, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('printers', $data)->andWhere(['id' => $printerID])->execute();
    }

    public function deletePrinter(int $printerId): void
    {
        $this->queryFactory->newDelete('printers')->andWhere(['id' => $printerId])->execute();

    }

    public function findPrinters(array $params): array
    {
        $query = $this->queryFactory->newSelect('printers');
        $query->select(
            [
                'id',
                'printer_name',
                'printer_address',
                'printer_type',
            ]
        );
        if (isset($params['printer_type'])) {
            $query->andWhere(['printer_type' => $params['printer_type']]);
        }
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
