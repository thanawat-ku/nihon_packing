<?php

namespace App\Domain\Section\Repository;

use App\Factory\QueryFactory;
use App\Factory\QueryFactory2;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class SectionRepository
{
    private $queryFactory;
    private $queryFactory2;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory,QueryFactory2 $queryFactory2)
    {
        $this->queryFactory = $queryFactory;
        $this->queryFactory2 = $queryFactory2;
        $this->session=$session;
    }
    
    public function insertSection(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('sections', $row)->execute()->lastInsertId();
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

    
    public function getMaxID(): array
    {
        $query = $this->queryFactory->newSelect('sections');
        $query->select(
            [
                'max_id'=> $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getSyncSections(int $max_id): array
    {
        $query = $this->queryFactory2->newSelect('sections');
        $query->select(
            [
                'SectionID',
                'SectionName',
                'SectionDesc',
                'IsVendor',
                'IsScrap',
            ]);
        $query->andWhere(['SectionID >' => $max_id]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }
    public function getLocalMaxSectionId():array
    {
        $query = $this->queryFactory->newSelect('sections');
        $query->select(
            [
                'max_id' => $query->func()->max('id'),
            ]);
        return $query->execute()->fetchAll('assoc') ?: [];
    }

   

}
