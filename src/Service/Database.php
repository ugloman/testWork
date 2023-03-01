<?php

declare(strict_types=1);


namespace App\Service;


use PDO;

class Database
{
    private const HOST = 'postgres';
    private const USER = 'root';
    private const PASSWORD = '123';
    private const DB_NAME = 'user_task';

    private PDO|null $connection;

    public function __construct()
    {
        $dsn = sprintf('pgsql:dbname=%s;host=%s',
            self::DB_NAME,
            self::HOST
        );

        $this->connection = new PDO(
            $dsn,
            self::USER,
            self::PASSWORD
        );
    }

    /**
     * @param string $query
     */
    public function executeQuery(string $query): void
    {
        $this->connection->exec($query);
    }

    /**
     * @param string $query
     * @return array
     */
    public function selectQuery(string $query): array
    {
        $query = $this->connection->query($query);
        $result = $query->fetchAll();

        return $result ?: [];
    }

    public function migrate(): void
    {
        $this->connection->exec('CREATE TABLE IF NOT EXISTS task_user (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL)');
        $this->connection->exec('CREATE TABLE IF NOT EXISTS mail_queue (id SERIAL PRIMARY KEY, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text TEXT NOT NULL, date_sent TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_sent BOOLEAN DEFAULT FALSE)');
        $this->connection->exec('ALTER TABLE mail_queue ADD CONSTRAINT FK_99F1D080D3331C94 FOREIGN KEY (user_id) REFERENCES task_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

}
