CREATE DATABASE IF NOT EXISTS deefy;

USE deefy;

CREATE TABLE users
(
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    user_email    VARCHAR(255) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    permission_id INT,
    FOREIGN KEY (permission_id) REFERENCES permissions (permission_id)
);

CREATE TABLE playlists
(
    playlist_id   INT AUTO_INCREMENT PRIMARY KEY,
    playlist_name VARCHAR(255) NOT NULL
);

CREATE TABLE user_playlists
(
    user_id     INT,
    playlist_id INT,
    PRIMARY KEY (user_id, playlist_id),
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (playlist_id) REFERENCES playlists (playlist_id)
);

CREATE TABLE podcast_tracks
(
    track_id       INT AUTO_INCREMENT PRIMARY KEY,
    track_title    VARCHAR(255) NOT NULL,
    track_genre    VARCHAR(255),
    track_duration INT,
    track_year     DATE,
    track_filename VARCHAR(255),
    track_artist   VARCHAR(255)
);

CREATE TABLE album_tracks
(
    track_id       INT AUTO_INCREMENT PRIMARY KEY,
    track_title    VARCHAR(255) NOT NULL,
    album_name     VARCHAR(255),
    track_genre    VARCHAR(255),
    track_duration INT,
    track_number   INT,
    track_year     DATE,
    track_filename VARCHAR(255)
);

CREATE TABLE playlist_tracks
(
    playlist_id INT,
    track_id    INT,
    PRIMARY KEY (playlist_id, track_id),
    FOREIGN KEY (playlist_id) REFERENCES playlists (playlist_id),
    FOREIGN KEY (track_id) REFERENCES podcast_tracks (track_id)
);

CREATE TABLE permissions
(
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name     VARCHAR(255) NOT NULL UNIQUE,
    role_level    INT          NOT NULL
);