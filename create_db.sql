CREATE DATABASE IF NOT EXISTS three_leaf;
USE three_leaf;
CREATE USER IF NOT EXISTS 'test_user'@'localhost' IDENTIFIED BY 'test_user';
GRANT ALL PRIVILEGES ON three_leaf.* TO 'test_user'@'localhost';