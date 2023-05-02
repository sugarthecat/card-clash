DROP DATABASE IF EXISTS cardclash;
CREATE DATABASE cardclash;
USE cardclash;
CREATE TABLE `user`(
    user_id int AUTO_INCREMENT UNIQUE,
    username varchar(30) UNIQUE,
    `password` varchar(30),
    PRIMARY KEY(user_id)
);
CREATE TABLE deck_ownership(
    user_id int,
    deck_id int,
    PRIMARY KEY(user_id,deck_id)
);
CREATE TABLE deck(
    deck_name VARCHAR(30),
    deck_id int AUTO_INCREMENT,
    deck_icon varchar(45),
    PRIMARY KEY(deck_id)
);
INSERT INTO deck (deck_name, deck_icon)
VALUES
("Army Deck", "tank.png");
CREATE TABLE deck_card(
    deck_id int,
    card_id int, 
    health int, 
    damage int,
    card_sprite varchar(45),
    card_name varchar(25),
    PRIMARY KEY(card_id, deck_id)
);
INSERT INTO deck_card (deck_id, card_id, health, damage, card_sprite, card_name)
VALUES 
(1,1,0,2,"recruit.png", "Recruit"),
(1,2,0,2,"recruit.png", "Recruit"),
(1,3,0,10,"tank.png", "Tank"),
(1,4,3,0,"medic.png", "Medic"),
(1,5,3,0,"medic.png", "Medic"),
(1,6,4,1,"officer.png", "Officer"),
(1,7,0,5,"soldier.png", "Soldier"),
(1,8,0,5,"soldier.png", "Soldier"),
(1,9,0,5,"soldier.png", "Soldier"),
(1,10,0,0,"mobilization.png", "Mobilization");
CREATE TRIGGER `giveDefaultCard` AFTER INSERT ON `user` FOR EACH ROW INSERT INTO cardclash.deck_ownership( user_id, deck_id ) VALUES (new.user_id, 1);
CREATE TABLE game_player(
    user_id int,
    last_server_contact DATETIME,
    health int
);
CREATE TABLE game_card(
    user_id int,
    card_id int, 
    played TINYINT(1)
);