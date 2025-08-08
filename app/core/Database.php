<?php 

namespace app\core;

use Exception;
use PDO;

class Database {
    private ?PDO $conn = null;

    public function connect(): void
    {
        if ($this->conn instanceof PDO) {
            return; 
        }

        $dbDriver = Config::get('db_driver');
        $dbHost = Config::get('db_host');
        $dbName = Config::get('db_name');
        $dbUser = Config::get('db_user');
        $dbPass = Config::get('db_pass');
        $dbPort = Config::get('db_port');
        $dbChar = Config::get('db_char');

        try {
            $dsn =              $dbDriver;
            $dsn .= ':host='    . $dbHost;
            $dsn .= ';dbname='  . $dbName;
            $dsn .= ';port='    . $dbPort;
            $dsn .= ';charset=' . $dbChar;

            $pdo = new PDO($dsn, $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $this->conn = $pdo;

        } catch (Exception $e) {
            throw new Exception('Database connection failed. ' . $e->getMessage());
        }
    }

    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($table);
    }

    public function query(string $sql, $params = [])
    {
        $this->ensureConnected();
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        $queryType = strtoupper(strtok(trim($sql), ' '));

        switch ($queryType) {
            case 'SELECT':
                return $stmt->fetchAll();
            case 'INSERT':
                return $this->conn->lastInsertId();
            case 'UPDATE':
            case 'DELETE':
                return $stmt->rowCount();
            default: 
                return true;
        }
    }

    public function execute(string $sql, array $params = []): bool
    {
        $this->ensureConnected();

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function raw(string $sql, array $params = []): array
    {
        return $this->query($sql, $params);
    }


    public function lastInsertId(): string 
    {
        $this->ensureConnected();
        return $this->conn->lastInsertId();
    }

    public function beginTransaction(): void
    {
        $this->ensureConnected();
        $this->conn->beginTransaction();
    }

    public function commit(): void
    {
        $this->ensureConnected();
        $this->conn->commit();
    }

    public function rollback(): void 
    {
        $this->ensureConnected();
        $this->conn->rollBack();
    }

    public function closeConnection(): void 
    {   
        $this->ensureConnected();
        $this->conn = null;
    }

    private function ensureConnected(): void 
    {
        if (!$this->conn instanceof PDO) {
            throw new Exception('No active database connection. Please call connect() first.');
        }
    }

}