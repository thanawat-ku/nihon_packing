<?php

namespace App\Domain\Invoice\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;


final class InvoiceRepository
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
    public function updateInvoice(int $id, array $data): void
    {
        $this->queryFactory2->newUpdate('[nsp_pack].[dbo].[invoice]', $data)->andWhere(['InvoiceID' => $id])->execute();
    }

    public function deleteInvoice(int $id): void
    {
        $this->queryFactory->newDelete('sell_invoices')->andWhere(['id' => $id])->execute();
    }


    public function findInvoice(array $params): array
    {
        $query = $this->queryFactory2->newSelect('invoice');

        if (isset($params['sync'])) {
            $query->select(
                [
                    'invoice.InvoiceNo',
                    'pt.PackingID'

                ]
            );
        } else {
            $query->select(
                [
                    'pt.PackingID'

                ]
            );
        }



        $query->join([
            'it' => [
                'table' => 'invoice_item',
                'type' => 'INNER',
                'conditions' => 'invoice.InvoiceID = it.InvoiceID',
            ]
        ]);

        $query->join([
            'pt' => [
                'table' => 'packing_item',
                'type' => 'INNER',
                'conditions' => 'it.InvoiceItemID = pt.InvoiceItemID',
            ]
        ]);

        if (isset($params['InvoiceNo'])) {
            $query->andWhere(['invoice.InvoiceNo' => $params['InvoiceNo']]);
            $query->group('pt.PackingID');
        }
        if (isset($params['sync'])) {
            $query->andWhere(['pt.PackingID' => $params['packing_id']]);
        }




        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
