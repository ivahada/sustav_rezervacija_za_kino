<?php

//require_once __DIR__ . '/../model/globalservice.class.php';

//require_once __DIR__ . '/../model/mongoservice.class.php';

require_once __DIR__ . '/../model/cinemaservice.class.php';
require_once __DIR__ . '/../model/seat.class.php';

function datum ($date)
{
    return date_format(date_create($date), 'd.m.');
}





class userController
{
    function __construct() {
        $this->USERTYPE="user";
    }

	private function checkPrivilege(){
		if (!isset($_SESSION["account_type"])){
			header( 'Location: index.php?rt=start/logout');
			exit();
		}
        if ( $_SESSION["account_type"] != $this->USERTYPE){
            header( 'Location: index.php?rt=start/logout');
			exit();
        }
	}


	public function index($danOdDanas =-1) {
		session_start();
        $this->checkPrivilege();
        //$m= new MongoService();
        $activeInd=0;

        $cs = new CinemaService();
        $cs -> erasePastProjections();

        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=0;
        $student="";
        $new_list="";

       

        $date='';
        if($danOdDanas == -1)
            $date= date("Y-m-d");
        else
            $date= date("Y-m-").$danOdDanas ;
        
        
        $movieList = $cs -> getAllProjectionsForDate($date);

        $cinema = $cs -> getCinemaInfo();

        $USERTYPE=$this->USERTYPE;
        require_once __DIR__ . '/../view/'.$USERTYPE.'/index.php';    

	}

    public function seatSelectionConfirm() { //ovdje server daje fail/pass ovisno o tom jesu li sjedala uspjesno rezervirana
		session_start();
        $cs = new CinemaService();
        //POSTAVI $_POST["seats"] za $_POST["prikaz"] U BAZU,VRATI BROJ REZERVACIJE
        header( 'Content-type:application/json;charset=utf-8' );
        
        if(!isset($_POST["seats"])) return;
        if(!isset($_POST["prikaz"])) return;
        
        
        
        $korisnik_id=1;
        $seats=[];

        //PRERADITI,PROBLEM S POSTom
        foreach($_POST["seats"] as $s){
            $t=true;
            foreach($s as $attr){
                if($t){
                    $i=$attr;
                    $t=false;
                } 
                else
                   $j=$attr;  
            }

            $seats[]= new Seat($i,$j,null);
        }


        //echo $_POST["seats"] ; 
        $m=$cs -> insertNewReservations($seats, $_POST["prikaz"] ,$korisnik_id);
        
        $message=[];
        $message[ 'uspjeh' ] =$m[ 'uspjeh' ];
        $message[ 'rezervacija' ] =$m[ 'rezervacija' ];
        


        header( 'Content-type:application/json;charset=utf-8' );
        echo json_encode($message);
        flush();
   
    }
    public function reservationSuccess($reservation_id) {
		session_start();

        

        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=0;


        $USERTYPE=$this->USERTYPE;
        require_once __DIR__ . '/../view/'.$USERTYPE.'/reservationSuccess.php';    
   
    }

    public function vratiZauzetaMjesta($prikazId){
        
        $zauzeta=[];
        $cs = new CinemaService();
        $reserved = $cs -> getReservedSeatsByProjectionId( $prikazId );
        
        
        /*$zauzeta[]= new MySeat(1,1);
        $zauzeta[]= new MySeat(2,3);
        $zauzeta[]= new MySeat(2,4);*/

        header( 'Content-type:application/json;charset=utf-8' );
        echo json_encode( $reserved );
        flush();

    }

    public function seatSelection($prikaz_id) {//DOBIJE POMOCU GETa: PRIKAZ_id 
		session_start();

        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=0;


        //U BAZU prikaz_id ------> VELICINA DVORANE I ZAUZETA MJESTA
        $cs = new CinemaService();
        $size = $cs -> getSizeOfHallByProjectionId( $prikaz_id );
        $br_redova = $size[0];
        $velicina_reda = $size[1];
        $movie = $cs -> getMovieByProjectionId( $prikaz_id );
        $projection = $cs -> getProjectionById( $prikaz_id);
        $date = datum( $projection->date);

        $USERTYPE=$this->USERTYPE;
        require_once __DIR__ . '/../view/'.$USERTYPE.'/seatSelection.php';    

    }
    
