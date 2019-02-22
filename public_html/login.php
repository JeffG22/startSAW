<!DOCTYPE html>
<html lang="en">

<head>
  <title>Log In</title>
    
  <!--Boostrap-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
	        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
        	integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
          integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" 
				integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <!---Bootstrap-->
        
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!--Inclusions-->
  <script src="js/include.js"></script> 
  
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" type="text/css" href="css/login.css">

</head>


<body>


  <?php
    session_start();
    if (isset($_SESSION['user_id']){
      header("location: user.php");
      exit();
    }
  ?>

  <?php
		include("php/navbar.php")
	?>

  <div class="container">
      <!--Login-->
      <div id="logcon" class="form-group">
          <form name="login" id="login" class="form-in" method="post" 
                action="authentication.php">
              <legend>Log In</legend>
              <!--Username box-->
              <label for="usr">Username:</label>
              <input type="text" name="usr" id="usr" class="form-control input-in" required>
              <!--Pwd box-->
              <label for="pwd">Password:</label>
              <input type="password" name="pwd" id="pwd" class="form-control input-in" required>
              <!--Submit button-->
              <div class="btn-container">
                <button type="submit" class="btn btn-primary">Log In</button>
              </div>
          </form>
      </div>
      <!--Login-->
  </div>

</body>
</html>