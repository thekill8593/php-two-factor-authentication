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

        if ($user['two_factor_key'] !== null) {
            $this->createSession(null, $user['email'], false);
            return ['result' => true, 'secondfactor' => true];
        }

        $this->createSession($user['id'], $user['email']);        

        return ['result' => true, 'secondfactor' => false];

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

    public function getUser() {
        $email = $_SESSION['email'];
        return (new User())->getUser($email);
    }   

    public function activateSecondFactor($secret, $code) {
        if ($this->checkGoogleAuthenticatorCode($secret, $code)) {
            $id = $_SESSION['userId'];
            (new User())->createSecret($secret, $id);
            return true;
        }
        return false;
    }

    public function deactivateSecondFactor() {
        $id = $_SESSION['userId'];
        (new User())->deleteSecret($id);
    }

    public function checkGoogleAuthenticatorCode($secret, $code) {
        $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
        if ($g->checkCode($secret, $code)) {
            return true;
        }
        return false;
    }

    public function validateCode($code) {
        $user = $this->getUser();
        if ($this->checkGoogleAuthenticatorCode($user['two_factor_key'], $code)) {
            $this->createSession($user['id'], $user['email']);
            return true;
        }

        return false;
    }
    
}