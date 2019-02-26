<?php
	require_once("php/handlesession.php");
	my_session_start();
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <title>Hand-Aid</title>
    
	<?php
        require("php/head_common.php");
  ?>

	<script src="js/jquery.easing.min.js"></script>
	
  <link rel="stylesheet" href="css/index.css">
</head>

<body>
	
	<!--First view-->
	<div id="firstView">
			<!--Header/Navbar-->
			<?php include("php/navbar.php"); ?>
			<!--Header/Navbar-->
			<!--Carousel-->
			<div id="carouselTop" class="carousel slide" data-ride="carousel">
	  		<div class="carousel-inner">
	  			<!--First slide-->
  			  <div id="firstSlide" class="carousel-item active">
    		  	<!--<img class="d-block img-fluid" src="" alt="">-->
    		  	<span class="sr-only">First slide</span>
    		    <div class="carousel-caption">
    					<h5>Cerca tra decine di proposte quella adatta a te!</h5>
    					<p>Puoi essere volontario per un giorno, volontario per la vita!</p>
  					</div>
    			</div>
    			<!--First slide-->
    			<!--Second slide-->
    			<div id="secondSlide" class="carousel-item">
    		  	<img class="d-block img-fluid" src="" alt="">
    		  	<span class="sr-only">Second slide</span>
    		  	<div class="carousel-caption">
							<h5>Ciò che abbiamo fatto solo per noi stessi muore con noi. Ciò che abbiamo fatto per gli altri e per il mondo resta ed è immortale.</h5>
    					<p>Harvey B. Mackay</p>
  					</div>
    			</div>
    			<!--Second slide-->
    			<!--Third slide-->
    			<div id="thirdSlide" class="carousel-item">
   			  	<!--<img class="d-block img-fluid" src="" alt="">-->
   			  	<span class="sr-only">Third slide</span>
   			  	<div class="carousel-caption">
    					<h5>Prendere riempie le mani, dare riempie il cuore.</h5>
    					<p>M. Seeman</p>
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
		<!--div id="continue">
				<form action="#services" method="GET"><button id="continueBtn" type="submit" value="Scopri di più">Scopri di più</button></form>
		</div>
			<!Continue button-->
	</div>
</div>
		<!--First view-->
	
		<!--Second view-->
		<section id="services">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h2 class="section-heading">Hand-Aid</h2>
          <h3 class="section-subheading text-muted">La piattaforma per il volontariato libero</h3>
        </div>
      </div>
      <div class="row text-center">
        <div class="col-md-4">
          <span class="fa-stack fa-4x">
            <i class="fas fa-circle fa-stack-2x text-primary"></i>
            <i class="fas fa-check-circle fa-stack-1x fa-inverse"></i>
          </span>
          <h4 class="service-heading">Semplice</h4>
          <p class="text-muted">Una semplice iscrizione, e sei pronto ad aiutare.</p>
        </div>
        <div class="col-md-4">
          <span class="fa-stack fa-4x">
            <i class="fas fa-circle fa-stack-2x text-primary"></i>
            <i class="fas fa-users fa-stack-1x fa-inverse"></i>
          </span>
          <h4 class="service-heading">Per tutti</h4>
          <p class="text-muted">Chiunque tu sia, c'è un ruolo per te nella nostra comunità.</p>
        </div>
        <div class="col-md-4">
          <span class="fa-stack fa-4x">
            <i class="fas fa-circle fa-stack-2x text-primary"></i>
            <i class="fas fa-stopwatch fa-stack-1x fa-inverse"></i>
          </span>
          <h4 class="service-heading">Immediata</h4>
          <p class="text-muted">La nostra interfaccia intuitiva è adatta a chiunque.</p>
        </div>
      </div>
    </div>
  </section>	
		<!--Second view--> 
		
		<!--Third view-->
    	<!--Testimonials-->
			<section class="text-center">

			<!-- Section heading -->
			<h2 class="h1-responsive font-weight-bold my-5">Dicono di noi</h2>
			<!-- Section description -->
			<p id="desc" class="dark-grey-text w-responsive mx-auto mb-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit.
				Fugit, error amet numquam iure provident voluptate esse quasi, veritatis totam voluptas nostrum quisquam
				eum porro a pariatur veniam.</p>

			<!--Grid row-->
			<div class="row text-center">

				<!--Grid column-->
				<div class="col-md-4 mb-md-0 mb-5">

					<div class="testimonial">
						<!--Avatar-->
						<div class="avatar mx-auto">
							<img src="https://mdbootstrap.com/img/Photos/Avatars/img%20(1).jpg" class="rounded-circle z-depth-1 img-fluid">
						</div>
						<!--Content-->
						<h4 class="font-weight-bold dark-grey-text mt-2">Anna Deynah</h4>
						<p class="font-weight-normal avatar-text dark-grey-text">
							<i class="fas fa-quote-left pr-2"></i>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quod
							eos id officiis hic tenetur quae quaerat ad velit ab hic tenetur. <i class="fas fa-quote-right pr-2"></i></p>
					</div>

				</div>
				<!--Grid column-->

				<!--Grid column-->
				<div class="col-md-4 mb-md-0 mb-5">

					<div class="testimonial">
						<!--Avatar-->
						<div class="avatar mx-auto">
							<img src="https://mdbootstrap.com/img/Photos/Avatars/img%20(8).jpg" class="rounded-circle z-depth-1 img-fluid">
						</div>
						<!--Content-->
						<h4 class="font-weight-bold dark-grey-text mt-2">John Doe</h4>
						<p class="font-weight-normal avatar-text dark-grey-text">
							<i class="fas fa-quote-left pr-2"></i>Ut enim ad minima veniam, quis nostrum exercitationem ullam
							corporis suscipit laboriosam, nisi ut aliquid commodi. <i class="fas fa-quote-right pr-2"></i></p>
					</div>

				</div>
				<!--Grid column-->

				<!--Grid column-->
				<div class="col-md-4">

					<div class="testimonial">
						<!--Avatar-->
						<div class="avatar mx-auto">
							<img src="https://mdbootstrap.com/img/Photos/Avatars/img%20(10).jpg" class="rounded-circle z-depth-1 img-fluid">
						</div>
						<!--Content-->
						<h4 class="font-weight-bold dark-grey-text mt-2">Maria Kate</h4>
						<p class="font-weight-normal avatar-text dark-grey-text">
							<i class="fas fa-quote-left pr-2"></i>At vero eos et accusamus et iusto odio dignissimos ducimus qui
							blanditiis praesentium voluptatum deleniti atque corrupti. <i class="fas fa-quote-right pr-2"></i></p>
					</div>

				</div>
				<!--Grid column-->

			</div>
			<!--Grid row-->
			</section>
			<!--Testimonials-->	
		<!--Third view--> 
		
		<!--Fourth view-->
    	<!--Map-->
    	
			<!--Map-->
    <!--Fourth view-->
	
		<!--Third view-->
			<!--Footer-->
			<?php
				include("php/footer.php")
			?>
			<!--Footer-->	
		<!--Third view--> 	
	
	<script>
		document.querySelectorAll('form[action^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        document.querySelector(this.getAttribute('action')).scrollIntoView({
            behavior: 'smooth'
        });
    	});
		});
	</script>	
</body>

</html>