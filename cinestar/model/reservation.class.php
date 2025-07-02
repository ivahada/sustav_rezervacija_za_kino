<?php

class Reservation
{
    public $id, $user_id, $projection_id, $num_of_tics;

    function __construct( $id, $user_id, $projection_id, $num_of_tics)
	{
		$this->id = $id;
        $this->user_id = $user_id;
		$this->projection_id= $projection_id;
		$this->num_of_tics = $num_of_tics;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}


?>