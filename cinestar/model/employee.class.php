<?php

class Employee
{
    public $id, $ime, $email;

    function __construct( $id, $ime, $email )
	{
		$this->id = $id;
		$this->ime = $ime;
		$this->email = $email;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}



?>