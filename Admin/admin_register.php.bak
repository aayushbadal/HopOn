<?php
    require_once"./includes/db_header.php";

    if(isLoggedIn()){
        header('Location: ./dashboard');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Get form data
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $full_name = trim($_POST['full_name']);

        //validate input
        if(empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($full_name)){
            $errormessage = "Please fill in all required fields";
        }
        else if(strlen($username)<3){
            $errormessage= "Username must be at least  3 characetr long";
        }
        else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $errormessage= "Please enter the valid email address";
        }
        else if(strlen($password)<6){
            $errormessage= "Password must be at least 6 character long";
        }
        else if($password != $confirm_password){
            $errormessage= "Passwords donot match";
        }   
        else{
            //perform registration work
            $check_sql = "SELECT ID FROM op_users Where username = ? OR email = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            
            if($check_stmt) {
                mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
                mysqli_stmt_execute($check_stmt);
                mysqli_stmt_store_result($check_stmt);

                if(mysqli_stmt_num_rows($check_stmt) > 0){
                    $errormessage = "Username or email already exists";
                } else{
                    //Now insert users into database
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $insert_sql = "INSERT INTO op_users(username, email, full_name, password) VALUES(?, ?, ?, ?);";
                    $insert_stmt = mysqli_prepare($conn, $insert_sql);

                    if($insert_stmt) {
                        mysqli_stmt_bind_param($insert_stmt, "ssss", $username, $email, $full_name, $password);
                        if (mysqli_stmt_execute($insert_stmt)){
                            // Redirect to login page
                            header('Location:./admin_login.php');
                            exit();
                        } else{
                            $errormessage = "Registration fail";
                        }
                    }
                }
            }
        }
    }
?>

<section class="form-section">
    <div class="container">
        <div class="form-container">
            <?php if(!empty($errormessage)):?>
                <div class="error-message">
                    <?=$errormessage ?>
                </div>
            <?php endif; ?>
            <h2 class="form-title">Create an Account</h2>
                <form action="admin_register.php" method="POST" id="register-form">
                <div class="form-group">
                    <label for="">Full Name</label>
                    <input type="text" name="full_name" required/>
                </div>

                <div class="form-group">
                    <label for="">Email Address</label>
                    <input type="email" name="email" id=""/>
                </div>

                <div class="form-group">
                    <label for="">Username</label>
                    <input type="text" name="username" id=""/>
                </div>

                <div class="form-group">
                    <label for="">Password</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" />
                        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Confirm Password</label>
                    <input type="password" name="confirm_password" id=""/>
                </div>

                <button type="submit" class="form-btn">Register</button>

                <div class="form-footer">
                    <p>
                        Already have account ? <a href="./admin_login.php">Login</a>
                    </p>
                </div>

                
                </form>
            
        </div>
    </div>
</section>
<script src="./assets/js/password.js" ></script>
