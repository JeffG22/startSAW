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
  <script src="js/include.js"></script> 
 	<script src="js/jquery-3.1.0.min.js"></script>
	<script src="js/jquery.easing.min.js"></script>
	<script src="js/jquery.mousewheel.min.js"></script>
	<!--script src="js/viewScroller.js"></script-->
	
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/index.css">
</head>

<body>
	
	<!--Header/Navbar-->
	<?php
		include("php/navbar.php")
	?>
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
				<a href="#secondView"><button id="continueBtn" type="button" value="Scopri di più">Scopri di più</button></a>
			</div>
			<!--Continue button-->
		</div>
		<!--First view-->
	
		<!--Second view-->
		<div id="secondView" class="mainView">
    	<!--Description-->
    	<div id="desContainer" class="contain">I know, I know I've let you down<br>
I've been a fool to myself<br>
I thought that I could<br>
live for no one else<br>
<br>
But now through all the hurt and pain<br>
Its time for me to respect<br>
the ones you love<br>
mean more than anything.<br>
<br>
So with sadness in my heart<br>
I feel the best thing I could do<br>
is end it all<br>
and leave forever<br>
whats done is done, it feels so bad<br>
what once was happy now is sad<br>
I'll never love again<br>
my world is ending<br>
<br>
I wish that I could turn back time<br>
'cause now the guilt is all mine<br>
can't live without the trust from those you love.<br>
I know we can't forget the past<br>
you can't forget love and pride<br>
because of that it's killing me inside<br>
<br>
It all returns to nothing, it all comes<br>
tumbling down, tumbling down,<br>
tumbling down,<br>
it all returns to nothing, I just keep<br>
letting me down, letting me down,<br>
letting me down,<br>
in my heart of hearts,<br>
I know that I could never love again<br>
I've lost everything<br>
everything, everything<br>
that matters to me,<br>
matters in this world<br>
<br>
I wish that I could turn back time<br>
'cause now all the guilt is mine<br>
can't live without<br>
the trust from those you love<br>
I know we can't forget the past<br>
we can't forget love and pride<br>
because of that, its killing me inside<br>
<br>
It all returns to nothing, it all comes<br>
tumbling down, tumbling down,<br>
tumbling down<br>
it all returns to nothing, I just keep<br>
letting me down, letting me down,<br>
letting me down<br>
It all returns to nothing, it all comes<br>
tumbling down, tumbling down,<br>
tumbling down<br>
it all returns to nothing, I just keep<br>
letting me down, letting me down,<br>
letting me down</div>
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
