<?php
    require_once"../config/db_connect.php";
if(session_status() == PHP_SESSION_NONE){
    session_name('auth');
    session_start();
}

function isLoggedIn(){
    return isset($_SESSION['user_id']);
}

function getUsername(){
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

ob_start();

    if(isLoggedIn()){
        header('Location: ./dashboard.php');
        exit();
    }

    $errormessage= "";
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if(empty($username) || empty($password)){
            $errormessage = "Please enter both username and password";
        } else{
            $sql = "SELECT id, username, password from op_users where username =?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $user_data = mysqli_fetch_assoc($result);
                if(password_verify($password, $user_data['password'])){
                    $_SESSION['user_id'] = $user_data['id'];
                    $_SESSION['username'] = $user_data['username'];

                    header('Location: ./dashboard.php');
                    exit();
                } else{
                    $errormessage = "Invalid Password.";
                }
            } else{
                $errormessage = "Username doesn't exist";
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HopOn - Operator Dashboard</title>
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="icon" type="image/jpg" href="./assets/images/Logo.jpg">
</head>
<body>



<section class="form-section">
    <div class="container">
        <div class="form-container">
            <?php if(!empty($errormessage)):?>
                <div class="error-message">
                    <?=$errormessage ?>
                </div>
            <?php endif; ?>
            <h2 class="form-title">Login to your account</h2>
            
            <form action="admin_login.php" method="POST" id="login-form">
                <div class="form-group">
                    <label for="">Username</label>
                    <input type="text" name="username" id="" required />
                </div>

                <div class="form-group">
                    <label for="">Password</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" />
                        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    
                </div>

                <button type="submit" class="form-btn">Login</button>
            </form>
        </div>
    </div>
</section>

<script src="../assets/js/password.js" ></script>