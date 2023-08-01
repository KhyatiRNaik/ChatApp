<?php

    require_once 'functions.php';
    
    if(isset($_GET['signup'])){
        registerUser($_POST);
    }
    else if(isset($_GET['login'])){
        checkUser($_POST);
    }
    else if(isset($_GET['logOut'])){
       logOut();
    }
    else if(isset($_GET['users'])){
        ajax_users();
    }
    else if(isset($_GET['searchUser'])){
        ajax_searchUser();
    }
    else if(isset($_GET['insert_msg'])){
        echo "<script>alert('actions wala')</script>";
        ajax_insert_msg($_POST['incoming_id'], $_POST['message']);
    }
    else if(isset($_GET['get_chat'])){
        ajax_get_chat($_POST['incoming_id'], $_POST['message']);
    }
    else{
        echo "<script>alert('ERROR')</script>";
      //  echo "<script>alert('Some error occured. Redirecting to home page'); window.location.href='../../?login';</script>";
    }

?>
