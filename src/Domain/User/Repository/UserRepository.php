<?php

namespace App\Domain\User\Repository;

use App\Factory\QueryFactory;
use DomainException;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Session\Session;

final class UserRepository
{
    private $queryFactory;
    private $session;

    public function __construct(Session $session,QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->session=$session;
    }

    public function checkLogin(string $username,string $password)
    {
        $query = $this->queryFactory->newSelect('users');
        $query->select(
            [
                'users.id',
                'username',
                'password',
                'first_name',
                'last_name',
                'user_role_id',
                'locale',
                'enabled',
            ]
        );
        $query->andWhere(['username' => $username]);

        $row = $query->execute()->fetch('assoc');
        if (!$row) {
            //throw new DomainException(sprintf('User not found: %s', $username));
            return null;
        }
        if(password_verify($password,$row["password"])){
            return $row;
        }
        return false;
    }

    public function getUserById(int $userId): array
    {
        $query = $this->queryFactory->newSelect('users');
        $query->select(
            [
                'users.id',
                'username',
                'first_name',
                'last_name',
                'user_role_id',
                'locale',
                'enabled',
            ]
        );
        $query->andWhere(['id' => $userId]);

        $row = $query->execute()->fetch('assoc');

        if (!$row) {
            throw new DomainException(sprintf('User not found: %s', $userId));
        }

        return $row;
    }

    public function findUsers(array $params): array
    {
        $query = $this->queryFactory->newSelect('users');
        $query->select(
            [
                'users.id',
                'username',
                'first_name',
                'last_name',
                'user_role_id',
                'locale',
                'enabled',
            ]
        );

        return $query->execute()->fetchAll('assoc') ?: [];
    }

    public function deleteUserById(int $userId): void
    {
        $statement = $this->queryFactory->newDelete('users')->andWhere(['id' => $userId])->execute();

        if (!$statement->count()) {
            throw new DomainException(sprintf('Cannot delete user: %s', $userId));
        }
    }

    public function insertUser(array $row): int
    {
        $row['created_at'] = Chronos::now()->toDateTimeString();
        $data['created_user_id'] = $this->session->get('user')["id"];
        $row['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        return (int)$this->queryFactory->newInsert('users', $row)->execute()->lastInsertId();
    }

    public function updateUser(int $userId, array $data): void
    {
        $data['updated_at'] = Chronos::now()->toDateTimeString();
        $data['updated_user_id'] = $this->session->get('user')["id"];

        $this->queryFactory->newUpdate('users', $data)->andWhere(['id' => $userId])->execute();
    }

    public function existsUserId(int $userId): bool
    {
        $query = $this->queryFactory->newSelect('users');
        $query->select('id')->andWhere(['id' => $userId]);

        return (bool)$query->execute()->fetch('assoc');
    }
}
