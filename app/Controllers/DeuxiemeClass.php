<?php
namespace App\Controllers;
use App\Users;

class DeuxiemeClasse {
    private $premiereClasse;
    function __construct(){
        $this->premiereClasse = new PremiereClasse();
    }
    function print(){
        $this->premiereClasse->printMyText('Hello');
    }
    
}
