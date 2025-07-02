<?php
require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/user.class.php';
require_once __DIR__ . '/employee.class.php';
require_once __DIR__ . '/admin.class.php';


class UserService
{
    function loginUser($email, $password)
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM korisnik WHERE email=:email');
			$st->execute( array( 'email' => $email ) );			
		}
		catch( PDOException $e ) { 
            return $e->getMessage();
        
        }

        $row = $st->fetch();

        $user = null;

        if (isset($row['id']))
        {
            if( password_verify( $password, $row['password_hash'] ) )
                $user = new User($row['id'], $row['ime'], $row['email'], $password);                
            else
                return 'incorrect';
        } else
            return 'notFound';

		return $user;
    }

    function loginEmployee($username, $password)
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM radnik WHERE email=:username');
			$st->execute( array( 'username' => $username ) );			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $row = $st->fetch();

        $employee = null;

        if (isset($row['id']))
        {
            if( password_verify( $password, $row['password_hash'] ) )
                $employee = new Employee($row['id'], $row['ime'], $row['email']);                
            else
                return 'incorrect';
        } else
            return 'notFound';

		return $employee;
    }

    function loginAdmin($username, $password)
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM admin WHERE email=:username');
			$st->execute( array( 'username' => $username ) );			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $row = $st->fetch();

        $admin =null;

        if (isset($row['id']))
        {
            if( password_verify( $password, $row['password_hash'] ) )
                $admin = new Admin($row['id'], $row['ime'], $row['email'], $password);                
            else
                return 'incorrect';
        } else
            return 'notFound';

		return $admin;
    }

    function checkUsername($email)
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM korisnik WHERE email=:email');
			$st->execute( array( 'email' => $email ) );			

	

		}
		catch( PDOException $e ) { 
            return "test " .$e->getMessage() ; }

        //$row = $st->fetch();    
        
        if ($st->rowCount() != 0)
            return True;
        else
            return False;
    }

    function registerUser($username, $user_name, $password)
    {
        try
		{
			$db = DB::getConnection();
            $hash = password_hash( $password, PASSWORD_DEFAULT );
			$st = $db->prepare('INSERT INTO korisnik ( ime, email, password_hash) 
                VALUES( :ime, :username, :password_hash)');

			

			$st->execute( array(  'ime' => $user_name, 'username' => $username, 'password_hash' => $hash) );
			
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
    }
}


?>