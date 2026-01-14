<?php
/**
 * Base Model Class
 * All models extend this class for common database operations
 * Uses PDO for secure database connections with prepared statements
 */

require_once __DIR__ . '/../config/database.php';

abstract class Model {
    protected $table;
    protected $primaryKey = 'id';
    protected static $pdo = null;

    /**
     * Get database connection
     * @return PDO|null
     */
    protected function getConnection() {
        if (self::$pdo === null) {
            self::$pdo = getDBConnection();
        }
        return self::$pdo;
    }

    /**
     * Get all records
     * @return array
     */
    public function getAll() {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return [];
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Model getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find record by ID
     * @param mixed $id
     * @return array|null
     */
    public function find($id) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return null;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Model find error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find records by field value
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function findBy($field, $value) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return [];
        }

        try {
            // Whitelist the field name to prevent SQL injection
            $allowedFields = $this->getAllowedFields();
            if (!in_array($field, $allowedFields)) {
                error_log("Invalid field name: " . $field);
                return [];
            }

            $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
            $stmt->execute([$value]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Model findBy error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create new record
     * @param array $data
     * @return int|bool Insert ID or false on failure
     */
    public function create($data) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return false;
        }

        try {
            // Filter data to only include allowed fields
            $allowedFields = $this->getAllowedFields();
            $filteredData = array_intersect_key($data, array_flip($allowedFields));

            if (empty($filteredData)) {
                return false;
            }

            $columns = implode(', ', array_keys($filteredData));
            $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($filteredData));

            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Model create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update record by ID
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return false;
        }

        try {
            // Filter data to only include allowed fields
            $allowedFields = $this->getAllowedFields();
            $filteredData = array_intersect_key($data, array_flip($allowedFields));

            if (empty($filteredData)) {
                return false;
            }

            $setClause = implode(' = ?, ', array_keys($filteredData)) . ' = ?';
            $values = array_values($filteredData);
            $values[] = $id;

            $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Model update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete record by ID
     * @param mixed $id
     * @return bool
     */
    public function delete($id) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return false;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Model delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute a raw query with prepared statements
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function query($sql, $params = []) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return [];
        }

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Model query error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Execute a raw query that returns a single row
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    protected function queryOne($sql, $params = []) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return null;
        }

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Model queryOne error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Count records
     * @param string|null $field
     * @param mixed $value
     * @return int
     */
    public function count($field = null, $value = null) {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return 0;
        }

        try {
            if ($field && $value !== null) {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE {$field} = ?");
                $stmt->execute([$value]);
            } else {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$this->table}");
                $stmt->execute();
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (PDOException $e) {
            error_log("Model count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Validate data
     * @param array $data
     * @param array $rules
     * @return array Array of error messages (empty if valid)
     */
    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);

            foreach ($ruleList as $r) {
                if ($r === 'required' && (!isset($data[$field]) || strlen((string)$data[$field]) === 0)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break;
                }

                if (strpos($r, 'min:') === 0) {
                    $min = (int)substr($r, 4);
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least $min characters";
                        break;
                    }
                }

                if (strpos($r, 'max:') === 0) {
                    $max = (int)substr($r, 4);
                    if (isset($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[$field] = ucfirst($field) . " must not exceed $max characters";
                        break;
                    }
                }

                if ($r === 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email address';
                    break;
                }

                if ($r === 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field] = ucfirst($field) . ' must be a number';
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Get list of allowed fields for this model
     * Override in child classes to specify allowed fields
     * @return array
     */
    protected function getAllowedFields() {
        return [];
    }
}
