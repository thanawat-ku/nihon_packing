<?php

namespace App\Domain\Defect\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class DefectRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    

    public function findDefects(array $params): array
    {
        $query = $this->queryFactory->newSelect('defects');
        $query->select(
            [
                'id',
                'defect_code',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

}
