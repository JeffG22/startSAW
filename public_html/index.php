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
	<!--Include Leaflet CSS -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"
			  integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
			  crossorigin=""/>
	<!--Include Leaflet JavaScript file after Leaflet’s CSS-->
	<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"
					integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=="
					crossorigin="">
	</script>
	<script src="js/jquery.easing.min.js"></script>
	<?php include("php/map.php"); ?>
	<script src="js/mapFunction.js"></script>
  <link rel="stylesheet" href="css/index.css">
</head>

<body>
	<div>
		<!--Header/Navbar-->
		<?php include("php/navbar.php"); ?>
		<!--Header/Navbar-->

		<!--First view-->
		<div id="firstView">
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
    		  	<!--<img class="d-block img-fluid" src="" alt="">-->
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
							<img src="media/person1.png" class="rounded-circle z-depth-1 img-fluid" alt="Testimonial 1">
						</div>
						<!--Content-->
						<h4 class="font-weight-bold dark-grey-text mt-2">Anna Parodi</h4>
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
							<img src="media/person2.png" class="rounded-circle z-depth-1 img-fluid" alt="Testimonial 2">
						</div>
						<!--Content-->
						<h4 class="font-weight-bold dark-grey-text mt-2">Mario Rossi</h4>
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
							<img src="media/person3.jpeg" class="rounded-circle z-depth-1 img-fluid" alt="Testimonial 3">
						</div>
						<!--Content-->
						<h4 class="font-weight-bold dark-grey-text mt-2">Rosa Ferrari</h4>
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
	</div>		
		<!--Fourth view-->
    	<!--Map-->
    	<div id="mapContainer" class="contain">
			<h4 class="preMap">Scopri sulla <span style="color: cornflowerblue">mappa</span> le proposte di volontariato!</h4>
			<h6 class="preMap"><i>Mentre guardi in giro, clicca per scoprire l'ora locale e la temperatura.</i></h6>
			<!-- a questo id viene associata la mappa -->
			<div id="mapid"></div>
			</div>
			<!--Map-->
			<script>
				var mymap = L.map("mapid").setView([41.9109, 12.4818], 6);

				L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
						attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
						maxZoom: 21,
						id: 'mapbox.streets',
						accessToken: 'pk.eyJ1IjoiamVmZmdpbGliZXJ0aSIsImEiOiJjam9rN2EzaTMwYnQ3M3NwajZ1Y2l1czh4In0.LfOcrE8c99dDvDAurXe9Mg'
				}).addTo(mymap);

				var heartIcon = L.icon({
						iconUrl: 'media/logo-border.png',
						iconSize: [30, 30],
						iconAnchor: [16, 25],
						popupAnchor: [0, -28]
				});

				/* adding the marker from feature collections by geojson+lfleat */
				L.geoJSON(layer1, {
						pointToLayer: function (feature, latlng) {return L.marker(latlng, {icon: heartIcon});},
						onEachFeature: onEachFeature}).addTo(mymap);
				
				var popup = L.popup();
				mymap.on('click', onMapClick);
				mymap.on('popupopen', onMapOpen);
    </script>
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