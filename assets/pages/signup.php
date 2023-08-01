<body>
    <main>      

        <form method="POST" action="assets/php/actions.php?signup">

            <h1>Sign-Up</h1>

            <div class="input">
                <input type="text" name="name" required>
                <span>Name</span>
            </div>

            <div class="input">
                <input type="text" name="username" required>
                <span>UserName</span>
            </div>

            <div class="input">
                <input type="email" name="email" required>
                <span>Mail</span>
            </div>
           
            <div class="input">
                <input type="password" name="password" required>
                <span>Password</span>
            </div>

            <div class="input">
                <input type="password"  name="cpassword" required>
                <span>Confirm</span>
            </div>

            <input type="submit" value="SignUp">

            <span><a href="index.php?login">Already have an account?</a></span>

        </form>

    </main>
