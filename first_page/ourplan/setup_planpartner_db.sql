-- Plan Partner Database Setup
-- This file creates the database and table for plan requests

-- Create the database
CREATE DATABASE IF NOT EXISTS planpartner;

-- Use the database
USE planpartner;

-- Create the plan_requests table
CREATE TABLE IF NOT EXISTS plan_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partnerid INT NOT NULL,
    planid INT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add some indexes for better performance
CREATE INDEX idx_partnerid ON plan_requests(partnerid);
CREATE INDEX idx_planid ON plan_requests(planid);
CREATE INDEX idx_status ON plan_requests(status);
CREATE INDEX idx_time ON plan_requests(time);

-- Insert some sample data (optional - for testing)
-- INSERT INTO plan_requests (partnerid, planid, status) VALUES 
-- (1, 1, 'pending'),
-- (2, 2, 'pending'),
-- (3, 3, 'pending');

-- Show the table structure
DESCRIBE plan_requests;

-- Show sample data (if any)
SELECT * FROM plan_requests;
