<?php
require_once 'AppController.php';

class SecurityController extends AppController{


    public function login(){
        //TODO get data from database
        
        return $this->render("login");
    }
}