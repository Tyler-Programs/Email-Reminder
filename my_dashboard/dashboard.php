<?php 
include 'my_dashboard/php/project_card.php';
?>

<html>
<header>
<button id="logout_button" name="logout_button" style="position: absolute; right: 20px;" onclick="logout()">Logout</button>
<br>
<script src="../index.js"></script>
</header>
<body>
    <h1>Welcome to your dashboard. Here you can choose which project you would like to view.</h1>
    <div id="project_cards_container">
        
            <div id="temp_card" class="project_card" onclick="window.location.href = '/reminder'">
                <h3>Project Name</h3>
                <hr>
                <p>Sends an email at a specified UTC time to remind you of important events.</p>
                <p>Created on: March 24, 2020</p>
                <p>Using: LAMP stack</p>
            </div>
            <?php
            /*foreach(PROJECTS as $value){
                echo display_card_test($value) . "\n"; // TODO: change to display_card
            }*/
            ?>
    </div>
</body>
</html>

<style>
.project_card
{
    display: block;
    background-color: #dff1f2;
}
.project_card p {
    margin: 0px; /* squish the description text together */
}
</style>