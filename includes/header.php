<?php
	// Initialiser la session
	session_start();

    //Vérifier le début de la session de l'utilisateur
    if (isset($_SESSION['session_start']) && (time() - $_SESSION['session_start'] > 3600)) {//Si la session est supérieure à 1 heure 
        session_unset(); //on enlève la session
        session_destroy(); //on détruit la session
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
        
    </head>

    <body>
            <!--    Barre du haut : logo, prise photo, login, Mot de passe, oublie Mdp  -->

            <header class="bg-dark text-white">
          <div class="container">
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 ">
      <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
        <span class="fs-4">Trames-Ethernet</span>
      </a>

      <ul class="nav nav-pills">
        <li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
        <li class="nav-item"><a href="tramesr.php" class="nav-link">Trames</a></li>
        <li class="nav-item"><a href="recherche.php" class="nav-link">Recherche</a></li>
      </ul>
    </header>
  </div>
        </div>
      </div>
    </div>
  </header>
