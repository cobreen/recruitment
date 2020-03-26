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
    <div class="register">
        <div class="pokemon">
            <img src="/img/new_user.png" alt="Pokemon">
        </div>
        <h1>New user?</h1>
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
        <form action="/register" method="post">
            <div>
                <label>
                    <p>Name</p>
                    <input type="text" required="required" name="name" placeholder="Ash Ketchum">
                </label>
                <label>
                    <p>Email</p>
                    <input type="email" required="required" name="email" placeholder="email@example.org">
                </label>
                <label>
                    <p>Password</p>
                    <input type="password" required="required" name="password" placeholder='••••••••'>
                </label>
                <button>register</button>
                <div class="or">
                or
                </div>
                <a href="/login" class="btn btn-secondary">Login</a>
            </div>
        </form>
    </div>
</div>    

<?php
    include 'scripts.php';
?>
</body>
</html>