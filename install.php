<?php

$conn = new mysqli('localhost','root','','db_note');

$conn->query('CREATE TABLE notes (
    id int NOT NULL AUTO_INCREMENT,
    title varchar(100),
    description varchar(255),
    created_at DATETIME,
    updated_at DATETIME,
    PRIMARY KEY (id)
);');