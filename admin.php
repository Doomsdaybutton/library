<?php 
    require('config/config.php');
    require('config/db.php');

    $_SESSION["current_page"] = 'admin';
?>
    <?php include('inc/header.php'); ?>
    <?php include('inc/navbar.php'); ?>

    <div class="container">
        <h1 class="text-center mb-3">Welcome to the Admin - Page!</h1>
        <h6>As an admin you have to:</h6>
        <ul>
            <li>Add new Media / Books to the database</li>
            <li>Confirm that a certain Medium / Book was returned</li>
            <li>Make sure, that lent Media / Books are marked as such</li>
        </ul>
    </div>
    <?php include('inc/footer.php'); ?>