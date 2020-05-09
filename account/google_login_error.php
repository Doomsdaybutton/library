<?php
    require('../config/config.php');
    require('../config/db.php');        

    

?>

    <?php include('../inc/header.php'); ?>
    <?php require('../inc/navbar.php'); ?>
    
    <div class="container d-flex justify-content-center">
        <div class="card" style="width:auto;">
            <div class="card-body text-center">
                <p class="card-header bg-white border-0 font-weight-bold">Bibliothek Proenca</p>
                <p class="card-subtitle mb-2"><small><?php echo $_SESSION["google_login_error_type"] == 'login' ? 'Anmelden' : 'Registrieren'; ?></small></p>
                <hr>
                <p class="card-text text-danger"><?php echo $_SESSION["google_login_error_msg"]; ?><br><span class="text-secondary"><small>
                    <?php if($_SESSION["google_login_error_type"] == 'sign_up'): ?>
                        Bitte <a class="text-info" href="sign_up.php">versuchen Sie es mit einer anderen Email</a> oder <a class="text-info" href="login.php">melden Sie sich an.</a>
                    <?php elseif($_SESSION["google_login_error_type"] == 'login'): ?>
                        Bitte <a href="login.php" class="text-info">versuchen Sie es erneut</a> oder <a href="sign_up.php" class="text-info">erstellen Sie ein neues Konto.</a>
                    <?php else: ?>
                        <a href="<?php echo ROOT_URL; ?>" class="text-info">Zurück zur Homepage</a>
                    <?php endif; ?>
                </small></span></p>
                
            </div>
        </div>
    </div>
    
    

    <?php include('../inc/footer.php'); ?>