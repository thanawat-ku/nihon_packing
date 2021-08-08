<?php

namespace App\Domain\MergePack\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ProductRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    

    public function findProducts(array $params): array
    {
        $query = $this->queryFactory->newSelect('products');
        $query->select(
            [
                'products.id',
                'product_code',
                'product_name',
                'price',
                'std_pack',
                'std_box',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
