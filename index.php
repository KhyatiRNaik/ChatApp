<?php
    require_once('assets/php/functions.php');

    if(isset($_GET['signup'])){
        showPage("header", ['addon' => "signup"], ['page_title' => 'ChitChat - SignUp']);
        showPage("signup");
    }
    else if(isset($_GET['login'])){
        showPage("header", ['addon' => "login"], ['page_title' => 'ChitChat - Log in']);
        showPage("login");
    }
    else if(isset($_GET['chats'])){
        showPage("header", ['addon' => "chats"], ['page_title' => 'ChitChat - Messages']);
        showPage("chats");
    }
    else if(isset($_GET['userBox'])){
        showPage("header", ['addon' => "userBox"], ['page_title' => 'ChitChat - Users']);
        showPage("userBox");
    }
    else if(isset($_GET['ChatArea'])){
        showPage("header", ['addon' => "chatBox"], ['page_title' => 'ChitChat - ChatArea']);
        showPage("chatBox");
    }
    else{
        showPage("header", ['addon' => "style"], ['page_title' => 'ChitChat - Oops! Something went wrong']);
        echo "<script>window.location.href='?signup'</script>";
    }
    

    showPage("footer");

?>