<?php
namespace App;
use App\PremiereClasse;

class DeuxiemeClasse {
    private $premiereClasse;
    function __construct(){
        $this->premiereClasse = new PremiereClasse();
    }
    function print(){
        $this->premiereClasse->printMyText('Hello');
    }
    
}
