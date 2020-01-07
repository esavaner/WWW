<!DOCTYPE html>
<?php

    require_once "config.php";
    $u = $p = $cp = "";
    $err = "";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(empty(trim($_POST["username"]))) {
            $err = "Please enter a username";
        } else {
            $sql = "SELECT id FROM users WHERE username = ?";
            if($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = trim($_POST["username"]);
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1) {
                        $err = "This username is already taken";
                    } else{
                        $u = trim($_POST["username"]);
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
        if(empty(trim($_POST["password"]))) {
            $err = "Please enter a password";     
        } else {
            $p = trim($_POST["password"]);
        }
        if(empty(trim($_POST["cpassword"]))) {
            $err = "Please confirm password";     
        } else {
            $cp = trim($_POST["cpassword"]);
            if($p != $cp){
                $err = "Password did not match";
            }
        }
        if(empty($err)){
            $sql = "INSERT INTO users (username, pass) VALUES (?, ?)";
            if($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
                $param_username = $u;
                $param_password = $p;
                if(mysqli_stmt_execute($stmt)) {
                    header("location: login.php");
                }
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    }
?>
 
<html>

<head>
    <title>Zakamarki kryptografii</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>

<body>
    <div class="project min-vh-100">
            <div style="height: 200px;"></div>
        <div class="row">
            <div class="col-3 mx-auto">
                <form action="" method="post" class="item">
                    <h2 class="text-center">Register</h2>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="username" class="form-control" name="username" placeholder=" Username">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder=" Password">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="cpassword" placeholder=" Password">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                        <span class="col-12" style="color: red;"><?php echo $err; ?></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>