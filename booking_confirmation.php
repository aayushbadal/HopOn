<?php

require_once "./includes/header.php";

?>
<div class="confirmation-container">
  <div class="confirmation-card">
    <div class="confirmation-icon"><i class="fa-solid fa-circle-check confirmation-icon"></i></div>
    <h1 class="confirmation-title">Booking Confirmed!</h1>
    <p class="confirmation-text">
      Your booking reference ID is <span><?= htmlspecialchars($_GET['reference']); ?></span>
    </p>
    <a href="./index.php" class="confirmation-btn">Continue</a>
  </div>
</div>