	public function myInfo() {
		session_start();





        
        

        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=1;
        
        $USERTYPE=$this->USERTYPE;
        require_once __DIR__ . '/../view/'.$USERTYPE.'/myInfo.php'; 

	}







    public function browseMovies() {  //popis filmova
		session_start();
        $this->checkPrivilege();
        
        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=2;
        $cs = new CinemaService();

        $cs -> erasePastProjections();

        $USERTYPE=$this->USERTYPE;

        $movieList = $cs -> getAllMovies();

        require_once __DIR__ . '/../view/'.$USERTYPE.'/browseMovies.php';    

	}

    public function myReservations() { //rezervacije
		session_start();
        $this->checkPrivilege();

        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=3;
        $cs = new CinemaService();
        
        $USERTYPE=$this->USERTYPE;

        $cs -> erasePastProjections();

        $reservationList = $cs -> getMoviesByUserName($ime);

        require_once __DIR__ . '/../view/'.$USERTYPE.'/myReservations.php';    

	}

    public function movie( $id )
    {
        session_start();
        $this->checkPrivilege();

        $ime=$_SESSION["user_name"];

        $naziv=$ime;

        $cs = new CinemaService();
        
        $USERTYPE=$this->USERTYPE;
        $movie = $cs -> getMovieById( $id );
        $projections = $cs -> getProjectionsByMovieId( $id );
        $dates = $cs -> getDatesByMovieId( $id );

        require_once __DIR__ . '/../view/'.$USERTYPE.'/movie.php';  
    }

    public function projection( $id ) //tu se odabiru sjedala
    {
        session_start();
        $this->checkPrivilege();

        $ime=$_SESSION["user_name"];

        $naziv=$ime;

        $cs = new CinemaService();
        
        $USERTYPE=$this->USERTYPE;

        $movie = $cs -> getMovieByProjectionId( $id );

        $hall_id = $cs -> getHallIdByProjectionId( $id );
        

        require_once __DIR__ . '/../view/'.$USERTYPE.'/projection.php';
    }

    public function cancel( $id ) //otkaži rezervaciju
    {
        session_start();
        $this->checkPrivilege();

        $ime=$_SESSION["user_name"];

        $naziv=$ime;

        $cs = new CinemaService();
        
        $USERTYPE=$this->USERTYPE;

        $cs -> cancelReservationById( $id );
        header('Location: index.php?rt=user/myReservations');
    }

    public function reservation()
    {
        session_start();
        $this->checkPrivilege();

        $ime=$_SESSION["user_name"];

        $naziv=$ime;


    }

  
    public function otherSettings() {
		session_start();
        $this->checkPrivilege();
        

        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=5;
        
        $USERTYPE=$this->USERTYPE;

        require_once __DIR__ . '/../view/'.$USERTYPE.'/otherSettings.php';   

	}

    public function otherSettingsCheck() {
		session_start();
        $this->checkPrivilege();
        $cs= new CinemaService();
        $ime=$_SESSION["user_name"];
        $naziv=$ime;
        $activeInd=5;
        
        $USERTYPE=$this->USERTYPE;

        if( isset($_POST['password']) && $_POST['password'] !== '')
            $cs -> changePassByUserId( $_SESSION['user_id'], $_POST['password']);
        if( isset($_POST['name']) && $_POST['name'] !=='')
            $cs -> changeNameByUserId( $_SESSION['user_id'], $_POST['name']);
        if( isset($_POST['email'] ) ){
            if( filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
                $cs -> changeEmailByUserId( $_SESSION['user_id'], $_POST['email']);
            }
            else{
                $_SESSION['error'] = 'Wrong email adress! Try again';
                header( 'Location: index.php?rt=user/otherSettings');
                exit();
            }
                
        }
           


        header( 'Location: index.php?rt=user/otherSettings');
        exit(); 

	}



};
?>