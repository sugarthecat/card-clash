DROP DATABASE IF EXISTS cardclash;
CREATE DATABASE cardclash;
USE cardclash;
CREATE TABLE `user`(
    user_id int AUTO_INCREMENT UNIQUE,
    username varchar(30) UNIQUE,
    `password` varchar(30),
    selected_deck int DEFAULT 1,
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
("Army Deck", "tank.png"),
("Navy Deck", "tank.png");
CREATE TABLE deck_card(
    deck_id int,
    card_id int, 
    health int, 
    damage int,
    card_sprite varchar(45),
    card_name varchar(25),
    PRIMARY KEY(card_id)
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
(1,10,0,0,"mobilization.png", "Mobilization"),
(2,11,0,0,"carrier.png", "Aircraft Carrier"),
(2,12,0,2,"destroyer.png", "Destroyer"),
(2,13,0,10,"destroyer.png", "Destroyer"),
(2,14,3,0,"cruiser.png", "Cruiser"),
(2,15,3,0,"cruiser.png", "Cruiser"),
(2,16,4,1,"battleship.png", "Battleship"),
(2,17,0,5,"submarine.png", "Submarine"),
(2,18,0,5,"submarine.png", "Submarine"),
(2,19,0,5,"frigate.png", "Frigate"),
(2,20,0,0,"frigate.png", "Frigate");

CREATE TRIGGER `giveDefaultCard` AFTER INSERT ON `user` FOR EACH ROW INSERT INTO cardclash.deck_ownership( user_id, deck_id ) VALUES (new.user_id, 1);
CREATE TABLE game_player(
    user_id int,
    last_server_contact DATETIME,
    health int DEFAULT 20,
    last_turn DATETIME,
    last_turn_int int DEFAULT 1,
    PRIMARY KEY(user_id)
);
CREATE TABLE game_card(
    user_id int,
    card_id int, 
    play_status int DEFAULT 1,
    key_id int UNIQUE AUTO_INCREMENT,
    FOREIGN KEY (user_id) REFERENCES game_player(user_id) ON DELETE CASCADE
);