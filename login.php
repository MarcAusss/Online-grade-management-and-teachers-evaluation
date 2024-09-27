<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
  ob_start();
  // if(!isset($_SESSION['system'])){

    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
  // }
  ob_end_flush();
?>
<?php 
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

?>
<?php include 'header.php' ?>
<body class="hold-transition main-body">
  <div class="login-body">
    <div class="body-design-login">
      <img src="img/bg.png" alt="" class="">
    </div>
    <div class="body-login-left text-center">
      <div class="login-box">
        <!-- /.login-logo -->
        <div class="card">
          <h2><b> <span style="font-size: 1.5rem !important; color: black;">Welcome to</span> <br> Dr Ruby Lanting Casaul College</b></h2>

          <div class="card-body login-card-body">
            <form action="" id="login-form">
              <div class="input-group mb-3">
                <input type="email" class="form-control" name="email" required placeholder="Email">
                <div class="input-group-append">
                </div>
              </div>
              <div class="input-group mb-3">
                <input type="password" class="form-control" name="password" required placeholder="Password">
                <div class="input-group-append">
                </div>
              </div>
              <div class="form-group mb-3">
                <label for="">Login As</label>
                <select name="login" id="" class="custom-select custom-select-sm">
                  <option value="3">Student</option>
                  <option value="2">Faculty</option>
                  <option value="1">Admin</option>
                </select>
              </div>
              <div class="row">
                <!-- /.col -->
                <div class="flex justify-content-center" style="width: 100%;">
                  <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
                <!-- /.col -->
              </div>
            </form>
          </div>
          <!-- /.login-card-body -->
        </div>
      </div>
    </div>
    <div class="body-login-left">
      <div style="background: maroon; padding: 10px; border-radius: 50%; margin-right: 30px">
        <img src="./img/1000001215 (2).webp" alt="" style="width: 300px">
      </div>
    </div>
  </div>
<!-- /.login-box -->
<script>
  $(document).ready(function(){
    $('#login-form').submit(function(e){
    e.preventDefault()
    start_load()
    if($(this).find('.alert-danger').length > 0 )
      $(this).find('.alert-danger').remove();
    $.ajax({
      url:'ajax.php?action=login',
      method:'POST',
      data:$(this).serialize(),
      error:err=>{
        console.log(err)
        end_load();

      },
      success:function(resp){
        if(resp == 1){
          location.href ='index.php?page=home';
        }else{
          $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>')
          end_load();
        }
      }
    })
  })
  })
</script>
<?php include 'footer.php' ?>

<style>
  .main-body{
    display: flex;
    justify-content: center !important;
    align-items: center !important;
    width: 100vw !important;
    height: 100vh !important;
    /* padding: 150px 200px; */
    position: relative;
    overflow: hidden;

    .login-body{
      display: flex;
      width: 100%;
      justify-content: space-between !important;
      align-items: center !important;
      padding: 0 250px;
      position: relative !important;
      z-index: 999;

      .body-login-left{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        
        h2{
          color: maroon;
          font-size: 1.7rem;

        }
        .login-box{
          .card{
            border: none !important;
            padding: 20px 30px;
            border-radius: 10px !important;
            box-shadow:   1px 13px 25px -15px rgba(0,0,0,0.81);
            -webkit-box-shadow: 1px 13px 25px -15px rgba(0,0,0,0.81);
            -moz-box-shadow: 1px 13px 25px -15px rgba(0,0,0,0.81); !important;
            width: 30vw;

            .form-control{
              border: none !important;
              border-bottom: 2px solid #5E1F32 !important;
              border-radius: 0 !important;
            }
            .form-control:focus{
              border-bottom: 2px solid #dfd2d6 !important;
              transition: .4s ease;
            }

            .btn{
              background-color: #6e3547 !important;
            }
          }
        }
      }

      .body-design-login{
        position: absolute;
        z-index: -90;
        right: -30rem;
      }
    }
  }
  

</style>
</body>
</html>
