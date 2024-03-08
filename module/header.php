
        <!--Mise en place de l'entête de la page avec un menu horizontal -->
        <!-- On créer une class pour le header afin que les style boostrap ne s'appliquent pas sur le header quand il sera appelé dans d'autres classes -->
        <header class="custom-header">
        <nav>
            <div class="wrapper">
                <div class="logo">
                    <a href="#">MedicoGest</a>
                </div>
                <input type="radio" name="slider" id="menu-btn">
                <input type="radio" name="slider" id="close-btn">
                <ul class="nav-links">
                <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
                <li>
                    <a href="#" class="desktop-item">Usagers</a>
                    <input type="checkbox" id="showDrop">
                    <label for="showDrop" class="mobile-item">Usagers</label>
                    <ul class="drop-menu">
                    <li><a href="affichage_patient.php">Voir la liste</a></li>
                    <li><a href="ajout_patient.php">Ajouter usager</a></li>
                    </ul>
                </li>
                <li>
                <li>
                    <a href="#" class="desktop-item">Médecins</a>
                    <input type="checkbox" id="showDrop">
                    <label for="showDrop" class="mobile-item">Médecins</label>
                    <ul class="drop-menu">
                    <li><a href="affichage_medecin.php">Voir la liste</a></li>
                    <li><a href="ajout_medecin.php">Ajouter médecin</a></li>
                    </ul>
                </li>
                <li>
                <li>
                    <a href="#" class="desktop-item">Consultations</a>
                    <input type="checkbox" id="showDrop">
                    <label for="showDrop" class="mobile-item">Consultations</label>
                    <ul class="drop-menu">
                    <li><a href="affichage_consultation.php">Voir la liste</a></li>
                    <li><a href="ajout_consultation.php">Ajouter consultation</a></li>
                    </ul>
                </li>
                <li><a href="statistiques.php">Statistiques</a></li>
                </ul>
                <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
            </div>
            <div class="utilisateur">
                    <img width=5% src="image/icone_utilisateur.png" />
                    <p> <?php echo $_SESSION['login']; ?> </p> <!-- Permet d'afficher le nom de l'utilisateur dans la session actuelle -->
                    <a class="deconnexion" href="module/deconnexion.php">Déconnexion</a> <!-- Bouton de deconnexion qui renvoit à la page de connexion -->
                </div>
        </nav>
     </header>