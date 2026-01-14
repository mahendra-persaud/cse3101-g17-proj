<?php
// general school settings
// handles terms and years and stuff

require_once __DIR__ . '/Model.php';

class School extends Model {
    
    public function __construct() {
        $this->table = 'terms'; // Default to terms
        $this->primaryKey = 'term_id';
    }

    // checks which fields i can use
    // depends on which table im looking at
    protected function getAllowedFields() {
        if ($this->table === 'school_years') {
            return ['year_name', 'start_date', 'end_date', 'status'];
        }
        return ['term_name', 'school_year_id', 'start_date', 'end_date', 'status'];
    }

    // makes a new school year (e.g. 2025/2026)
    public function createYear($data) {
        $this->table = 'school_years';
        $this->primaryKey = 'school_year_id';
        return $this->create($data);
    }

    // makes a new term (e.g. Term 1)
    public function createTerm($data) {
        $this->table = 'terms';
        $this->primaryKey = 'term_id';
        return $this->create($data);
    }

    // finds out what term/year we are in right now
    // grabs the latest one from the db
    public function getCurrentAcademicContext() {
        // fetching the latest term
        // hope this logic is right
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

    // gets all the years
    public function getYears() {
        $this->table = 'school_years';
        return $this->getAll();
    }

    // gets terms and puts the year next to them
    public function getTermsWithYears() {
        $sql = "SELECT t.*, y.year_name 
                FROM terms t 
                JOIN school_years y ON t.school_year_id = y.school_year_id 
                ORDER BY y.year_name DESC, t.term_name ASC";
        return $this->query($sql);
    }
}
