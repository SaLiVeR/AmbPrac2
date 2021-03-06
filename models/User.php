<?php
class User{
	public  $ID, $username, $last_name, $first_name, $password, $email, $kp_employee, $reloginDigest, $position, $service_area, $campus, $active, $val_key, $creation_date;
	
	function __construct($username = null){
		if($username !== null){
			$this->getUser($username);
		}
	}

	function getUser($username){
		$username = db()->real_escape_string($_POST['username']);
		$salt = "1234124k12ljKJSDklasjdkljj214l1j24j";
		$password = sha1(db()->real_escape_string($_POST['password']).$salt);
		
		$sql = "SELECT * FROM `users` WHERE `username` = '$username' LIMIT 1";
		$result = db()->query($sql);
		if($result && $result->num_rows > 0){
			$row = $result->fetch_object();
			$this->fillData($row);
			return true;
		}else{
			return false;
		}
	}

	function fillData($row){
		$this->ID = $row->ID;
		$this->username = $row->username;
		$this->last_name = $row->last_name;
		$this->first_name = $row->first_name;
		$this->password = $row->password;
		$this->email = $row->email;
		$this->kp_employee = $row->kp_employee;
		$this->reloginDigest = $row->reloginDigest;
		$this->position = $row->position;
		$this->service_area = $row->service_area;
		$this->campus = $row->campus;
		$this->active = $row->active;
		
	}

	function loginUser($_POST){
		$username = db()->real_escape_string($_POST['username']);
		$salt = "1234124k12ljKJSDklasjdkljj214l1j24j";
		$password = sha1(db()->real_escape_string($_POST['password']).$salt);
		
		$sql = "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password' LIMIT 1";
		$result = db()->query($sql);
		if($result && $result->num_rows > 0){
			$row = $result->fetch_object();
			$this->fillData($row);
			$this->setSessions();
			if(isset($_POST['remember']) && $_POST['remember'] == true){
/* 				$this->setCookies(); */
			}
			return false;
		}else{
			$error['login_error'] = "Username/Password combination were invalid.<br/>Please try to login again.";
		}
		return $error;
	}

	function setSessions(){
		$_SESSION['First_name'] = $this->first_name;
		$_SESSION['Logged_in'] = true;
		$_SESSION['User_ID'] = $this->ID;
		$_SESSION['kp_employee'] = $this->kp_employee;
	}
	
	function setCookies(){
		$digest = sha1(rand(1,20).$this->username.rand(1,20));
		$sql = "UPDATE users SET reloginDigest = '$digest' WHERE username = '$this->username' LIMIT 1";
		db()->query($sql);
		setcookie( 'reloginID', $digest, time()+60*60*24*7,'/','http://ambpracdev.org', false, true);
	}

	function createUser($obj){
		//sql injection cleaning;
		$username = db()->real_escape_string($obj->username);
		$salt = "1234124k12ljKJSDklasjdkljj214l1j24j";
		$password = sha1(db()->real_escape_string($obj->password).$salt);
		$email = db()->real_escape_string($obj->email);
		$first_name = db()->real_escape_string($obj->first_name);
		$last_name = db()->real_escape_string($obj->last_name);
		$val_key = sha1($first_name.$last_name.$username);
		$active = 1;

		$error = array();
		//check to see if username is already in use
		$sql = "SELECT count(*) AS count FROM `users` WHERE `username` = '$username'";
		$result = db()->query($sql);
		$row = $result->fetch_object();
		if($row->count > 0){
			$error['username_error'] = "Username is already registered";
		}
		else{
			//check to see if email is already in use
			$sql = "SELECT count(*) AS count FROM `users` WHERE `email` = '$email'";
			$result = db()->query($sql);
			$row = $result->fetch_object();
			if($row->count > 0){
				$error['email_error'] = "Email is already registered";
			}
			else{
				//create the user
				$sql = "INSERT INTO `users`(username, last_name, first_name, password, email, title, area, active, val_key)
				VALUES('$username','$last_name','$first_name','$password','$email','$obj->title','$obj->area', '$active','$val_key')";
				if(db()->query($sql)){
					$this->setSessions();
					
				}else{
					echo $sql;
					echo db()->error;
				}
			}
		}	
		
		return $error;
	}

	



	public static function checkEmployee(){
		if ($_SESSION['Logged_in'] == true && isset($_SESSION['User_ID']) && $_SESSION['kp_employee'] == 'yes'){
			//user is logged in and is a  KP employee
			$check = 'kp_employee';
			return $check;
		}
		elseif ($_SESSION['Logged_in'] == true && isset($_SESSION['User_ID']) && $_SESSION['kp_employee'] == 'no'){
			//user is logged in but is not a KP employee
			$check = 'other';
			return $check;
		}
		else {
			//user is not logged in
			return false;
		}
	}

	public static function logout_User(){
		$_SESSION['First_name'] = '';
		$_SESSION['Logged_in'] = '';
		$_SESSION['User_ID'] = '';
		$_SESSION['kp_employee'] = '';
	}

	public static function check_registration($_POST){
		$error = array();
		//check username for bad characters
		if (!empty($_POST['username'])){
			if (preg_match("/[^a-zA-Z0-9]+$/", $_POST['username'])) {
				$error['username_error'] = "Username contains invalid character(s)"; 
			}
		}
		else {
			$error['username_error'] = "Username can not be blank";
		}
		//check passwords
		if (!empty($_POST['password']) && (!empty($_POST['confirm_password']))){
			if ($_POST['password'] == $_POST['confirm_password']){
				if (preg_match("/^(?=.*\d+)(?=.*[a-zA-Z])[0-9a-zA-Z!@#$%]{6,}$/", $_POST['password'])){
					//password is valid
				}
				else {
					$error['password_error'] = "Password contains invalid character(s)";
				}
			}
			else {
				$error['password_error'] = "Passwords do not match";
				$error['comfirmpassword_error'] = "Passwords do not match";
			}
		}
		else {
			if (empty($_POST['password'])){
				$error['password_error'] = "Password can not be left blank";
			}
			if (empty($_POST['confirm_password'])){
				$error['comfirmpassword_error'] = "Comfirm password can not be left blank";
			}
		}
		
		//check first/last name for bad characters
		if (!empty($_POST['first_name'])){
			if (preg_match("/[^a-zA-Z]+$/", $_POST['first_name'])) { $error['firstname_error'] = "First name contains invalid character(s)"; }
		}
		else { $error['firstname_error'] = "First name can not be blank"; }
		
		if (!empty($_POST['last_name'])){
			if (preg_match("/[^a-zA-Z]+$/", $_POST['last_name'])) { $error['lastname_error'] = "Last name contains invalid character(s)"; }
		}
		else {
			$error['lastname_error'] = "Last name can not be blank";
		}
		
		//check valid email
		if (!empty($_POST['email'])){
			if (preg_match("/\b^[a-zA-Z0-9._]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$\b/", $_POST['email'])) {
				//email address is correctly formatted
			}
			else { $error['email_error'] = "Email format is incorrect"; }
		}
		else {
			$error['email_error'] = "Email can not be blank";
		}
		
		//check title/area isn't blank
		if (empty($_POST['title'])){
			$error['title_error'] = "Title can not be blank";
		}
		if (empty($_POST['area'])){
			$error['area_error'] = "Area can not be blank";
		}
		
		return $error;
	}

}

?>

