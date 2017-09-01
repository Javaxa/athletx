<?php
session_start();

$username = "rkljavaxa@gmail.com";
$password = "Jaavaaxaa1";

if (isset($_POST["username"]) && isset($_POST["password"])) {
    if ($_POST["username"] == $username && $_POST["password"] == $password) {
        $_SESSION["admin"] = true;
        die();
    }
}

if (isset($_POST["logout"])) {
    session_destroy();
    die();
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View sent emails</title>
</head>
<style type="text/css">
    html {
    height: 100%;
    width: 100%;
    margin: 0;
    font-family: Helvetica, Arial;
    background: #858d98;
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#858d98', endColorstr='#4a525d',GradientType=1 );
    background: -moz-radial-gradient(top, ellipse cover, #858d98 1%, #4a525d 99%);
    background: -webkit-gradient(radial, top top, 0px, top top, 100%, color-stop(1%,#858d98), color-stop(99%,#4a525d));
    background: -webkit-radial-gradient(top, ellipse cover, #858d98 1%,#4a525d 99%);
    background: -o-radial-gradient(top, ellipse cover, #858d98 1%,#4a525d 99%);
    background: -ms-radial-gradient(top, ellipse cover, #858d98 1%,#4a525d 99%);
    background: radial-gradient(ellipse at top, #858d98 1%,#4a525d 99%);
    color: white;
    }
    
    .email-row {
        width: 100%;
        padding: 20px;
        box-sizing: border-box;
    background: #858d98;
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#858d98', endColorstr='#4a525d',GradientType=1 );
    background: -moz-radial-gradient(top, ellipse cover, #858d98 1%, #4a525d 99%);
    background: -webkit-gradient(radial, top top, 0px, top top, 100%, color-stop(1%,#858d98), color-stop(99%,#4a525d));
    background: -webkit-radial-gradient(top, ellipse cover, #858d98 1%,#4a525d 99%);
    background: -o-radial-gradient(top, ellipse cover, #858d98 1%,#4a525d 99%);
    background: -ms-radial-gradient(top, ellipse cover, #858d98 1%,#4a525d 99%);
    background: radial-gradient(ellipse at top, #858d98 1%,#4a525d 99%);
    }
    
    .email-time {
        width: 100%;
        margin: 5px 0;
        text-align: right;
        font-size: 14px;
    }
    
    .email-row:nth-child(even) {
        background: darkgrey;
        color: black;
    }
    
    .email-row-line {
        width: 100%;
        margin: 5px 0;
    }
    
    .email-row-title {
        font-weight: bold;
        float: left;
        margin-right: 5px;
    }
    
    .email-row-content {
        white-space: normal;
    }
    
    .login-form {
        width: 400px;
        margin: auto;
        margin-top: 300px;
    }

    .log-out {
        margin-left: 45%;
        width: 100px;
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>
<body>
    <?php
        if ($_SESSION["admin"] != true) {
    ?>
   
    <div class="login-form">
        <p>Login</p>
        <input name="username" placeholder="Username" />
        <input name="password" placeholder="Password" type="password" />
        <button class="log-in">Log In</button>
    </div>
    
    <?php
        }
    ?>
    
    <?php
        define("JSON_PATH", "contact.json");
        $jsonString = file_get_contents(JSON_PATH);
        $json       = json_decode($jsonString, true, 2048);
        
        if ($_SESSION["admin"] == true) {
    ?>
            <button class="log-out">Log Out</button>
    <?php
            foreach ($json as $index => $object) {
    ?>
            <div class="email-row">
                <div class="email-time"><?php echo date("F dS, Y \a\\t H:i:s A", $object["time"] + 4 * 3600); ?></div>
                <?php
                    foreach ($object as $key => $value) {
                        
                    if ($key == "time") continue; // ignore time key
                ?>
                    <div class="email-row-line">
                        <div class="email-row-title"><?php echo $key; ?></div>
                        <div class="email-row-content"><?php echo $value; ?></div>
                    </div>
                <?php
                    }
                ?>
            </div>
    <?php
            }
        }
    ?>
</body>
<script type="text/javascript" src="/js/jquery.js"></script>
<script>
    $(".log-in").click(function() {
        $.post("viewcontacts.php", $(".login-form input").serialize(), function() {
            location.reload();
        });
    });
    
    $(".log-out").click(function() {
        $.post("viewcontacts.php", {logout: true}, function() {
            location.reload();
        });
    });
</script>
</html>