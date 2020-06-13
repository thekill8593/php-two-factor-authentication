<?php

namespace App\Models;

class User extends Database {

    public function createUser($name, $email, $password) {
        $query = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query->bind_param('sss', $name, $email, $hash);
        $query->execute();
        $insertedId = $query->insert_id;
        $query->close();
        return $insertedId;
    }

    public function getUser($email) {
        $query = $this->db->prepare("SELECT * FROM users WHERE email = ?");        
        $query->bind_param('s', $email);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        return $result->fetch_assoc();
    }

}