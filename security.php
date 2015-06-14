<?php
class security
{
	/*
	* At this point you should choose either start a session or not! Also if you consider about checking for bruteforce attacks you should pass 
	* TRUE to the class! for more information refer to documentation!
	*/
	
		function __construct($session_start_check,$check_brute)
		{
			/* load configuration file! */
			
			include("config.php");
			
			/* check either user wants sessions turned on or off! WARNING!: for using secure_session function you should allow sessions! */
			
			if($session_start_check == TRUE)
			{
				session_start();
				session_regenerate_id(true);
			}
			
			/* check either user wants to check for brute force attacks or not! WARNING!: for using brute and debrute function you should allow
			* us to check for current ip and attempts! */
			
			if($check_brute == TRUE)
			{
				try
				{
					/* you can change the max attempts! since the code will not allow other codes to be executed if the attempts are greater than
					* max_attempts! so change the max_attempts to satisfies your needs! */
					
					$max_attempt = 3;
					$db = new pdo('mysql:host=localhost;dbname='.db_name,db_user,db_pass);
					$ip = $_SERVER['REMOTE_ADDR'];
					$check = $db->prepare('SELECT attempts FROM brute Where ip = :ip');
					$check->bindparam(':ip',$ip);
					$check->execute();
					$count = $check->fetch(PDO::FETCH_ASSOC);
					if($count['attempts'] > $max_attempt)
					{
						echo "you were an asshole! get the fuck out!";
						exit();
					}
						}
				catch(pdoexception $e)
				{
					echo $e->getMessage();
				}
			}
		}
		
		/*
		* I have done my best to bring more secure session with less codes! I didn't want to make it verbose so users wont face any problem! 
		* for usage and changes refer to documentation!
		*/
		

		var $session_name;

		function secure_session($session_name)
		{
			if (ini_set('session.use_only_cookies', 1) === FALSE) 
			{
        	echo "we could not start a secure session!";
        	exit();
    		}
    		$cookieParams = session_get_cookie_params();
    		session_set_cookie_params($cookieParams["lifetime"],
        	$cookieParams["path"], 
    	    $cookieParams["domain"], 
    	    "SECURE",
    	    TRUE);
    	    session_name($session_name);
    	}
    	
		/*
		* SAFE_GET will stop SQL injection from being executed! There are more ways! This is the most simple way! WARNING:! This method only
		* support integer values! Char and string are not supported yet!
		*/
	
	
			function safe_get($get_parameter)
			{
				$get_parameter = $_GET[$get_parameter];
				$get_parameter = intval($get_parameter);
				return $get_parameter;
			}
			
			
			/* here is the most useful function of this class! Brute function will count and check failed login attempts! You can use it
			* for other cases but mostly it will be used for login! As I have said before change the Max_attempts for your use! I think 3 is
			* enough but if you want to give more chances to your users...go On!
			*/
			
			function brute()
			{
				try
				{
					$db = new pdo('mysql:host=localhost;dbname='.db_name,db_user,db_pass);
					$ip = $_SERVER['REMOTE_ADDR'];
					$agent = $_SERVER['HTTP_USER_AGENT'];
					$time = time();
					$select = $db->prepare('SELECT ip FROM brute Where ip = :ip');
					$select->bindparam(':ip',$ip);
					$select->execute();
					if($select->rowcount() == 1)
					{
						$update = $db->prepare('UPDATE brute set attempts = attempts + 1 , time = :time where ip = :ip');
						$update->bindparam(':time',$time);
						$update->bindparam(':ip',$ip,PDO::PARAM_INT);
						$update->execute();
					}
					else
					{
						$insert = $db->prepare('INSERT INTO brute (ip,agent,attempts,time) VALUES(:ip,:agent,1,:time)');
						$insert->bindparam(':ip',$ip,PDO::PARAM_INT);
						$insert->bindparam(':agent',$agent);
						$insert->bindparam(':time',$time);
						$insert->execute();
					}
				}
				catch(pdoexception $e)
				{
					echo "Error:".$e->getMessage()." !";
					exit();
				}
			}
			
			/* debrute function should be used with brute function in two different situations! if the login is successful! you should use debrute function 
			* in order to delete the record of the brute function! and if the login fails you should use brute function in order to record it to database!
			* WARNING!: since user achieve its maximum attempts debrute function will not execute and the user will get banned from your website permanently!
			* until you delete the users record manually! i have not work on timing yet...i will and you may see this feature soon!
			*/
			
			function debrute()
			{
				try
				{
					$db = new pdo('mysql:host=localhost;dbname='.db_name,db_user,db_pass);
					$ip = $_SERVER['REMOTE_ADDR'];
					$del = $db->prepare('delete FROM brute Where ip = :ip');
					$del->bindparam(':ip',$ip,PDO::PARAM_INT);
					$del->execute();
				}
				catch(pdoexception $e)
				{
					echo $e->getMessage();	
				}
			}
}
?>
