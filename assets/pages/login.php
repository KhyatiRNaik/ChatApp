<body>
    <main>
        
        <form method="POST" action="assets/php/actions.php?login">
            <h1>login</h1>
            <div class="input">
                <input type="text" name="username_email" required>
                <span>Username / Email</span>
            </div>
            <div class="input">
                <input type="password" name="password" required>
                <span>Password</span>
            </div>
            <input type="submit" value="login">
            <div class="p">
                <a href="index.php?signup">Don't have an account?</a></a>
            </div>
        </form>
    
    </main>
