<nav class="navbar navbar-expand-lg navbar-light navbar-fixed-top" id="header">
	<!--Logo-->
	<a class="navbar-brand" href="#">
		<!--TODO CAMBIARE (fare in modo che il logo si raggiunga globalmente-->
		<img src="media/Ph.png" width="40" height="40" alt="Logo">
		Placeholder
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
        <a class="nav-link" href="dummy.php">Placeholder1</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Placeholder2</a>
      </li>
    </ul>
    <!---Links-->

    <div id="search-contain" class="ml-auto">
      <form class="form-inline my-2 my-lg-0">
        <input class="form-control" type="search" placeholder="Search" aria-label="Search">
        <button id="src-btn" class="btn btn-outline-dark my-2 my-sm-0" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>

    <div>
      <?php
        require_once("php/handlesession.php");
        if(my_session_is_valid()){
          echo "<img id=\"bar-id-pic\" src=\"media/Ph.png\" alt=\"Placeholder (profile pic)\">";
        } else {
          echo "<a class=\"bar-link\" href=\"login.php\">Log in</a>";
          echo "<a class=\"bar-link\" href=\"registration_form.php\">Sign up</a>";
        }
      ?>


  </div>
	<!--Collapsible-->
	
  <!--See https://getbootstrap.com/docs/4.1/components/navbar/#color-schemes for other options.-->
</nav>	