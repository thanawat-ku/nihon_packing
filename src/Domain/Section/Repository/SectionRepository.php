<?php

namespace App\Domain\Section\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SectionRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }
    
    public function findSections(array $params): array
    {
        $query = $this->queryFactory->newSelect('sections');
        $query->select(
            [
                'sections.id',
                'section_name',
                'section_description',
                
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

   

}
