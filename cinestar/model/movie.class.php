<?php

class Movie
{
    public $id, $name, $description, $year, $duration;

    function __construct( $id, $name, $description, $year, $duration )
	{
		$this->id = $id;
        $this->name = $name;
		$this->description = $description;
		$this->year = $year;
		$this->duration = $duration;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}


?>