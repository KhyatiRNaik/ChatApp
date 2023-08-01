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
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
</head>

<body>
  <section>

<!--header starts here -->
    <header>

      <?php

        require_once("assets/php/functions.php");

        global $db;

        $c_user_no = $_GET['ChatArea'];
        $sql = "SELECT * FROM public.users WHERE user_no = ".$c_user_no."";
       
        $query = pg_query($db, $sql);
        $c_user = pg_fetch_assoc($query);

        if($c_user['active'] == 't'){
          $status = "active";
        }
        else{
          $status = "inactive";
        }
      ?>

      <a href="?userBox"><div class="back-icon"></div></a>
      <img src="assets/profile_imgs/default.jpg">
      <div>
        <span><?=$c_user["username"];?></span>
        <p><?=$status?></p>
      </div>
    </header>
<!--  header ends here     -->

<!--  chat section starts   -->
    <div class="chat-area">
      
    </div>
    <!--   message box   -->
      <form action=# class="typing-area" autocomplete="off">
        <input type="text" name="outgoing_id" value="<?= $userData['user_no']?>" hidden>
        <input type="text" name="incoming_id"  value="<?= $c_user_no?>" hidden>
        <input type="text" name="message" placeholder="Type your message here ....." class="input-field">
        <button>🚀</button>
      </form>
  </section>
  <script src="assets/js/chatBox.js"></script>
</body>
</html>