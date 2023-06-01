DROP DATABASE IF EXISTS cardclash;
CREATE DATABASE cardclash;
USE cardclash;

CREATE TABLE game_status(
    is_active int,
    PRIMARY KEY (is_active)
);
INSERT INTO game_status VALUES
(0); 
CREATE TABLE activity_log(
    log_msg  TEXT,
    log_icon VARCHAR(45),
    inc INT AUTO_INCREMENT,
    PRIMARY KEY(inc)
);
CREATE TABLE `user`(
    user_id int AUTO_INCREMENT UNIQUE,
    username varchar(30) UNIQUE,
    `password` varchar(30),
    selected_deck int DEFAULT 1,
    PRIMARY KEY(user_id)
);
CREATE TABLE special_card_description(
    `description` TEXT,
    card_id int,
    PRIMARY KEY(card_id)
);

INSERT INTO special_card_description (card_id, `description`) VALUES
(10, "Draw 2 Cards"),
(11, "Take an extra turn"),
(30, "Discard a pawn. Draw cards until you don't get a pawn."),
(40, "Draw and play the next 2 cards");
CREATE TABLE deck_ownership(
    user_id int,
    deck_id int,
    PRIMARY KEY(user_id,deck_id)
);

CREATE TABLE deck(
    deck_name VARCHAR(30),
    deck_id int AUTO_INCREMENT,
    folder varchar(45),
    PRIMARY KEY(deck_id)
);

INSERT INTO deck (deck_name, folder)
VALUES
("Army Deck", "army"),
("Navy Deck", "navy"),
("Chess Deck", "chess"),
("USSR Deck", "ussr");

CREATE TABLE deck_card(
    deck_id int,
    card_id int AUTO_INCREMENT, 
    health int, 
    damage int,
    card_sprite varchar(45),
    card_name varchar(25),
    PRIMARY KEY(card_id)
);
INSERT INTO deck_card (deck_id, health, damage, card_sprite, card_name)
VALUES 
(1,0,2,"army/recruit.png", "Recruit"),
(1,0,2,"army/recruit.png", "Recruit"),
(1,0,10,"army/tank.png", "Tank"),
(1,3,0,"army/medic.png", "Medic"),
(1,3,0,"army/medic.png", "Medic"),
(1,4,1,"army/officer.png", "Officer"),
(1,0,5,"army/soldier.png", "Soldier"),
(1,0,5,"army/soldier.png", "Soldier"),
(1,0,5,"army/soldier.png", "Soldier"),
(1,0,0,"army/mobilization.png", "Mobilization"),
(2,0,0,"navy/carrier.png", "Aircraft Carrier"),
(2,3,0,"navy/destroyer.png", "Destroyer"),
(2,3,0,"navy/destroyer.png", "Destroyer"),
(2,0,6,"navy/cruiser.png", "Cruiser"),
(2,0,6,"navy/cruiser.png", "Cruiser"),
(2,0,10,"navy/battleship.png", "Battleship"),
(2,0,4,"navy/submarine.png", "Submarine"),
(2,0,4,"navy/submarine.png", "Submarine"),
(2,2,0,"navy/frigate.png", "Frigate"),
(2,2,0,"navy/frigate.png", "Frigate"),
(3,2,1,"chess/pawn.png", "Pawn"),
(3,2,1,"chess/pawn.png", "Pawn"),
(3,2,1,"chess/pawn.png", "Pawn"),
(3,2,1,"chess/pawn.png", "Pawn"),
(3,2,1,"chess/pawn.png", "Pawn"),
(3,0,4,"chess/knight.png", "Knight"),
(3,0,4,"chess/bishop.png", "Bishop"),
(3,0,7,"chess/rook.png", "Rook"),
(3,0,10,"chess/queen.png", "Queen"),
(3,0,0,"chess/king.png", "King"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,1,3,"ussr/conscript.png", "Conscript"),
(4,2,6,"ussr/soviet_tank.png", "Tank"),
(4,0,0,"ussr/mass_assault.png", "Mass Assault");
CREATE TRIGGER `giveDefaultCard` 
AFTER INSERT ON `user` 
FOR EACH ROW INSERT INTO cardclash.deck_ownership( user_id, deck_id ) VALUES (new.user_id, 1),(new.user_id, 2), (new.user_id, 3),(new.user_id, 4);
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