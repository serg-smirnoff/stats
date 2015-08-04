<?php

class Config{

    public $host;
    public $user;
    public $pass;
    public $base;
    
    function __construct(){
	$this->host = "localhost";
        $this->user = "";
        $this->pass = "";
        $this->base = "stat";
    } 
    
}

?>