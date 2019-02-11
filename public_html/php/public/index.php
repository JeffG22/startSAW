<!DOCTYPE html>
<html lang="en">

<head>
  <title>Placeholder</title>
    
  <!--Boostrap-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
	integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
	integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
	integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<!--Boostrap-->
	
	<!--ViewScroller-->
	<link rel="stylesheet" type="text/css" href="viewScroller.css">
	<!--ViewScroller-->

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!--Inclusions-->
  <!--link rel="stylesheet" href="../CSS/Global.css"-->
  <script src="../../js/include.js"></script> 
 	<script src="../../js/jquery-3.1.0.min.js"></script>
	<script src="../../js/jquery.easing.min.js"></script>
	<script src="../../js/jquery.mousewheel.min.js"></script>
	<script src="../../js/viewScroller.js"></script>
	
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/index.css">
</head>

<body>
	
	<!--Header/Navbar-->
	<div w3-include-html="../navbar.php"></div>
	<!--Header/Navbar-->
	
	<div class="mainbag">
	
		<!--First view-->
		<div id="firstView" vs-anchor="firstview" class="mainview">
			<!--Carousel-->
			<div id="carouselTop" class="carousel slide" data-ride="carousel">
	  		<div class="carousel-inner">
	  			<!--First slide-->
  			  <div id="firstSlide" class="carousel-item active">
    		  	<img class="d-block img-fluid" src="" alt="">
    		  	<span class="sr-only">First slide</span>
    		    <div class="carousel-caption d-none d-md-block">
    					<h5>Test caption</h5>
    					<p>Placeholder</p>
  					</div>
    			</div>
    			<!--First slide-->
    			<!--Second slide-->
    			<div id="secondSlide" class="carousel-item">
    		  	<img class="d-block img-fluid" src="" alt="">
    		  	<span class="sr-only">Second slide</span>
    		  	<div class="carousel-caption d-none d-md-block">
    					<h5>Test caption</h5>
    					<p>Placeholder</p>
  					</div>
    			</div>
    			<!--Second slide-->
    			<!--Third slide-->
    			<div id="thirdSlide" class="carousel-item">
   			  	<img class="d-block img-fluid" src="" alt="">
   			  	<span class="sr-only">Third slide</span>
   			  	<div class="carousel-caption d-none d-md-block">
    					<h5>Test caption</h5>
    					<p>Placeholder</p>
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
				<a href="#secondView" class="vs-anchor"><button id="continueBtn" type="button" value="Scopri di più">Scopri di più</button></a>
			</div>
			<!--Continue button-->
		</div>
		<!--First view-->
	
		<!--Second view-->
		<div vs-anchor="secondView" class="mainview">
    	<!--Description-->
    	<div id="desContainer"></div>
			<!--Description-->	
    </div>
    <!--Second view--> 
	
		<!--Footer-->
		<div w3-include-html="../footer.php"></div>
		<!--Footer-->

	</div>
	
	
	<script>
		includeHTML();
	</script> 
  <script>
	  $(document).ready(function() {
	    // Sets viewScroller
      $('.mainbag').viewScroller({
	      useScrollbar: false,
        changeWhenAnim: false
      });
    });
	</script>	
</body>
