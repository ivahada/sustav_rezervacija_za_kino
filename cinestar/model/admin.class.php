<?php

class Admin
{
    protected $id, $ime, $username, $password;

    function __construct( $id, $ime, $username, $password )
	{
		$this->id = $id;
		$this->ime = $ime;
		$this->username = $username;
		$this->password = $password;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}



?>