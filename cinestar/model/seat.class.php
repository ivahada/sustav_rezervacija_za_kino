<?php
class Seat
{
    public $red, $broj_u_redu, $rezervacija_id;

    function __construct($red, $broj_u_redu, $rezervacija_id)
	{
		$this->red = $red;
        $this->broj_u_redu = $broj_u_redu;
		$this->rezervacija_id = $rezervacija_id;
		
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}
?>