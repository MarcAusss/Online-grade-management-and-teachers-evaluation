<?php 
include('db_connect.php');

// Function to add ordinal suffix (1st, 2nd, 3rd, etc.)
function ordinal_suffix1($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}

// Fetch the academic year where `is_default = 1`
$academic = $conn->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();

// Check if academic year exists and update session
if($academic) {
    $_SESSION['academic'] = array(
        'id' => $academic['id'],
        'year' => $academic['year'],
        'semester' => $academic['semester'],
        'status' => $academic['status']  // Assuming status field holds evaluation status
    );
}

// Array for evaluation status text
$astat = array("Not Yet Started", "Started", "Closed");
?>

<!-- Display the Welcome message with Academic Year and Evaluation Status -->
<div class="col-12">
    <div class="card">
      <div class="card-body">
        Welcome <?php echo $_SESSION['login_name'] ?>!
        <br>
        <div class="col-md-5">
          <div class="callout callout-info">
            <!-- Display the academic year and semester with ordinal suffix -->
            <h5><b>Academic Year: <?php echo $_SESSION['academic']['year'] . ' ' . (ordinal_suffix1($_SESSION['academic']['semester'])) ?> Semester</b></h5>
            
            <!-- Display the evaluation status based on the session value -->
            <h6><b>Evaluation Status: <?php echo $astat[$_SESSION['academic']['status']] ?></b></h6>
          </div>
        </div>
      </div>
    </div>
</div>
