<?php

namespace App\Domain\SelectProductToMerge\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SelectProductToMergeRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    

    public function findSelectProductToMerges(array $params): array
    {
        $query = $this->queryFactory->newSelect('products');
        $query->select(
            [
                'products.id',
                // 'product_id',
                'part_code',
                'part_name',
                // 'price',
                'std_pack',
                'std_box',

            ]
        );

        // $query->join([
        //     'mp' => [
        //         'tabel' => 'merge_packs',
        //         'type' => 'INNER',
        //         'conditions' => 'product.id = mp.product_id'

        //     ]
        // ]);

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
