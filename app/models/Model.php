<?php
/**
 * Base Model Class
 * All models extend this class
 */

require_once __DIR__ . '/../Database.php';

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $timestamps = true;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Get all records
     */
    public function all($orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($sql);
    }

    /**
     * Find records with conditions
     */
    public function where($conditions, $orderBy = null, $limit = null) {
        $where = [];
        $params = [];

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                // Handle IN clause
                $placeholders = str_repeat('?,', count($value) - 1) . '?';
                $where[] = "{$field} IN ({$placeholders})";
                $params = array_merge($params, $value);
            } else {
                $where[] = "{$field} = ?";
                $params[] = $value;
            }
        }

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Find one record with conditions
     */
    public function whereOne($conditions) {
        $where = [];
        $params = [];

        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = ?";
            $params[] = $value;
        }

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . " LIMIT 1";
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Insert new record
     */
    public function create($data) {
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $fields = array_keys($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";

        $this->db->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $set = [];
        $params = [];

        foreach ($data as $field => $value) {
            $set[] = "{$field} = ?";
            $params[] = $value;
        }

        $params[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, $params);
    }

    /**
     * Delete record
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$id]);
    }

    /**
     * Soft delete (if deleted_at column exists)
     */
    public function softDelete($id) {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $result = $this->db->fetchOne($sql);
        } else {
            $where = [];
            $params = [];

            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $params[] = $value;
            }

            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE " . implode(' AND ', $where);
            $result = $this->db->fetchOne($sql, $params);
        }

        return $result['count'] ?? 0;
    }

    /**
     * Execute raw query
     */
    public function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }

    /**
     * Fetch all from raw query
     */
    public function fetchAll($sql, $params = []) {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Fetch one from raw query
     */
    public function fetchOne($sql, $params = []) {
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->rollback();
    }
}
