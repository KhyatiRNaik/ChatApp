<?php
    require_once("config.php");
    //require("../js/functions.js");
    
    //Connecting db
    $db = pg_connect("user = ".DB_USER." host = ".DB_HOST." port = ".DB_PORT." dbname = ".DB_NAME." password = ".DB_PASSWORD) or die("Error connecting to db");


    // TO show a page
    function showPage($page, $style="", $data=""){
        include("assets/pages/$page.php");
    }


    //for "SIGNUP" To register an user
    function registerUser($form_data){

        $pswd = $form_data['password'];

        if($form_data['username'] == "" || $form_data['name'] == "" || $form_data['email'] == "" || $pswd == "" || $form_data['cpassword'] == ""){
            echo "<script>alert('Fill form properly'); window.location.href='../../?signup';</script>";
        }
        else if(passwordNeeds($pswd) && $pswd === $form_data['cpassword']){

            global $db; //so it can access a global var($db) into a function

            $pswd = password_hash($pswd, PASSWORD_BCRYPT);

            $query = "INSERT INTO public.users VALUES(DEFAULT, '".$form_data['name']."', '".$form_data['username']."', '".$pswd."', '".$form_data['email']."', DEFAULT);";

            $query_result = pg_query($db, $query);

            echo "<script>alert('Sign in successful. Login to continue'); window.location.href='../../?login';</script>";
        }
        else{
            echo "<script>alert('Passwords do not match'); window.location.href='../../?signup';</script>";          
        }

    }

    //Check password
    function passwordNeeds($pswd){
        $errors = array();
        if (strlen($pswd) < 8 || strlen($pswd) > 13) {
            $errors[] = "\nPassword should be min 8 characters and max 13 characters";
        }
        if (!preg_match("/\d/", $pswd)) {
            $errors[] = "\nPassword should contain at least one digit";
        }
        if (!preg_match("/[A-Z]/", $pswd)) {
            $errors[] = "\nPassword should contain at least one Capital Letter";
        }
        if (!preg_match("/[a-z]/", $pswd)) {
            $errors[] = "\nPassword should contain at least one small Letter";
        }
        if (!preg_match("/\W/", $pswd)) {
            $errors[] = "\nPassword should contain at least one special character";
        }
        if (preg_match("/\s/", $pswd)) {
            $errors[] = "\nPassword should not contain any white space";
        }
        
        if ($errors) {
           echo "<script>alert(".json_encode($errors)."); window.location.href='../../?signup';</script>";
           return false;
        } 
        else {
            echo "<script>alert('Criteria matched')</script>";
            return true;
        }
        
    }

    // For "LOGIN" - to check if user is already present or not
    function checkUser($form_data){

        global $db;

        if($form_data['username_email'] == "" || $form_data['password'] == ""){
            echo "<script>alert('Fill form properly'); window.location.href='../../?signup';</script>";
        }
        else{
            $query = "SELECT * FROM public.users where (username = '".$form_data['username_email']."' or email = '".$form_data['username_email']."')";
            $query_result = pg_query($db, $query);
            $query_data = pg_fetch_assoc($query_result);

            $enterdP = $form_data['password'];
            $storedP = $query_data['password'];

            
            //correct password
            if(password_verify($enterdP, $storedP)){     //password_verify($pswd, $hash) returns boolean
            
                session_start();

                $_SESSION['userdata'] = $query_data;
                // print_r($query_data);
                
                $statusQuery = "UPDATE public.users SET active = true WHERE (username = '".$form_data['username_email']."' or email = '".$form_data['username_email']."')";
                pg_query($db, $statusQuery);

                $currUserQuery = "SELECT user_no from public.users WHERE (username = '".$form_data['username_email']."' or email = '".$form_data['username_email']."')";
                $currUserRes = pg_query($currUserQuery);
                $currUserNo = pg_fetch_array($currUserRes, 0, PGSQL_NUM);
                header('Location: ../../?userBox');
                // print_r($currUserNo);
                // echo("<button><a href='../php/actions.php?logOut'>LogOut</a></button>");
                // echo("<button><a href='../pages/userBox.php'>user</a></button>");    
                // echo "<script>alert('Login successful'); window.location.href='../php/actions.php?chats';</script>";
            }
            //incorrect password
            else{
                echo "<script>alert('Passwords do not match'); window.location.href='../../?login';</script>";
            }  
        }

    }

    function logOut(){
        session_start();

        global $db;
        $statusQuery = "UPDATE public.users SET active = false WHERE user_no = ".$_SESSION['userdata']['user_no']."";
        pg_query($db, $statusQuery);

        session_destroy();

        echo "<script>alert('Log out successful')</script>";
        header('Location: ../../?login');
    }

    function ajax_users(){
        session_start();

        global $db;

        $c_user_no = $_SESSION['userdata'];
        $outgoing_id = $c_user_no['user_no'];

        $sql = "SELECT * FROM public.users WHERE NOT user_no = {$outgoing_id}";

        // $sql = "SELECT u.user_no, u.username, u.email, u.active 
        // FROM public.users u
        // JOIN public.user_msgs m 
        // ON u.user_no = m.incoming_msg_id OR u.user_no = m.outgoing_msg_id
        // WHERE m.msg IS NOT NULL and user_no != {$outgoing_id}
        // GROUP BY u.user_no, u.username, u.email, u.active
        // ORDER BY MAX(m.sent_at) DESC;
        // ";
    

        $query = pg_query($db, $sql);

        $output = "";

        if(pg_num_rows($query) == 0){
            $output .= "No users are available to chat";
        }
        elseif(pg_num_rows($query) > 0){
            $output .= getData($query, $output); 
        }
        echo $output;
    }
 
    function ajax_searchUser(){
        session_start();

        global $db;

        $v = $_SESSION['userdata'];
        $outgoing_id = $v['user_no'];
        $searchTerm = pg_escape_string($db, $_POST['searchTerm']);

        $sql = "SELECT * FROM public.users WHERE NOT user_no = {$outgoing_id} AND (username LIKE '%{$searchTerm}%'OR name LIKE '%{$searchTerm}%')";
        $output = "";
        $query = pg_query($db, $sql);
        if(pg_num_rows($query) > 0){
           $output .= getData($query, $output); 
        }
        else{
            $output .= 'No user found related to your search term';
        }
        echo $output;
    }

    function getData($query, $output){

        global $db;

        $v = $_SESSION['userdata'];
        $outgoing_id = $v['user_no'];


        while($row = pg_fetch_assoc($query)){

            $sql2 = "SELECT * FROM public.user_msgs WHERE (incoming_msg_id = {$row['user_no']}
            OR outgoing_msg_id = {$row['user_no']}) AND (outgoing_msg_id = {$outgoing_id} 
            OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";

            $query2 = pg_query($db, $sql2);
            $row2 =  pg_fetch_assoc($query2);

            (pg_num_rows($query2) > 0) ? $result = $row2['msg'] : $result ="No message available";
            (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;

            if(isset($row2['outgoing_msg_id'])){
                ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
            } 
            else{
                $you = "";
            }

            ($row['active'] == "f") ? $offline = "offline" : $offline = "";
            ($outgoing_id == $row['user_no']) ? $hid_me = "hide" : $hid_me = "";


            $output .= ' <a href="?ChatArea='.$row["user_no"].'">
                        <div class="content">
                        <img src="assets/profile_imgs/default.jpg" alt="no img">
                        <div class="details">
                            <span>'.$row["username"].'</span>
                            <p>'.$you.$msg.'</p> 
                        </div>
                        </div>
                        <div class="status-dot '.$offline.'">
                        <i class="gg-circleci"></i>
                        </div>
                        </a>' ;
    
        }
        return $output;
    }

    function ajax_insert_msg($incoming_id, $message){
        session_start();
        echo "<script>alert('inset wala')</script>";
        global $db;

        if(isset($_SESSION['userdata'])){   
            $outgoing_id = $_SESSION['userdata']['user_no'];

            if(!empty($message)){
                $sql = pg_query($db, "INSERT INTO public.user_msgs (incoming_msg_id, outgoing_msg_id, msg)
                                            VALUES ({$incoming_id}, {$outgoing_id}, '{$message}')") or die();
            }

        }
        else{
            header("location: ../login.php");
        }
    
    }

    function ajax_get_chat(){
        session_start();

        global $db;

        if(isset($_SESSION['userdata'])){

            $outgoing_id = $_SESSION['userdata']['user_no'];
            $incoming_id = pg_escape_string($db, $_POST['incoming_id']);
            $output = "";
            $sql = "SELECT * FROM public.user_msgs LEFT JOIN users ON public.users.user_no = public.user_msgs.outgoing_msg_id
                    WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                    OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";

            $query = pg_query($db, $sql);            

            if((pg_num_rows($query)) > 0){
                while($row = pg_fetch_assoc($query)){
                  
                    $msg_id = $row['msg_id'];
                    $sql2 = "Select sent_at from public.user_msgs where msg_id = ".$msg_id."";
                    $time =  pg_query($db, $sql2);
                    $time = pg_fetch_assoc($time);
        
                    $time = implode(" ", $time);
                    $time = explode(" ", $time);
                    
                    $time = $time[3];
                    $time = substr($time, 0, 5);

                    if($row['outgoing_msg_id'] === $outgoing_id){
                        $output .= '<div class="chat outgoing">
                                    <div class="chats">
                                    <span class="outgoing-msg">
                                        '.$row["msg"].'
                                    </span>
                                    <div style="color: white;">'.$time.'</div>
                                    </div>
                                    </div>';
                    }
                    else{
                        $time = "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;".$time;
                        $output .= '<div class="chat incoming">
                                    <div class="chats">
                                    <img class="img" src="assets/profile_imgs/default.jpg" alt="profile">
                                    <span class="incoming-msg"> 
                                    '.$row["msg"].'
                                    </span>
                                    <div style="color: white;">'.$time.'</div>
                                    </div>
                                    </div>';
                    }
                }
            }
            else{
                $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
            }
            echo $output;
        }
        else{
            header("location: ../login.php");
        }
    }


?>

