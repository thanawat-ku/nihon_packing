<?php

namespace App\Domain\Scrap\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class ScrapRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session, QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session = $session;
    }

    public function insertScrap(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('scraps', $row)->execute()->lastInsertId();
    }

    public function updateScrap(int $id, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] =  $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('scraps', $data)->andWhere(['id' => $id])->execute();
    }

    public function insertScrapApi(array $row, $user_id): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $row['created_user_id'] = $user_id;
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $row['updated_user_id'] = $user_id;

        return (int)$this->queryFactory->newInsert('scraps', $row)->execute()->lastInsertId();
    }
    public function updateScrapApi(int $id, array $data, $user_id): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $user_id;

        $this->queryFactory->newUpdate('scraps', $data)->andWhere(['id' => $id])->execute();
    }
    public function deleteScrap(int $id): void
    {
        $this->queryFactory->newDelete('scraps')->andWhere(['id' => $id])->execute();
    }

    public function findScraps(array $params): array
    {
        $query = $this->queryFactory->newSelect('scraps');
        $query->select(
            [
                'scraps.id',
                'scrap_no',
                'scrap_date',
                'scrap_status',

            ]
        );
        $query->andWhere(['is_delete' => 'N']);
        
        if (isset($params["startDate"])) {
            $query->andWhere(['scrap_date <=' => $params['endDate'], 'scrap_date >=' => $params['startDate']]);
        }
        if (isset($params['scrap_id'])) {
            $query->andWhere(['scraps.id' => $params['scrap_id']]);
        }


        return $query->execute()->fetchAll('assoc') ?: [];
    }
}
