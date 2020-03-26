<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include 'head.php';
    ?>
    <title>Login</title>
</head>
<body>

<div class="wrapper">
    <div class="login">
        <div class="pokemon">
            <img src="/img/pokeball.png" alt="Pokemon">
        </div>
        <h1>Login to your Account</h1>
        <?php
        if (isset($_SESSION['errors'])) {
        ?>
        <div class="errors">
        <?php
            foreach ($_SESSION['errors'] as $error) {
                ?>
                <div class="error">
                    <?= $error ?>
                </div>
                <?php
            }
        ?>
        </div>
        <?php
        }
        ?>
        <form action="/login" method="post">
            <div>
                <label>
                    <p>Login</p>
                    <input type="text" required="required" name="login" placeholder="email@example.org">
                </label>
                <label>
                    <p>Password</p>
                    <input type="password" required="required" name="password" placeholder='••••••••'>
                </label>
                <button>Login</button>
                <div class="or">
                or
                </div>
                <a href="/register" class="btn btn-secondary">Register</a>
            </div>
        </form>
    </div>
</div>    

<?php
    include 'scripts.php';
?>
</body>
</html>