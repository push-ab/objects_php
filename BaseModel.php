<?php

require_once __DIR__ . '/DatabaseWrapper.php';

abstract class BaseModel implements DatabaseWrapper
{
    protected PDO $db;
    protected string $tableName;
    
    public function __construct(PDO $db, string $tableName)
    {
        $this->db = $db;
        $this->tableName = $tableName;
    }
    
    public function insert(array $tableColumns, array $values): array
    {
        $columns = implode(', ', $tableColumns);
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        
        $sql = "INSERT INTO {$this->tableName} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        
        $id = $this->db->lastInsertId();
        return $this->find((int)$id);
    }
    
    public function update(int $id, array $values): array
    {
        $setParts = [];
        $params = [];
        
        foreach ($values as $column => $value) {
            $setParts[] = "{$column} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        
        $setClause = implode(', ', $setParts);
        $sql = "UPDATE {$this->tableName} SET {$setClause} WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $this->find($id);
    }
    
    public function find(int $id): array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->tableName}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
