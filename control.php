<?php

include 'DB.php';

$db=new NotesDatabase();

function index(){
    global $db;
    $result = $db->getAllNotes($_GET['offset'],$_GET['rows-per-page']);
    $pages = $result['pages'];
    return json_encode(["data"=>$result['notes'],"pages"=>$pages]);
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

function search(){
    global $db;
    $result = $db->searchNotes($_GET["searchTerm"],$_GET['offset'],$_GET['rows-per-page']);
    $pages = $result['pages'];
    return json_encode(["data"=>$result['notes'],"pages"=>$pages]);
}


if(isset($_GET['operation']) && $_GET['operation']=='findAll'){
    echo index();
}elseif(isset($_POST['operation']) && $_POST['operation']=='insert'){
    empty($_POST['id']) ? save() : update();
}elseif(isset($_POST['operation']) && $_POST['operation']=='delete'){
    delete();
}elseif(isset($_GET['operation']) && $_GET['operation']=='find'){
    echo search();
}
