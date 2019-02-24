<nav class="navbar navbar-expand-lg navbar-light navbar-fixed-top" id="header">
	<!--Logo-->
	<a class="navbar-brand" href="index.php">
		<!--TODO CAMBIARE (fare in modo che il logo si raggiunga globalmente-->
		<img src="media/Ph.png" width="40" height="40" alt="Logo">
		Volontari per caso
  </a>
  <!--Logo-->
  
  <!--Button for collapsed version-->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <!--Button for collapsed version-->

  <!--Collapsible-->
  <div class="collapse navbar-collapse" id="navbarNav">
    <!--Links-->  
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="browse_proposals.php">Vedi proposte</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Aggiungi proposte</a>
      </li>
    </ul>
    <!---Links-->
    
    <!--Login/profile-->
    <ul class="navbar-nav ml-auto">
      <?php
        if(my_session_is_valid()){
<<<<<<< HEAD
          echo "<li class=\"nav-item\"><a href=\"#\" class=\"nav-link\"><img style=\"margin-right: 10px;\"id=\"bar-id-pic\" src=\"media/Ph.png\" alt=\"Placeholder (profile pic)\"></a></li>";
=======
          echo "<li class=\"nav-item\"><a href=\"profile.php\"><img id=\"bar-id-pic\" src=\"media/Ph.png\" alt=\"Placeholder (profile pic)\"></a></li>";
>>>>>>> 393a3289c5310b35ff626e7ca00b50336dbde39f
        } else {
          echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"login.php\">Log in</a></li>";
          echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"registration_form.php\">Sign up</a></li>";
        }
      ?>
    </ul>
    <!--Login/profile-->
    
    <div id="search-contain">
      <form class="form-inline my-2 my-lg-0" action = "browse_proposals.php" method="GET">
        <input class="form-control" type="search" name="search" placeholder="Cerca una proposta" aria-label="Search">
        <button id="src-btn" class="btn btn-outline-dark my-2 my-sm-0" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>

    
  </div>
	<!--Collapsible-->
	
  <!--See https://getbootstrap.com/docs/4.1/components/navbar/#color-schemes for other options.-->
</nav>	