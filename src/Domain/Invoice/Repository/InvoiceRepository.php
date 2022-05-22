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
        $this->queryFactory2->newUpdate('invoice', $data)->andWhere(['InvoiceID' => $id])->execute();
    }

    public function updateInvoicePacking(int $id, array $data, $user_id): void
    {
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;
        $this->queryFactory->newUpdate('invoices', $data)->andWhere(['id' => $id])->execute();
    }

    public function insertInvoicePacking(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('invoices', $row)->execute()->lastInsertId();
    }

    public function deleteInvoice(int $id): void
    {
        $this->queryFactory->newDelete('pack_invoices')->andWhere(['id' => $id])->execute();
    }


    public function findInvoice(array $params): array
    {
        $query = $this->queryFactory2->newSelect('invoice');

        if (isset($params['sync'])) {
            $query->select(
                [
                    'invoice.InvoiceNo',
                    'invoice.IssueDate',
                    'invoice.CustomerID',
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

    public function findInvoicePackings(array $params): array
    {
        $query = $this->queryFactory->newSelect('invoices');

        $query->select(
            [
                'invoices.id',
                'invoice_no',
                'customer_id',
                'invoices.date',
                'invoice_status',
                'invoice_id',
                'customer_code',
                'pack_id' => 'p.id',
            ]
        );

        $query->join([
            'c' => [
                'table' => 'customers',
                'type' => 'INNER',
                'conditions' => 'c.id = invoices.customer_id',
            ]
        ]);

        $query->join([
            'p' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 'p.invoice_id = invoices.id',
            ]
        ]);

        $query->orderDesc('invoices.date');

        // InvoiceNo
        if (isset($params['invoice_no'])) {
            $query->andWhere(['invoice_no' => $params['invoice_no']]);
        }
        if (isset($params['packing_id'])) {
            $query->andWhere(['packing_id' => $params['packing_id']]);
        }
        // status of invoice
        if (isset($params['invoice_status'])) {
            $query->andWhere(['invoice_status' => $params['invoice_status']]);
        }
        if (isset($params['search_invoice_status'])) {
            if ($params['search_invoice_status'] != 'ALL') {
                $query->andWhere(['invoice_status' => $params['search_invoice_status']]);
            }
            $query->group('invoices.id');
        }
        if (isset($params['search_customer_id'])) {
            if ($params['search_customer_id'] != 'ALL') {
                $query->andWhere(['c.id' => $params['search_customer_id']]);
            }
        }
        if (isset($params['invoice_id'])) {
            $query->andWhere(['p.invoice_id' => $params['invoice_id']]);
            if (!isset($params['findPack'])) {
                $query->group('invoices.id');
            }
        }
        if (isset($params['InvoiceNo'])) {
            $query->andWhere(['invoices.invoice_no' => $params['InvoiceNo']]);
            $query->andWhere(['invoices.invoice_status' => 'INVOICE']);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findInvoices(array $params): array
    {
        $query = $this->queryFactory->newSelect('invoices');

        $query->select(
            [
                'invoices.id',
                'invoice_no',
                'customer_id',
                'invoices.date',
                'invoice_status',
                'customer_code',
                // 'pack_id' => 'p.id',
            ]
        );

        $query->join([
            'c' => [
                'table' => 'customers',
                'type' => 'INNER',
                'conditions' => 'c.id = invoices.customer_id',
            ]
        ]);

        $query->orderDesc('invoices.date');

        // InvoiceNo
        if (isset($params['invoice_no'])) {
            $query->andWhere(['invoice_no' => $params['invoice_no']]);
        }
        if (isset($params['packing_id'])) {
            $query->andWhere(['packing_id' => $params['packing_id']]);
        }
        // status of invoice
        if (isset($params['invoice_status'])) {
            $query->andWhere(['invoice_status' => $params['invoice_status']]);
        }
        if (isset($params['search_invoice_status'])) {
            if ($params['search_invoice_status'] != 'ALL') {
                $query->andWhere(['invoice_status' => $params['search_invoice_status']]);
            }
            $query->group('invoices.id');
        }
        if (isset($params['search_customer_id'])) {
            if ($params['search_customer_id'] != 'ALL') {
                $query->andWhere(['c.id' => $params['search_customer_id']]);
            }
        }
        if (isset($params['invoice_id'])) {
            $query->andWhere(['p.invoice_id' => $params['invoice_id']]);
            if (!isset($params['findPack'])) {
                $query->group('invoices.id');
            }
        }
        if (isset($params['InvoiceNo'])) {
            $query->andWhere(['invoices.invoice_no' => $params['InvoiceNo']]);
            $query->andWhere(['invoices.invoice_status' => 'INVOICE']);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function findInvoiceDetails(array $params): array
    {
        $query = $this->queryFactory->newSelect('invoices');

        $query->select(
            [
                'invoices.id',
                'invoice_no',
                'invoices.date',
                'invoice_status',
                'invoice_id',
                'pack_no',
                'part_code',
                'total_qty'
            ]
        );

        $query->join([
            'p' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 'p.invoice_id = invoices.id',
            ]
        ]);

        $query->join([
            'pd' => [
                'table' => 'products',
                'type' => 'INNER',
                'conditions' => 'p.product_id = pd.id',
            ]
        ]);

        $query->orderDesc('invoices.date');

        if (isset($params['invoice_id'])) {
            $query->andWhere(['p.invoice_id' => $params['invoice_id']]);
        }

        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function findInvoiceTagAndLabels(array $params): array
    {
        $query = $this->queryFactory->newSelect('invoices');

        $query->select(
            [
                'invoices.id',
                'invoice_no',
                'customer_id',
                'invoices.date',
                'invoice_status',
                'invoice_id',
                'pack_id' => 'p.id',
                'tag_id' => 't.id',
                'tag_no',
                'label_id' => 'pl.label_id',
            ]
        );

        $query->join([
            'p' => [
                'table' => 'packs',
                'type' => 'INNER',
                'conditions' => 'p.invoice_id = invoices.id',
            ]
        ]);

        $query->join([
            't' => [
                'table' => 'tags',
                'type' => 'INNER',
                'conditions' => 't.pack_id = p.id',
            ]
        ]);
        $query->join([
            'pl' => [
                'table' => 'pack_labels',
                'type' => 'INNER',
                'conditions' => 'pl.pack_id = p.id',
            ]
        ]);


        $query->orderAsc('invoices.date');

        if (isset($params['invoice_id'])) {
            $query->andWhere(['p.invoice_id' => $params['invoice_id']]);
            if (isset($params['findTags'])) {
                $query->group('t.id');
            } elseif (isset($params['findLabels'])) {
                $query->group('pl.id');
            }
        }
        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
