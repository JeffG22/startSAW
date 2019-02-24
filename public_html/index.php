<?php
	require_once("php/handlesession.php");
	my_session_start();
	//print_r($_SESSION);
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <title>Volontari per caso</title>
    
  <!--Bootstrap-->
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
	<!--Bootstrap-->

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!--Inclusions-->
  <script src="js/include.js"></script> 
 	<script src="js/jquery-3.1.0.min.js"></script>
	<script src="js/jquery.easing.min.js"></script>
	
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/index.css">
</head>

<body>
	
	<!--Header/Navbar-->
	<?php include("php/navbar.php"); ?>
	<!--Header/Navbar-->

	<div id="mainContainer">
		
		<!--First view-->
		<div id="firstView">
			<!--Carousel-->
			<div id="carouselTop" class="carousel slide" data-ride="carousel">
	  		<div class="carousel-inner">
	  			<!--First slide-->
  			  <div id="firstSlide" class="carousel-item active">
    		  	<img class="d-block img-fluid" src="" alt="">
    		  	<span class="sr-only">First slide</span>
    		    <div class="carousel-caption d-none d-md-block">
    					<h5>Cerca tra decine di proposte quella adatta a te!</h5>
    					<p>Puoi essere volontario per un giorno, volontario per la vita!</p>
  					</div>
    			</div>
    			<!--First slide-->
    			<!--Second slide-->
    			<div id="secondSlide" class="carousel-item">
    		  	<img class="d-block img-fluid" src="" alt="">
    		  	<span class="sr-only">Second slide</span>
    		  	<div class="carousel-caption d-none d-md-block" style="display: flex; height: 120px; color: #dedede; background: #222; background: rgba(0,0,0,0.7); left: 0px; width: 100%;">
							<h5 style="font-size: 30px; word-spacing: 4px; color: #ffffff; margin-top: -20px;">Ciò che abbiamo fatto solo per noi stessi muore con noi. Ciò che abbiamo fatto per gli altri e per il mondo resta ed è immortale.</h5>
    					<p style="font-size: 15px; word-spacing: 4px; color: #ffffff; ">Harvey B. Mackay</p>
  					</div>
    			</div>
    			<!--Second slide-->
    			<!--Third slide-->
    			<div id="thirdSlide" class="carousel-item">
   			  	<img class="d-block img-fluid" src="" alt="">
   			  	<span class="sr-only">Third slide</span>
   			  	<div class="carousel-caption d-none d-md-block" style="display: flex; height: 80px; color: #dedede; background: #222; background: rgba(0,0,0,0.7); left: 0px; width: 100%; ">
    					<h5 style="font-size: 30px; word-spacing: 4px; color: #ff8c00; margin-top: -20px;">Prendere riempie le mani, dare riempie il cuore</h5>
    					<p style="font-size: 15px; word-spacing: 4px; color: #ff8c00; ">M. Seeman</p>
  					</div>
   			 	</div>
   			 	<!--Third slide-->
  			</div>
  			<a class="carousel-control-prev" href="#carouselTop" role="button" data-slide="prev">
  			  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
   				<span class="sr-only">Previous</span>
  			</a>
  			<a class="carousel-control-next" href="#carouselTop" role="button" data-slide="next">
   			 	<span class="carousel-control-next-icon" aria-hidden="true"></span>
   			 	<span class="sr-only">Next</span>
  			</a>
  			<ol class="carousel-indicators">
  			  <li data-target="#carouselTop" data-slide-to="0" class="active"></li>
  			  <li data-target="#carouselTop" data-slide-to="1"></li>
 	  	 		<li data-target="#carouselTop" data-slide-to="2"></li>
 				</ol>
			</div>
			<!--Carousel-->
			
			<!--Continue button-->
			<div id="continue">
				<a href="#secondView"><button id="continueBtn" type="button" value="Scopri di più">Scopri di più</button></a>
			</div>
			<!--Continue button-->
		</div>
		<!--First view-->
	
		<!--Second view-->
		<div id="secondView" class="mainView">
    	<!--Description-->
    	<div id="desContainer" class="contain">
				<!-- Three columns of text below the carousel -->
				<div class="row">
          <div class="three-columns col-lg-4">
            <img class="rounded-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
            <h2>Heading</h2>
            <p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Praesent commodo cursus magna.</p>
            <p><a class="btn btn-secondary" href="#" role="button">View details &raquo;</a></p>
          </div><!-- /.col-lg-4 -->
          <div class="three-columns col-lg-4">
            <img class="rounded-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
            <h2>Heading</h2>
            <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh.</p>
            <p><a class="btn btn-secondary" href="#" role="button">View details &raquo;</a></p>
          </div><!-- /.col-lg-4 -->
          <div class="three-columns col-lg-4">
            <img class="rounded-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
            <h2>Heading</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-secondary" href="#" role="button">View details &raquo;</a></p>
          </div><!-- /.col-lg-4 -->
        </div><!-- /.row -->
			</div>
			<!--Description-->	
    </div>
    <!--Second view--> 
	
		<!--Third view-->
		<div vs-anchor="thirdView" class="mainview">
    	<!--Testimonials-->
    	<div id="testimContainer" class="contain">asd</div>
			<!--Testimonials-->	
    </div>
		<!--Third view--> 
		
		<!--Fourth view-->
		<div vs-anchor="fourthView" class="mainview">
    	<!--Map-->
    	<div id="mapContainer" class="contain">asdjasf</div>
			<!--Map-->

		</div>	
    <!--Fourth view-->
	
		<!--Third view-->
		<div vs-anchor="fifthView" class="mainview">
			<!--Footer-->
			<?php
				include("php/footer.php")
			?>
			<!--Footer-->	
    </div>
		<!--Third view--> 
		
	

	</div>
	
	
	<script>
		includeHTML();
	</script> 
  <script>
	  /*$(document).ready(function() {
	    // Sets viewScroller
      $('.mainbag').viewScroller({
	      useScrollbar: true,
        changeWhenAnim: false,
				viewsHeight: [0, 0, 0, 0, 232]
      });
    });*/
		$("#continueBtn").click(function() {
    $("html, body").animate({
        scrollTop: $("#secondView").offset().top - 65
			}, "slow");
});
	</script>	
</body>

</html>