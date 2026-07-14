-- database_export.sql
-- Library System Database Schema and Sample Data

CREATE DATABASE IF NOT EXISTS library_system;
USE library_system;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year INT(4) NOT NULL,
    status VARCHAR(20) DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO books (title, author, year, status) VALUES
('The Alchemist', 'Paulo Coelho', 1988, 'Available'),
('1984', 'George Orwell', 1949, 'Borrowed'),
('Clean Code', 'Robert C. Martin', 2008, 'Available'),
('Introduction to Algorithms', 'Thomas H. Cormen', 2009, 'Available'),
('The Great Gatsby', 'F. Scott Fitzgerald', 1925, 'Borrowed');
