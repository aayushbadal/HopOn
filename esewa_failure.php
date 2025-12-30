<?php
require_once "./includes/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$session_id = session_id();

/* =========================
   REMOVE SEAT LOCKS (FAILURE)
========================= */
$stmt = $conn->prepare("
    DELETE FROM seat_locks
    WHERE session_id = ?
");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$stmt->close();
?>


<section class="failure_container">
    <h2 class="esewa_failure_h2">Payment Failed!!</h2>
    <a class = "esewa_failure_a" href="index.php">Try Again</a>
</section>

