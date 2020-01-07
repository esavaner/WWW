<!DOCTYPE html>
<?php
    session_start();
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("location: index.php");
        exit;
    }

    require_once "config.php";

    $u = $p = $err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $err = "";
        if(empty(trim($_POST["username"]))) {
            $err = "Username required";
        } else {
            $u = trim($_POST["username"]);
        }

        if(empty(trim($_POST["password"]))) {
            $err = "Password required";
        } else {
            $p = trim($_POST["password"]);
        }

        if(empty($err)) {
            $sql = "SELECT id, username, pass FROM users WHERE username= ?";
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $u;
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1){   
                        mysqli_stmt_bind_result($stmt, $id, $u, $password);
                        if(mysqli_stmt_fetch($stmt)){
                            if($p == $password){
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $u;
                                $_SESSION["time"] = time();                            
                                
                                header("location: index.php");
                            } else {
                                $err = "1";
                            }
                        }
                    } else {
                        $err = "Invalid username or password";
                    }
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
                    <h2 class="text-center">Login</h2>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="username" class="form-control" name="username" placeholder=" Username">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder=" Password">
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