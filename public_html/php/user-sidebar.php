<!--To include this bar:
    1. - Make a class="container" div
    2. - Make a class="row profile" div in it
        <div class="container">
            <div class="row profile">
    3. - Include normally
    4. - The rest of the content should be in: 
        <div class="col-md-8">
            <div class="profile-content">
                <main role="main">
                    <div class="album py-5 bg-light">
                        <div class="container">
                            <div class="row">
    5. - Use this as "units" for the proposals, inside the row div:
        <div class="col-md-4">
            <div class="card mb-4 box-shadow">
                <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&bg=55595c&fg=eceeef&text=Thumbnail" alt="Card image cap">
                <div class="card-body">
                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                    </div>
                    <small class="text-muted">9 mins</small>
                </div>
                </div>
            </div>
        </div>
-->

<div class="col-md-4">
    <div class="profile-sidebar">
        <!-- SIDEBAR USERPIC -->
        <div class="profile-userpic">
            <img src="media/profile-placeholder.png" class="img-responsive" alt="">
        </div>
        <!-- END SIDEBAR USERPIC -->
        <!-- SIDEBAR USER TITLE -->
        <div class="profile-usertitle">
            <div class="profile-usertitle-name">
                <?php
                    echo $name;
                ?>
            </div>
            <!--div class="profile-usertitle-job">
                Volontario o organizzazione
            </div-->
        </div>
        <!-- END SIDEBAR USER TITLE -->

        <!-- SIDEBAR MENU -->
        <div class="profile-usermenu">
            <ul class="nav">
                <li class="active">
                    <a href="#">
                    <i class="fas fa-home"></i>
                    Profilo </a>
                </li>
                <li>
                    <a href="#">
                    <i class="fas fa-user"></i>
                    Impostazioni</a>
                </li>
                <li>
                    <a href="#" target="_blank">
                    <i class="fas fa-list-alt"></i>
                    Le Mie Proposte </a>
                </li>
                <li>
                    <a href="#" target="_blank">
                    <i class="fas fa-heart"></i>
                    Proposte Accettate </a>
                </li>
            </ul>
        </div>
        <!-- END MENU -->
    </div>
</div>