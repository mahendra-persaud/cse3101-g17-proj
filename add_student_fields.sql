-- Migration: Add date_of_birth and gender fields to students table
-- Run this if you already have an existing database

USE School_Management;

-- Add date_of_birth column if it doesn't exist
ALTER TABLE students ADD COLUMN IF NOT EXISTS date_of_birth DATE DEFAULT NULL AFTER last_name;

-- Add gender column if it doesn't exist
ALTER TABLE students ADD COLUMN IF NOT EXISTS gender ENUM('Male', 'Female') DEFAULT NULL AFTER date_of_birth;

SELECT 'Migration complete! date_of_birth and gender columns added to students table.' AS Status;
