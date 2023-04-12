DROP DATABASE IF EXISTS cardclash;
CREATE DATABASE cardclash;
USE cardclash;
CREATE TABLE sessions(
    user_id int UNIQUE,
    session_key varchar(45) UNIQUE
);
CREATE TABLE users(
    user_id int AUTO_INCREMENT UNIQUE,
    username varchar(30) UNIQUE,
    `password` varchar(30),
    PRIMARY KEY(user_id)
);
INSERT INTO users(username, `password`)
VALUES 
('test', 'test');