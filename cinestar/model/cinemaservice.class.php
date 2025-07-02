<?php
require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/employee.class.php';
require_once __DIR__ . '/movie.class.php';
require_once __DIR__ . '/seat.class.php';
require_once __DIR__ . '/projection.class.php';
require_once __DIR__ . '/reservation.class.php';



class CinemaService
{
    function getAllMovies()
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM film');
			$st->execute( );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		
		$arr = array();
		while( $row = $st->fetch() )
		{
			$name = $row['ime'];
			$id = $row['id'];
            $projections = $this -> getProjectionsByMovieId( $id );
			$arr[] = ['movie' => new Movie( $row['id'], $row['ime'], $row['opis'], $row['godina'], $row['trajanje'] ),
                        'projections' => $projections]; //dodaj projekcije
		}

		return $arr;
    
    }

    function getMoviesBySearch( $name )
    {
        $regexp = '/' . $name . '/i';
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM film' );
			$st->execute( );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
		{
			if( preg_match( $regexp, $row['ime'] ) ){
                $projections = $this -> getProjectionsByMovieId( $row['id'] );
				$arr[] = ['movie' => new Movie( $row['id'], $row['ime'], $row['opis'], $row['godina'], $row['trajanje'] ),
                            'projections' => $projections];
			}
		}

		return $arr;
    }

    function getMoviesByUserName( $name ) //koje filmove je user rezervirao
    {
		$id = $this->getUserIdByUserName( $name );
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM rezervacija WHERE user_id=:id_user' );
			$st->execute( array( 'id_user' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		
		$arr = array();
		while( $row = $st->fetch() )
		{
			$movie_id = $this->getMovieIdByProjectionId( $row['prikaz_id'] );
			$movie = $this->getMovieById( $movie_id );
            $projection = $this->getProjectionById( $row['prikaz_id']);
			$arr[] = ['movie' => $movie,
                        'projection' => $projection,
						'tics' => $row['broj_karata'],
						'id' => $row['id']];
                       // 'price' => $row['price']];
		}

		return $arr;
    }

	function getUserIdByUserName( $name ) //vraća id
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id FROM korisnik WHERE ime=:ime' );
			$st->execute( array( 'ime' => $name ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		$user = $st -> fetch();
		return $user['id'];

	}

    function getMovieIdByProjectionId( $id ) //vraća id filma
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT film_id FROM prikaz WHERE id=:id ' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $movie = $st->fetch();
        return $movie['film_id'];
    }

    function getMovieById( $id ) //vraća film (class Movie )
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM film WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $movie = $st->fetch();
        return new Movie( $id, $movie['ime'], $movie['opis'], $movie['godina'], $movie['trajanje'] );
    }

    function getProjectionsByMovieId( $id ) //vraća array of class Projection
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM prikaz WHERE film_id=:id ' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $arr = array();
		while( $row = $st->fetch() ){
            $arr[] = new Projection( $row['id'], $row['dvorana_id'], $row['film_id'], $row['datum'], $row['vrijeme']);
        }
        return $arr;

    }

    function getProjectionById( $id ) //vraća class Projection
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM prikaz WHERE id=:id ' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $row =$st->fetch();
        return new Projection( $row['id'], $row['dvorana_id'], $row['film_id'], $row['datum'], $row['vrijeme']);
    }


	function getAllProjectionsForDate($date){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT prikaz.id, dvorana_id,film.ime,vrijeme FROM prikaz,film WHERE prikaz.datum = :datum AND prikaz.film_id=film.id' );
			$st->execute( array( 'datum' => $date ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $arr = array();
		while( $row = $st->fetch() ){
            $arr[] = new Projection( $row['id'], $row['dvorana_id'], $row['ime'], null, $row['vrijeme']);
        }
        return $arr;
	}

	function getDatesByMovieId( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT DISTINCT datum FROM prikaz WHERE film_id=:id ' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() ){
            $arr[] = $row['datum'];
        }
        return $arr;
	}

	function getMovieByProjectionId( $id )
	{
		$movie_id = $this->getMovieIdByProjectionId( $id );
		$movie = $this -> getMovieById( $movie_id );
		return $movie;
	}

	function getHallIdByProjectionId( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT dvorana_id FROM prikaz WHERE id=:id ' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		return $row['dvorana_id'];
	}

	function cancelReservationById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM rezervacija WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$this->eraseSeatsByReservationId( $id );
		
	}

	function eraseSeatsByReservationId( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM sjedalo WHERE rezervacija_id=:id' );
			$st->execute( array( 'id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function erasePastProjections()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, datum, vrijeme FROM prikaz' );
			$st->execute( );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		while( $row = $st->fetch()){
			$date = explode('-',$row['datum']);
			$time = explode(':', $row['vrijeme']);
			$new_date = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
			if( $new_date < time()){
				$this->erasePastReservationsByProjectionId( $row['id'] );
				$stt = $db->prepare( 'DELETE FROM prikaz WHERE id=:id');
				$stt->execute( array('id' => $row['id'] ) );
			}
		}
	}

	function erasePastReservationsByProjectionId( $id ) //izbriši sjedala
	{
		try
		{
			$db = DB::getConnection();
			$stt = $db->prepare( 'SELECT id FROM rezervacija WHERE prikaz_id=:id');
			$stt->execute( array('id' => $id ) );
			while( $row = $stt->fetch() ){
				$this->eraseSeatsByReservationId( $row['id']);
			}
			
			$st = $db->prepare( 'DELETE FROM rezervacija WHERE prikaz_id=:id' );
			$st->execute( array('id' => $id ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		
	}

	function getSizeOfHallByProjectionId( $id ) //vraca array
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT dvorana_id FROM prikaz WHERE id=:id' );
			$st->execute( array( 'id' => $id) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		return $this -> getSizeOfHallById( $row['dvorana_id']);

	}

	function getSizeOfHallById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT broj_redova, broj_sjedala_po_redu FROM _dvorane WHERE id=:id' );
			$st->execute( array( 'id' => $id) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		$size = [];
		$size[] = $row['broj_redova'];
		$size[] = $row['broj_sjedala_po_redu'];
		return $size;
	}

	function getProjectionIdByReservationId ( $id ){
		
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT prikaz_id
									FROM rezervacija
									WHERE id =:id');
			$st->execute( array( 'id' => $id) );

			
			$row = $st->fetch();
			if ($row != null)
				return $row['prikaz_id'];
			else return null;				
		}
		catch( PDOException $e ) {
			
			return  $e->getMessage() ;
		
		}

	}



	function getReservedSeatsByReservationId ( $reserv_id )
	{
		
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT sjedalo.red,sjedalo.broj_u_redu ,sjedalo.rezervacija_id
									FROM sjedalo,rezervacija,prikaz 
									WHERE sjedalo.rezervacija_id=rezervacija.id 
									AND prikaz.id=rezervacija.prikaz_id 
									AND rezervacija.id=:id;');
			$st->execute( array( 'id' => $reserv_id) );
			
		}
		catch( PDOException $e ) { 
			
			return $e->getMessage() ;
		
		}
		$arr = [];
		while( $row = $st->fetch()){
			
			$arr[] = new Seat((int)$row['red'],(int)$row["broj_u_redu"], (int)$row["rezervacija_id"]);
			
		
		}
		return $arr;

	}


	function getReservedSeatsByProjectionId ( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT sjedalo.red,sjedalo.broj_u_redu ,sjedalo.rezervacija_id
									FROM sjedalo, rezervacija, prikaz 
									WHERE sjedalo.rezervacija_id=rezervacija.id 
									AND prikaz.id=rezervacija.prikaz_id 
									AND prikaz.id=:id;');
			$st->execute( array( 'id' => $id) );
			
		}
		catch( PDOException $e ) { 
			
			return $e->getMessage() ;
		
		}
		$arr = [];
		while( $row = $st->fetch()){
			
			$arr[] = new Seat((int)$row['red'],(int)$row["broj_u_redu"], (int)$row["rezervacija_id"]);
			
		
		}
		return $arr;

	}

	function addNewMovie( $name, $description, $year, $duration)
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('INSERT INTO film (id, ime, opis, godina, trajanje) VALUES(:id, :ime, :opis, :godina, :trajanje)');

			$stt = $db->prepare('SELECT id FROM film');
			$stt->execute();
			$id = $stt -> rowCount() + 1;

			$st->execute( array( 'id' => $id, 'ime' => $name, 'opis' => $description, 'godina' => $year, 'trajanje' => $duration) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function addNewProjection( $movie_id, $hall_id, $date, $time )
	{
		
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('INSERT INTO prikaz (id, film_id, dvorana_id, datum, vrijeme) VALUES(:id, :film_id, :dvorana_id, :datum, :vrijeme)');

			$stt = $db->prepare('SELECT MAX(id) AS id FROM prikaz');

			$stt -> execute();

			$row = $stt ->fetch();
			$id = (int) $row['id'] + 1;

			$st->execute( array( 'id' => $id, 'film_id' => $movie_id, 'dvorana_id' => $hall_id, 'datum' => $date, 'vrijeme' => $time) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function insertNewReservations($seats, $prikaz_id ,$korisnik_id){ //dovrsiti
		$zaVratiti=[];
		//PROVJERI JESU LI SJEDALA ZAUZETA -- nije gotovo
		foreach ($seats as $key => $seat) {
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare('SELECT sjedalo.red ,sjedalo.broj_u_redu
									FROM  sjedalo,prikaz,rezervacija 
									WHERE sjedalo.red = :red 
									AND sjedalo.broj_u_redu=:stupac
									AND sjedalo.rezervacija_id=rezervacija.id 
									AND prikaz.id=rezervacija.prikaz_id 
									AND prikaz.id=:prikaz');
	
				$st->execute( array( 'red' => $seat->red, 'stupac' => $seat->broj_u_redu, 'prikaz' => $prikaz_id) );

				if ($st->rowCount() > 0) {
					
					$zaVratiti['uspjeh']= False;
					$row = $st->fetch();
					$zaVratiti['rezervacija']= "vec su zauzeta mjesta " . strval($row["red"]) ."," .strval($row["broj_u_redu"]);
					return $zaVratiti;
				}


			}
			catch( PDOException $e ) {
				
				
				
				$zaVratiti['uspjeh']= False;
				$row = $st->fetch();
				$zaVratiti['rezervacija']=$e->getMessage();
				
			
			}
		}
		//AKO TA NISU ZAUZETA NASTAVI
		$zaVratiti=[];
		$br_karata = count($seats);
		try {
			$db = DB::getConnection();
			$st = $db -> prepare('SELECT MAX(id) AS id FROM rezervacija');
			$st->execute();
			$row = $st -> fetch();
			$new_id = (int) $row['id'] +1 ;



			/*

			PREPARED STATEMENTS NEMOGU IC UNUTAR TRANSAKCIJE

			*/
			$db->beginTransaction();

			$st = $db->query('INSERT INTO rezervacija (id, user_id, prikaz_id, broj_karata) 
							VALUES('. $new_id.', '. $korisnik_id.', '. $prikaz_id.', '. $br_karata.')');
			
			

			foreach($seats as $seat){
				
				$x=$seat->red;
				$y=$seat->broj_u_redu;
				$st = $db->query( ' INSERT INTO sjedalo (red, broj_u_redu, rezervacija_id) VALUES ('. $x.', '. $y.', '. $new_id.')');
				
			}

			$db->commit();
		} catch (PDOException $e) {
			
			$db->rollback();

			$zaVratiti['uspjeh'] = False;
			$zaVratiti['rezervacija']= strval($e);
			//echo $e; 
			return $zaVratiti;
			
			
		}
		$zaVratiti['uspjeh']= True;
		$zaVratiti['rezervacija'] = $new_id;
		return $zaVratiti;
	}


	function checkIfTheNewProjectionIsOk( $movie_id, $hall_id, $date, $time ) //provjeri preklapa li se nova projekcija sa starima
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT vrijeme, film_id FROM prikaz WHERE dvorana_id=:hall_id AND datum=:date' );
			$st->execute( array('hall_id' => $hall_id, 'date' => $date ) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		$duration = $this->getDurationByMovieId( $movie_id );

		while( $row = $st->fetch() ){
			$duration2 = $this-> getDurationByMovieId( $row['film_id']);
			if( !$this->compareTimes($time, $row['vrijeme'], $duration, $duration2 ) )
				return false;
		}
		return true;

	}

	function compareTimes($time1, $time2, $duration1, $duration2)
	{
		$timeArr1 = explode(':', $time1); //nova projekcija
		$timeArr2 = explode(':', $time2);
		$durArr1 = explode(':', $duration1); // trajanje novog filma
		$durArr2 = explode(':', $duration2);

		$t1 = mktime((int)$timeArr1[0], (int)$timeArr1[1], (int)$timeArr1[2]);
		$t2 = mktime((int)$timeArr2[0], (int)$timeArr2[1], (int)$timeArr2[2]);
		$dur1 = mktime((int)$durArr1[0], (int)$durArr1[1], (int)$durArr1[2]);
		$dur2 = mktime((int)$durArr2[0], (int)$durArr2[1], (int)$durArr2[2]);

		if( $t1 + $dur1 >= $t2) return false; //valjda su to svi slučajevi
		else if( $t2 + $dur2 >= $t1 ) return false;
		else if( $t1 === $t2) return false;
		else if( $t2 > $t1 && $t2 <= $t1 + $dur1) return false;
		else if( $t1 > $t2 && $t1 <= $t2 + $dur2) return false;
		else return true;
	}

	function getDurationByMovieId( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT trajanje FROM film WHERE id=:id' );
			$st->execute( array('id' => $id ) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		return $row['trajanje'];
	}

	function getEmployees()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, ime, email FROM radnik ');
			$st->execute(  );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		
		$arr = [];
		while( $row = $st->fetch() ){
			$arr[] = new Employee($row['id'], $row['ime'], $row['email']);
		}

		return $arr;

	}

	function addEmployee( $name, $password, $email )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('INSERT INTO radnik (id, ime, password_hash, email) VALUES(:id, :ime, :password_hash, :email )');
			$stt = $db -> prepare('SELECT MAX(id) AS id FROM radnik');
			$stt->execute();
			$row = $stt->fetch();
			$id = (int) $row['id'] + 1; //nešto

			$st->execute( array( 'id' => $id, 'ime' => $name, 'password_hash' => password_hash( $password, PASSWORD_DEFAULT ), 'email' => $email ) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function removeEmployeeById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM radnik WHERE id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function removeProjectionById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM prikaz WHERE id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$this->removeReservationsByProjectionId( $id );
	}

	function removeReservationsByProjectionId( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id FROM rezervacija WHERE prikaz_id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		while( $row = $st->fetch()){
			$this->removeSeatsByReservationId($row['id']);
		}
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM rezervacija WHERE prikaz_id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

	}

	function removeSeatsByReservationId ($id )
	{


		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM sjedalo WHERE rezervacija_id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function removeReservationByReservationId ($id )
	{


		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM rezervacija WHERE id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}


	function sellSeatsByReservationId ($id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE rezervacija SET kupljeno = 1  WHERE id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function getReservationsByProjectionId( $id ) //vraca array s class Reservation i array s class Seat
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM rezervacija WHERE prikaz_id=:id');
			$st->execute( array( 'id' => $id) );

		}
		catch( PDOException $e ) { print $e->getMessage() ; }

		$arr = [];

		while( $row = $st->fetch()){
			
			$arr[] = ['reservation' => new Reservation( $row['id'], $row['user_id'], $row['prikaz_id'], $row['broj_karata']),
						'seats' => $this->getReservedSeatsByReservationId( $row['id'])];
		}

		return $arr;
	}

	function getCinemaInfo()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM kino WHERE id=:id');
			$st->execute( array( 'id' => 123) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		$arr = [];
		$arr['name'] = $row['ime'];
		$arr['adress'] = $row['adresa'];
		$arr['email'] = $row['email'];
		$arr['tel'] = $row['tel'];
		$arr['open'] = $row['radimo'];

		return $arr;
	}

	function changePassByUserId( $id, $pass )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE korisnik SET password_hash=:pass WHERE id=:id');
			$st->execute( array( 'pass' => password_hash($pass, PASSWORD_DEFAULT ), 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changeNameByUserId( $id, $name )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE korisnik SET ime=:name WHERE id=:id');
			$st->execute( array( 'name' => $name, 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changeEmailByUserId( $id, $email )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE korisnik SET email=:email WHERE id=:id');
			$st->execute( array( 'email' => $email, 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changePassByEmployeeId( $id, $pass )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE radnik SET password_hash=:pass WHERE id=:id');
			$st->execute( array( 'pass' => password_hash($pass, PASSWORD_DEFAULT ), 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changeEmailByEmployeeId( $id, $email )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE radnik SET email=:email WHERE id=:id');
			$st->execute( array( 'email' => $email, 'id' => $id) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changeCinemaAdress( $adress )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE kino SET adresa=:adress WHERE id=:id');
			$st->execute( array( 'adress' => $adress, 'id' => 123) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changeCinemaEmail( $email )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE kino SET email=:email WHERE id=:id');
			$st->execute( array( 'email' => $email, 'id' => 123) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changeCinemaTelephone( $tel )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE kino SET tel=:tel WHERE id=:id');
			$st->execute( array( 'tel' => $tel, 'id' => 123) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function changeCinemaOpen( $open )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE kino SET radimo=:open WHERE id=:id');
			$st->execute( array( 'open' => $open, 'id' => 123) );

		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}


}


?>