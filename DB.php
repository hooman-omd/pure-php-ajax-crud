<?php

class NotesDatabase {
    private $connection;
    private const host = 'localhost';
    private const username = 'root';
    private const password ='';
    private const database = 'db_notes';

    public function __construct() {
        $this->connection = new mysqli(
            self::host,
            self::username,
            self::password,
            self::database
        );
        
       
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    
    public function getAllNotes(int $offset,int $rowsPerPage) {
        $offset = $offset * $rowsPerPage;
        $query = "SELECT * FROM notes ORDER BY id DESC limit $offset,$rowsPerPage";
        $numRows = $this->connection->query("SELECT * FROM notes");
        $result = $this->connection->query($query);
        $pages = ceil($numRows->num_rows / $rowsPerPage);
        
        if (!$result) {
            die("Error fetching notes: " . $this->connection->error);
        }
        
        $notes = [];
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
        
        return ["notes"=>$notes,"pages"=>$pages];
    }

    
    public function searchNotes($searchTerm,int $offset,int $rowsPerPage) {
        $offset = $offset * $rowsPerPage;
        $query = "SELECT * FROM notes 
                 WHERE title LIKE ? OR description LIKE ? 
                 ORDER BY id DESC limit $offset,$rowsPerPage";

        $numRows = $this->connection->query("SELECT * FROM notes WHERE title LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'");
        $pages = ceil($numRows->num_rows / $rowsPerPage);
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->connection->error);
        }
        
        $searchParam = "%" . $searchTerm . "%";
        $stmt->bind_param("ss", $searchParam, $searchParam);
        
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $notes = [];
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
        
        $stmt->close();
        return ["notes"=>$notes,"pages"=>$pages];
    }

    
    public function insertNote($title, $description) {
        $query = "INSERT INTO notes (title, description, created_at, updated_at) 
                  VALUES (?, ?, NOW(), NOW())";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->connection->error);
        }
        
        $stmt->bind_param("ss", $title, $description);
        
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        
        $insertedId = $stmt->insert_id;
        $stmt->close();
        return $insertedId;
    }

    
    public function updateNote($id, $title, $description) {
        $query = "UPDATE notes SET title = ?, description = ?, updated_at = NOW() 
                 WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->connection->error);
        }
        
        $stmt->bind_param("ssi", $title, $description, $id);
        
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows;
    }

   
    public function deleteNote($id) {
        $query = "DELETE FROM notes WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->connection->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows;
    }

   
    public function closeConnection() {
        $this->connection->close();
    }

    
    public function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}