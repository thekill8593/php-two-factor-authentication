<?php

namespace App\Controllers;

use App\Models\User;
use Exception;

class UserController {
    public function register($name, $email, $password) {
        $id = (new User())->createUser($name, $email, $password);
        return $id;
    }

    public function login($email, $password) {
        $user = (new User())->getUser($email);
        
        if ($user === null) {
            return ['result' => false];
        }

        if (!password_verify($password, $user['password'])) {
            return ['result' => false];
        }

        //segundo factor

        $this->createSession($user['id'], $user['email']);

        return ['result' => true];

    }

    protected function createSession($id, $email, $isLoggedIn = true) {
        $_SESSION['isLoggedIn'] = $isLoggedIn;
        $_SESSION['email'] = $email;
        $_SESSION['userId'] = $id;
    }


    public function isUserLoggedIn() {
        return isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'];
    }

    public function logout() {
        try {
            unset($_SESSION['isLoggedIn']);
            unset($_SESSION['email']);
            unset($_SESSION['userId']);
        }catch (\Exception $ex) {

        }
    }
    
}