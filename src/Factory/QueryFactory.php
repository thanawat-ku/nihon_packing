<?php

namespace App\Factory;

use Cake\Database\Connection;
use Cake\Database\Query;
use RuntimeException;
use UnexpectedValueException;

/**
 * Factory.
 */
final class QueryFactory
{
    /**
     * @var Connection
     */
    public $connection;

    /**
     * The constructor.
     *
     * @param Connection $connection The database connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Create a new query.
     *
     * @return Query The query
     */
    public function selectQuery(): Query
    {
        return $this->connection->SelectQuery();
    }
    public function updateQuery(): Query
    {
        return $this->connection->UpdateQuery ();
    }
    public function deleteQuery(): Query
    {
        return $this->connection->DeleteQuery ();
    }
    public function insertQuery(): Query
    {
        return $this->connection->InsertQuery  ();
    }

    /**
     * Create a new 'select' query for the given table.
     *
     * @param string $table The table name
     *
     * @throws RuntimeException
     *
     * @return Query A new select query
     */
    public function newSelect(string $table): Query
    {
        $query = $this->selectQuery()->from($table);

        if (!$query instanceof Query) {
            throw new UnexpectedValueException('Failed to create query');
        }

        return $query;
    }

    /**
     * Create an 'update' statement for the given table.
     *
     * @param string $table The table to update rows from
     * @param array<mixed> $data The values to be updated
     *
     * @return Query The new update query
     */
    public function newUpdate(string $table, array $data): Query
    {
        return $this->updateQuery()->update($table)->set($data);
    }

    /**
     * Create an 'update' statement for the given table.
     *
     * @param string $table The table to update rows from
     * @param array<mixed> $data The values to be updated
     *
     * @return Query The new insert query
     */
    public function newInsert(string $table, array $data): Query
    {
        return $this->insertQuery()->insert(array_keys($data))
            ->into($table)
            ->values($data);
    }

    /**
     * Create a 'delete' query for the given table.
     *
     * @param string $table The table to delete from
     *
     * @return Query A new delete query
     */
    public function newDelete(string $table): Query
    {
        return $this->deleteQuery()->delete($table);
    }
}
