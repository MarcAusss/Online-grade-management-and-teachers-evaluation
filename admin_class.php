<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

		function login(){
			extract($_POST);
			$type = array("","users","faculty_list","student_list");
			$type2 = array("","admin","faculty","student");
				$qry = $this->db->query("SELECT *,concat(firstname,' ',lastname) as name FROM {$type[$login]} where email = '".$email."' and password = '".md5($password)."'  ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
						$_SESSION['login_type'] = $login;
						$_SESSION['login_view_folder'] = $type2[$login].'/';
			$academic = $this->db->query("SELECT * FROM academic_list where is_default = 1 ");
			if($academic->num_rows > 0){
				foreach($academic->fetch_array() as $k => $v){
					if(!is_numeric($k))
						$_SESSION['academic'][$k] = $v;
				}
			}
					return 1;
			}else{
				return 2;
			}
		}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = '".$student_code."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['rs_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);

				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");

		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
					$_SESSION['login_id'] = $id;
				if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user(){
		extract($_POST);
		$data = "";
		$type = array("","users","faculty_list","student_list");
	foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
				
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM {$type[$_SESSION['login_type']]} where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(!empty($password))
			$data .= " ,password=md5('$password') ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO {$type[$_SESSION['login_type']]} set $data");
		}else{
			echo "UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id";
			$save = $this->db->query("UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function save_system_settings(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set $data where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}
	
	function save_subject(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM subject_list WHERE code = '$code' AND id != '{$id}'")->num_rows;
		if($chk > 0){
			return 2;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO subject_list SET $data");
		}else{
			$save = $this->db->query("UPDATE subject_list SET $data WHERE id = $id");
		}
		if($save){
			return 1;
		}
	}
	
	function delete_subject(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM subject_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_class(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM class_list where (".str_replace(",",'and',$data).") and id != '{$id}' ")->num_rows;
		if($chk > 0){
			return 2;
		}
		if(isset($user_ids)){
			$data .= ", user_ids='".implode(',',$user_ids)."' ";
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO class_list set $data");
		}else{
			$save = $this->db->query("UPDATE class_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_class(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM class_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_academic(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM academic_list where (".str_replace(",",'and',$data).") and id != '{$id}' ")->num_rows;
		if($chk > 0){
			return 2;
		}
		$hasDefault = $this->db->query("SELECT * FROM academic_list where is_default = 1")->num_rows;
		if($hasDefault == 0){
			$data .= " , is_default = 1 ";
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO academic_list set $data");
		}else{
			$save = $this->db->query("UPDATE academic_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_academic(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM academic_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function make_default(){
		extract($_POST);
		$update= $this->db->query("UPDATE academic_list set is_default = 0");
		$update1= $this->db->query("UPDATE academic_list set is_default = 1 where id = $id");
		$qry = $this->db->query("SELECT * FROM academic_list where id = $id")->fetch_array();
		if($update && $update1){
			foreach($qry as $k =>$v){
				if(!is_numeric($k))
					$_SESSION['academic'][$k] = $v;
			}

			return 1;
		}
	}
	function save_criteria(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM criteria_list where (".str_replace(",",'and',$data).") and id != '{$id}' ")->num_rows;
		if($chk > 0){
			return 2;
		}
		
		if(empty($id)){
			$lastOrder= $this->db->query("SELECT * FROM criteria_list order by abs(order_by) desc limit 1");
		$lastOrder = $lastOrder->num_rows > 0 ? $lastOrder->fetch_array()['order_by'] + 1 : 0;
		$data .= ", order_by='$lastOrder' ";
			$save = $this->db->query("INSERT INTO criteria_list set $data");
		}else{
			$save = $this->db->query("UPDATE criteria_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_criteria(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM criteria_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_criteria_order(){
		extract($_POST);
		$data = "";
		foreach($criteria_id as $k => $v){
			$update[] = $this->db->query("UPDATE criteria_list set order_by = $k where id = $v");
		}
		if(isset($update) && count($update)){
			return 1;
		}
	}

	function save_question(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		
		if(empty($id)){
			$lastOrder= $this->db->query("SELECT * FROM question_list where academic_id = $academic_id order by abs(order_by) desc limit 1");
			$lastOrder = $lastOrder->num_rows > 0 ? $lastOrder->fetch_array()['order_by'] + 1 : 0;
			$data .= ", order_by='$lastOrder' ";
			$save = $this->db->query("INSERT INTO question_list set $data");
		}else{
			$save = $this->db->query("UPDATE question_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_question(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM question_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_question_order(){
		extract($_POST);
		$data = "";
		foreach($qid as $k => $v){
			$update[] = $this->db->query("UPDATE question_list set order_by = $k where id = $v");
		}
		if(isset($update) && count($update)){
			return 1;
		}
	}
	function save_faculty() {
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		// Save password if provided
		if (!empty($password)) {
			$data .= ", password=md5('$password') ";
		}
		// Checking for duplicates
		$check = $this->db->query("SELECT * FROM faculty_list WHERE email ='$email' " . (!empty($id) ? " AND id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		$check = $this->db->query("SELECT * FROM faculty_list WHERE school_id ='$school_id' " . (!empty($id) ? " AND id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 3;
			exit;
		}
		// File upload for avatar
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		// Insert or update the faculty record
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO faculty_list SET $data");
		} else {
			$save = $this->db->query("UPDATE faculty_list SET $data WHERE id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	
	function delete_faculty(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM faculty_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_student(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM student_list where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO student_list set $data");
		}else{
			$save = $this->db->query("UPDATE student_list set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function delete_student(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM student_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_task(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO task_list set $data");
		}else{
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_task(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_progress(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'progress')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!isset($is_complete))
			$data .= ", is_complete=0 ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO task_progress set $data");
		}else{
			$save = $this->db->query("UPDATE task_progress set $data where id = $id");
		}
		if($save){
		if(!isset($is_complete))
			$this->db->query("UPDATE task_list set status = 1 where id = $task_id ");
		else
			$this->db->query("UPDATE task_list set status = 2 where id = $task_id ");
			return 1;
		}
	}
	function delete_progress(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_progress where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_restriction(){
		extract($_POST);
		$filtered = implode(",",array_filter($rid));
		if(!empty($filtered))
			$this->db->query("DELETE FROM restriction_list where id not in ($filtered) and academic_id = $academic_id");
		else
			$this->db->query("DELETE FROM restriction_list where  academic_id = $academic_id");
		foreach($rid as $k => $v){
			$data = " academic_id = $academic_id ";
			$data .= ", faculty_id = {$faculty_id[$k]} ";
			$data .= ", class_id = {$class_id[$k]} ";
			$data .= ", subject_id = {$subject_id[$k]} ";
			if(empty($v)){
				$save[] = $this->db->query("INSERT INTO restriction_list set $data ");
			}else{
				$save[] = $this->db->query("UPDATE restriction_list set $data where id = $v ");
			}
		}
			return 1;
	}
	public function delete_restriction() {
		if (isset($_POST['rid'])) {
			$rid = $_POST['rid'];
	
			// Prepare the query to delete the record
			$stmt = $this->db->prepare("DELETE FROM restriction_list WHERE id = ?");
			$stmt->bind_param("i", $rid);
	
			// Execute the query and handle response
			if ($stmt->execute()) {
				echo 1; // Success
			} else {
				echo 0; // Failure
			}
	
			$stmt->close();
		} else {
			echo 0; // Invalid input
		}
	}
	
	
	public function search_restrictions() {
		if (isset($_POST['action']) && $_POST['action'] === 'search_restrictions') {
			$search = $this->db->real_escape_string($_POST['search']);
	
			// Adjust query for empty search (return all rows)
			$query = "
				SELECT * FROM restriction_list 
				WHERE 
					(faculty_id IN (
						SELECT id FROM faculty_list 
						WHERE firstname LIKE '%$search%' OR lastname LIKE '%$search%'
					) 
					OR class_id IN (
						SELECT id FROM class_list 
						WHERE section LIKE '%$search%'
					) 
					OR subject_id IN (
						SELECT id FROM subject_list 
						WHERE subject LIKE '%$search%'
					)
				)";
	
			if (empty($search)) {
				$query = "SELECT * FROM restriction_list";
			}
	
			$result = $this->db->query($query);
			$output = '';
			$count = 1;
	
			while ($row = $result->fetch_assoc()) {
				$faculty = $this->db->query("SELECT CONCAT(firstname, ' ', lastname) as name FROM faculty_list WHERE id = {$row['faculty_id']}")->fetch_assoc();
				$class = $this->db->query("SELECT CONCAT(curriculum, ' ', level, ' - ', section) as class FROM class_list WHERE id = {$row['class_id']}")->fetch_assoc();
				$subject = $this->db->query("SELECT CONCAT(code, ' - ', subject) as subj FROM subject_list WHERE id = {$row['subject_id']}")->fetch_assoc();
	
				$output .= "
					<tr>
						<td>{$count}</td>
						<td>{$faculty['name']}</td>
						<td>{$class['class']}</td>
						<td>{$subject['subj']}</td>
						<td class='text-center'>
							<button class='btn btn-sm btn-danger' onclick='deleteRestriction({$row['id']})'>Delete</button>
						</td>
					</tr>";
				$count++;
			}
	
			if ($output === '') {
				$output = '<tr><td colspan="5" class="text-center">No results found.</td></tr>';
			}
	
			echo $output;
		}
	}
	
	
	public function create_restriction() {
		// Use $this->db instead of $conn
		$academic_id = $_POST['academic_id'] ?? null;
		$faculty_id = $_POST['faculty_id'] ?? null;
		$class_id = $_POST['class_id'] ?? null;
		$subject_id = $_POST['subject_id'] ?? null;
	
		// Validate input data
		if (empty($academic_id) || empty($faculty_id) || empty($class_id) || empty($subject_id)) {
			error_log("Missing required fields in create_restriction: " . json_encode($_POST));
			return 0; // Failure due to missing data
		}
	
		// Prepare SQL statement
		$stmt = $this->db->prepare("INSERT INTO restriction_list (academic_id, faculty_id, class_id, subject_id) VALUES (?, ?, ?, ?)");
		if (!$stmt) {
			error_log("Failed to prepare statement in create_restriction: " . $this->db->error);
			return 0; // Failure due to statement preparation error
		}
	
		// Bind parameters
		$stmt->bind_param("iiii", $academic_id, $faculty_id, $class_id, $subject_id);
	
		// Execute and handle result
		if ($stmt->execute()) {
			return 1; // Success
		} else {
			error_log("Error in create_restriction execution: " . $stmt->error); // Log error
			return 0; // Failure
		}
	}
	

	public function update_restriction(){
		if(isset($_POST['action']) && $_POST['action'] == 'update_restriction'){
			extract($_POST);
			$stmt = $conn->prepare("UPDATE restriction_list SET faculty_id = ?, class_id = ?, subject_id = ? WHERE id = ? AND academic_id = ?");
			$stmt->bind_param("iiiii", $faculty_id, $class_id, $subject_id, $rid, $academic_id);
			if($stmt->execute()){
				echo 1;
			} else {
				echo 0;
			}
		}
	}
	public function save_evaluation() {
		include 'db_connect.php'; // Ensure proper DB connection
	
		$academic_id = $_POST['academic_id'];
		$faculty_id = $_POST['faculty_id'];
		$class_id = $_POST['class_id'];
		$subject_id = $_POST['subject_id'];
		$student_id = $_SESSION['login_id'];
		$date_taken = date('Y-m-d H:i:s');
		$comments = isset($_POST['comments']) ? trim($_POST['comments']) : ''; // Fetch comments
	
		// Check for restrictions
		$restriction_check_query = "
			SELECT * FROM restriction_list 
			WHERE academic_id = ? AND faculty_id = ? AND class_id = ? AND subject_id = ?
		";
		$stmt = $conn->prepare($restriction_check_query);
		$stmt->bind_param("iiii", $academic_id, $faculty_id, $class_id, $subject_id);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows === 0) {
			return json_encode(['status' => 'error', 'message' => 'You are not allowed to evaluate this combination.']);
		}
	
		// Insert into evaluation_list
		$insert_query = "
			INSERT INTO evaluation_list (academic_id, class_id, student_id, subject_id, faculty_id, date_taken) 
			VALUES (?, ?, ?, ?, ?, ?)
		";
		$stmt = $conn->prepare($insert_query);
		$stmt->bind_param("iiisss", $academic_id, $class_id, $student_id, $subject_id, $faculty_id, $date_taken);
		$stmt->execute();
		$evaluation_id = $conn->insert_id;
	
		// Insert answers into evaluation_answers (for ratings)
		foreach ($_POST['rate'] as $question_id => $rate) {
			$insert_answer = "
				INSERT INTO evaluation_answers (evaluation_id, question_id, rate, comment) 
				VALUES (?, ?, ?, NULL)  -- Comment is NULL for ratings
			";
			$stmt = $conn->prepare($insert_answer);
			$stmt->bind_param("iid", $evaluation_id, $question_id, $rate);
			$stmt->execute();
		}
	
		// Insert the comment (only once) if provided
		if (!empty($comments)) {
			$insert_comment = "
				INSERT INTO evaluation_answers (evaluation_id, question_id, rate, comment) 
				VALUES (?, NULL, NULL, ?)
			";
			$stmt = $conn->prepare($insert_comment);
			$stmt->bind_param("is", $evaluation_id, $comments);
			$stmt->execute();
		}
	
		return json_encode(['status' => 'success', 'message' => 'Evaluation successfully saved.']);
	}
	
	

	
	function get_class(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT c.id,concat(c.curriculum,' ',c.level,' - ',c.section) as class,s.id as sid,concat(s.code,' - ',s.subject) as subj FROM restriction_list r inner join class_list c on c.id = r.class_id inner join subject_list s on s.id = r.subject_id where r.faculty_id = {$fid} and academic_id = {$_SESSION['academic']['id']} ");
		while($row= $get->fetch_assoc()){
			$data[]=$row;
		}
		return json_encode($data);



	}
	function get_report() {
		extract($_POST);
		$data = array();
	
		// Fetch evaluation answers for ratings and comments
		$get = $this->db->query("
			SELECT question_id, rate, comment 
			FROM evaluation_answers 
			WHERE evaluation_id IN (
				SELECT evaluation_id 
				FROM evaluation_list 
				WHERE academic_id = {$_SESSION['academic']['id']} 
				AND faculty_id = $faculty_id 
				AND subject_id = $subject_id 
				AND class_id = $class_id
			)
		");
	
		// Fetch the total number of evaluations
		$answered = $this->db->query("
			SELECT * 
			FROM evaluation_list 
			WHERE academic_id = {$_SESSION['academic']['id']} 
			AND faculty_id = $faculty_id 
			AND subject_id = $subject_id 
			AND class_id = $class_id
		");
	
		$rate = array();
		$comments = array();
	
		while ($row = $get->fetch_assoc()) {
			// Handle ratings
			if (!isset($rate[$row['question_id']][$row['rate']])) {
				$rate[$row['question_id']][$row['rate']] = 0;
			}
			$rate[$row['question_id']][$row['rate']] += 1;
	
			// Collect comments if available
			if (!empty($row['comment'])) {
				$comments[] = $row['comment'];
			}
		}
	
		// Calculate the percentage ratings
		$ta = $answered->num_rows;
		$r = array();
		foreach ($rate as $qk => $qv) {
			foreach ($qv as $rk => $rv) {
				$r[$qk][$rk] = ($rate[$qk][$rk] / $ta) * 100;
			}
		}
	
		// Prepare the response data
		$data['tse'] = $ta;
		$data['data'] = $r;
		$data['comments'] = $comments;
	
		return json_encode($data);
	}
	
	
	public function fetch_questions() {
		include 'db_connect.php'; // Adjust this to your database connection file
	
		$academic_id = $_SESSION['academic']['id'];
		$faculty_id = $_POST['faculty_id'];
		$class_id = $_POST['class_id'];
		$subject_id = $_POST['subject_id'];
	
		// Query to fetch questions with criteria
		$query = "
			SELECT q.*, c.criteria 
			FROM question_list q 
			INNER JOIN criteria_list c ON c.id = q.criteria_id 
			WHERE q.academic_id = '$academic_id'
			ORDER BY c.criteria ASC, ABS(q.order_by) ASC
		";
		$questions = $conn->query($query);
	
		$current_criteria = '';
		$response = '';
	
		while ($row = $questions->fetch_assoc()) {
			// If the criteria has changed, close the previous table and start a new one
			if ($current_criteria != $row['criteria']) {
				// Close previous table if one exists
				if ($current_criteria != '') {
					$response .= "</tbody></table>";
				}
	
				// Start new table for the new criteria
				$current_criteria = $row['criteria'];
				$response .= "
					<table class='table table-condensed'>
						<thead>
							<tr class='bg-gradient-secondary'>
								<th>{$row['criteria']}</th>
								<th class='text-center'>1</th>
								<th class='text-center'>2</th>
								<th class='text-center'>3</th>
								<th class='text-center'>4</th>
								<th class='text-center'>5</th>
							</tr>
						</thead>
						<tbody>
				";
			}
	
			// Add the question to the current table
			$response .= "
				<tr class='bg-white'>
					<td>{$row['question']}</td>
					<td><input type='radio' name='rate[{$row['id']}]' value='1' required></td>
					<td><input type='radio' name='rate[{$row['id']}]' value='2' required></td>
					<td><input type='radio' name='rate[{$row['id']}]' value='3' required></td>
					<td><input type='radio' name='rate[{$row['id']}]' value='4' required></td>
					<td><input type='radio' name='rate[{$row['id']}]' value='5' required></td>
				</tr>
			";
		}
	
		// Close the last table if any questions were added
		if ($current_criteria != '') {
			$response .= "</tbody></table>";
		}
	
		// Append comments section at the bottom
		$response .= "
			<div class='form-group'>
				<label for='comments'>Additional Comments/Suggestions (optional)</label>
				<textarea id='comments' name='comments' class='form-control' rows='4'></textarea>
			</div>
		";
	
		return $response;
	}
	
	

	function submit_grade() {
		// Ensure POST data is available
		if (isset($_POST['student_id'], $_POST['subject_id'], $_POST['grade'], $_POST['term'])) {
			$student_id = $_POST['student_id'];
			$subject_id = $_POST['subject_id'];
			$grade = $_POST['grade'];
			$term = $_POST['term']; // Get the term value
			$faculty_id = $_SESSION['login_id']; // Assuming this is set
	
			// Prepare the query
			$query = "INSERT INTO grades (student_id, subject_id, faculty_id, grade, term) VALUES (?, ?, ?, ?, ?)";
			$stmt = $this->db->prepare($query);
	
			// Bind parameters
			$stmt->bind_param("iiids", $student_id, $subject_id, $faculty_id, $grade, $term);
	
			// Execute the query
			if ($stmt->execute()) {
				echo "Grade submitted successfully.";
			} else {
				echo "Error submitting grade: " . $stmt->error;
			}
	
			// Close the statement
			$stmt->close();
		} else {
			echo "Invalid input.";
		}
	}
	
	public function edit_grade() {
		if (isset($_POST['grade_id'], $_POST['grade'])) {
			$grade_id = $_POST['grade_id'];
			$grade = $_POST['grade'];
	
			// Update the grade based on the grade_id
			$query = "UPDATE grades SET grade=? WHERE id=?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("di", $grade, $grade_id); // Bind grade and grade_id
	
			if ($stmt->execute()) {
				echo "Grade updated successfully.";
			} else {
				echo "Error updating grade: " . $stmt->error;
			}
	
			$stmt->close();
		} else {
			echo "Invalid input.";
		}
	}
	
	

    public function delete_grade() {
		if (isset($_POST['grade_id'])) {
			$grade_id = $_POST['grade_id'];
	
			// Delete the grade record based on the grade_id
			$query = "DELETE FROM grades WHERE id=?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $grade_id); // Bind grade_id
	
			if ($stmt->execute()) {
				echo "Grade deleted successfully.";
			} else {
				echo "Error deleting grade: " . $stmt->error;
			}
	
			$stmt->close();
		} else {
			echo "Invalid input.";
		}
	}
	
	

	// public function fetch_class() {
	// 	global $conn; // Make sure to access the connection
	
	// 	$faculty_id = $_POST['faculty_id'];
	
	// 	// Query to fetch classes based on faculty_id
	// 	$query = "
	// 		SELECT DISTINCT c.id as cid, CONCAT(c.curriculum, ' - ', c.level, ' ', c.section) as class 
	// 		FROM restriction_list r 
	// 		INNER JOIN class_list c ON c.id = r.class_id 
	// 		WHERE r.faculty_id = '$faculty_id' AND r.academic_id = {$_SESSION['academic']['id']}
	// 	";
	
	// 	$result = $conn->query($query);
	
	// 	if ($result) {
	// 		// Generate options for the class dropdown
	// 		$options = '<option value="">-- Select Class --</option>';
	// 		while ($row = $result->fetch_assoc()) {
	// 			$options .= '<option value="' . $row['cid'] . '">' . $row['class'] . '</option>';
	// 		}
	// 		echo $options;
	// 	} else {
	// 		echo '<option value="">Error fetching classes</option>'; // Handle query error
	// 	}
	// }
	
	// public function fetch_subject() {
	// 	global $conn; // Make sure to access the connection
	
	// 	$faculty_id = $_POST['faculty_id'];
	
	// 	// Query to fetch subjects based on faculty_id
	// 	$query = "
	// 		SELECT DISTINCT s.id as sid, s.code as subject_code, s.subject 
	// 		FROM restriction_list r 
	// 		INNER JOIN subject_list s ON s.id = r.subject_id 
	// 		WHERE r.faculty_id = '$faculty_id' AND r.academic_id = {$_SESSION['academic']['id']}
	// 	";
	
	// 	$result = $conn->query($query);
	
	// 	if ($result) {
	// 		// Generate options for the subject dropdown
	// 		$options = '<option value="">-- Select Subject --</option>';
	// 		while ($row = $result->fetch_assoc()) {
	// 			$options .= '<option value="' . $row['sid'] . '">' . $row['subject_code'] . ' - ' . $row['subject'] . '</option>';
	// 		}
	// 		echo $options;
	// 	} else {
	// 		echo '<option value="">Error fetching subjects</option>'; // Handle query error
	// 	}
	// }
	
}