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
	<div id="mainContainer">
		
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
    	<div id="mapContainer" class="contain">
			<h4 id="preMap">Scopri sulla <span style="inline: block; color: cornflowerblue">mappa</span> le proposte di volontariato!<h4>
			<h6 id="preMap"><i>Se non ce ne sono, guarda che tempo fa con un click.</i></h6>
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
						iconUrl: 'media/heart.png',
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