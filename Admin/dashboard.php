<?php
  require_once"./includes/db_header.php";
  if(!isLoggedIn()){
        header('Location: ./admin_login.php');
        exit();
  }



  /* OP USER:

    Username: RamBdr,
    Password: 123456
  */
?>


</body>
</html>
