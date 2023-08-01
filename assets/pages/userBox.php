<?php
  session_start();
  if(!isset($_SESSION['userdata'])){
    header('Location: ../../index.php?login');
  }
  else{
    $userData = $_SESSION['userdata'];
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User's Page</title>
  <!-- <link rel="stylesheet" href="../css/userBox.css"> -->
  <link rel="stylesheet" href="https://unpkg.com/css.gg@2.0.0/icons/css/search.css">
  <link rel="stylesheet" href="https://unpkg.com/css.gg@2.0.0/icons/css/circleci.css">
</head>
<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <img src="assets/profile_imgs/default.jpg">
          <div class="details">
            <span>
              <?php echo $userData['username']; ?>
            </span>
            <p>
              <?php
               if($userData['active']){
                echo "active";
               }
               else{
                echo "inactive";
               }
              ?>
            </p>
          </div>
        </div>
        <a href="assets/php/actions.php?logOut" class="logout">Logout</a>
      </header>
      <div class="search">
        <span class="text">Select an user to chat</span>
        <input type="text" placeholder="Enter a name to search">
        <button><i class="gg-search"></i></button>
      </div>
      <div class="users-list">
    
        
      </div>
    </section>
  </div>
  <script src="assets/js/userBox.js"></script>
</body>
</html>