DROP DATABASE IF EXISTS deefy;

CREATE DATABASE IF NOT EXISTS deefy CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE deefy;

CREATE TABLE users
(
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    user_name     VARCHAR(255)  NOT NULL UNIQUE,
    user_email    VARCHAR(255)  NOT NULL UNIQUE,
    user_password VARCHAR(255)  NOT NULL,
    permission_id INT DEFAULT 1 NOT NULL
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

CREATE TABLE playlists
(
    playlist_id   INT AUTO_INCREMENT PRIMARY KEY,
    playlist_name VARCHAR(255) NOT NULL
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

CREATE TABLE user_playlists
(
    user_id     INT,
    playlist_id INT,
    PRIMARY KEY (user_id, playlist_id)
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

CREATE TABLE podcast_tracks
(
    track_id       INT AUTO_INCREMENT PRIMARY KEY,
    track_title    VARCHAR(255) NOT NULL,
    track_genre    VARCHAR(255),
    track_duration INT,
    track_year     DATE,
    track_filename VARCHAR(255),
    track_artist   VARCHAR(255)
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

CREATE TABLE playlist_tracks
(
    playlist_id INT,
    track_id    INT,
    PRIMARY KEY (playlist_id, track_id)
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

CREATE TABLE permissions
(
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name     VARCHAR(255) NOT NULL UNIQUE,
    role_level    INT          NOT NULL
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

CREATE TABLE tokens
(
    token_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNIQUE   NOT NULL,
    token      VARCHAR(255) NOT NULL,
    created_at DATETIME     NOT NULL default CURRENT_TIMESTAMP,
    expires_at DATETIME     NOT NULL
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

ALTER TABLE users
    ADD FOREIGN KEY (permission_id) REFERENCES permissions (permission_id);

ALTER TABLE user_playlists
    ADD FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD FOREIGN KEY (playlist_id) REFERENCES playlists (playlist_id);

ALTER TABLE playlist_tracks
    ADD FOREIGN KEY (playlist_id) REFERENCES playlists (playlist_id),
    ADD FOREIGN KEY (track_id) REFERENCES podcast_tracks (track_id);

ALTER TABLE tokens
    ADD FOREIGN KEY (user_id) REFERENCES users (user_id);

INSERT INTO permissions (role_name, role_level)
VALUES ('USER', 0);
INSERT INTO permissions (role_name, role_level)
VALUES ('MOD', 10);
INSERT INTO permissions (role_name, role_level)
VALUES ('ADMIN', 100);

insert into deefy.users (user_id, user_name, user_email, user_password, permission_id)
values  (2, 'ADMINISTRATEUR', 'ADMINISTRATEUR@example.com', '$2y$10$KEtiW6iIEIzs0ogCBuQX/eJTpLluCrpBPlWumszFTWDfG1IqbRoSC', 3);