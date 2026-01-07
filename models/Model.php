<?php
/**
 * Base Model Class
 * All models extend this class for common database operations
 * Currently uses session/arrays for data storage, will be updated to use database
 */
abstract class Model {
    protected $table;
    protected $primaryKey = 'id';

    /**
     * Get database connection (placeholder for now)
     * TODO: Implement actual PDO connection when database is setup
     */
    protected function getConnection() {
        // Placeholder - will return PDO connection later
        return null;
    }

    /**
     * Get all records
     * @return array
     */
    public function getAll() {
        // TODO: Replace with actual database query
        // SELECT * FROM {$this->table}
        return $this->getSampleData();
    }

    /**
     * Find record by ID
     * @param mixed $id
     * @return array|null
     */
    public function find($id) {
        // TODO: Replace with actual database query
        // SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?
        $all = $this->getAll();
        foreach ($all as $item) {
            if (isset($item[$this->primaryKey]) && $item[$this->primaryKey] == $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Find records by field value
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function findBy($field, $value) {
        // TODO: Replace with actual database query
        // SELECT * FROM {$this->table} WHERE {$field} = ?
        $results = [];
        $all = $this->getAll();
        foreach ($all as $item) {
            if (isset($item[$field]) && $item[$field] == $value) {
                $results[] = $item;
            }
        }
        return $results;
    }

    /**
     * Create new record
     * @param array $data
     * @return int|bool Insert ID or false on failure
     */
    public function create($data) {
        // TODO: Replace with actual database INSERT
        // INSERT INTO {$this->table} (...) VALUES (...)

        // For now, store in session
        if (!isset($_SESSION[$this->table])) {
            $_SESSION[$this->table] = [];
        }

        // Generate ID
        $maxId = 0;
        foreach ($_SESSION[$this->table] as $item) {
            if (isset($item[$this->primaryKey]) && $item[$this->primaryKey] > $maxId) {
                $maxId = $item[$this->primaryKey];
            }
        }
        $data[$this->primaryKey] = $maxId + 1;

        $_SESSION[$this->table][] = $data;
        return $data[$this->primaryKey];
    }

    /**
     * Update record by ID
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        // TODO: Replace with actual database UPDATE
        // UPDATE {$this->table} SET ... WHERE {$this->primaryKey} = ?

        if (!isset($_SESSION[$this->table])) {
            return false;
        }

        foreach ($_SESSION[$this->table] as $key => $item) {
            if (isset($item[$this->primaryKey]) && $item[$this->primaryKey] == $id) {
                $_SESSION[$this->table][$key] = array_merge($item, $data);
                return true;
            }
        }
        return false;
    }

    /**
     * Delete record by ID
     * @param mixed $id
     * @return bool
     */
    public function delete($id) {
        // TODO: Replace with actual database DELETE
        // DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?

        if (!isset($_SESSION[$this->table])) {
            return false;
        }

        foreach ($_SESSION[$this->table] as $key => $item) {
            if (isset($item[$this->primaryKey]) && $item[$this->primaryKey] == $id) {
                unset($_SESSION[$this->table][$key]);
                $_SESSION[$this->table] = array_values($_SESSION[$this->table]); // Re-index
                return true;
            }
        }
        return false;
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
                if ($r === 'required' && empty($data[$field])) {
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
     * Get sample data - to be overridden by child classes
     * @return array
     */
    abstract protected function getSampleData();
}
