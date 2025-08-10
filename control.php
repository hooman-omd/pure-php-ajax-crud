<?php

include 'DB.php';

$db=new NotesDatabase();

function index(){
    global $db;
    return json_encode($db->getAllNotes());
}

function save(){
    global $db;
    $db->insertNote($_POST['title'],$_POST['description']);
}

function delete() {
    global $db;
    $db->deleteNote($_POST['id']);
}

function update() {
    global $db;
    $db->updateNote($_POST['id'],$_POST['title'],$_POST['description']);
}


if(isset($_GET['operation']) && $_GET['operation']=='findAll'){
    echo index();
}elseif(isset($_POST['operation']) && $_POST['operation']=='insert'){
    empty($_POST['id']) ? save() : update();
}elseif(isset($_POST['operation']) && $_POST['operation']=='delete'){
    delete();
}
