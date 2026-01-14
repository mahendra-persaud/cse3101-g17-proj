<?php
/**
 * School Model
 * Handles general school settings like terms and years
 */

require_once __DIR__ . '/Model.php';

class School extends Model {
    
    public function __construct() {
        $this->table = 'terms'; // Default to terms
    }

    /**
     * Get the current active term and year
     * @return array
     */
    public function getCurrentAcademicContext() {
        // In a real app, this might come from a settings table.
        // For now, we'll fetch the latest term from the DB.
        $sql = "SELECT t.term_name, y.year_name 
                FROM terms t 
                JOIN school_years y ON t.school_year_id = y.school_year_id 
                ORDER BY t.term_id DESC 
                LIMIT 1";
        
        $context = $this->queryOne($sql);
        
        if (!$context) {
            return [
                'term_name' => 'Term 1',
                'year_name' => '2025/2026'
            ];
        }
        
        return $context;
    }

    /**
     * Get all school years
     * @return array
     */
    public function getYears() {
        $this->table = 'school_years';
        return $this->getAll();
    }

    /**
     * Get all terms with year info
     * @return array
     */
    public function getTermsWithYears() {
        $sql = "SELECT t.*, y.year_name 
                FROM terms t 
                JOIN school_years y ON t.school_year_id = y.school_year_id 
                ORDER BY y.year_name DESC, t.term_name ASC";
        return $this->query($sql);
    }
}
