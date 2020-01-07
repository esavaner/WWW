<!DOCTYPE html>
<?php
    $src = "counter.txt";
    if(!file_exists($src)) {
        $f = fopen($src, "w");
        fwrite($f, "0");
        fclose($f);
    }

    $f = fopen($src, "r");
    $visits = fread($f, filesize($src));
    fclose($f);

    if(!isset($_COOKIE["visited"])) {
        $visits++;
        $fc = fopen($src, "w");
        fwrite($fc, $visits);
        fclose($fc);
        setcookie("visited", "visited", time() + 24*60*60, '/');
    }

    $maxtime = 5*60;

    session_start();
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        if(time() - $_SESSION["time"] > $maxtime) {
            header("location: logout.php");
        } else {
            $_SESSION["time"] = time();
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        session_start();
        if(time() - $_SESSION["time"] > $maxtime) {
            header("location: logout.php");
        } else {
            require_once "config.php";
            $_SESSION["time"] = time();
            $comment = isset($_POST["comment"]) ? trim($_POST["comment"]) : "";
            $sender = isset($_SESSION["username"]) ? trim($_SESSION["username"]) : "Anonymous";
            $date = date("H:i:s d-m-Y");
    
            $sql = "INSERT INTO scomments(val, sender, added) VALUES (?, ?, ?)";
            
            if($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $param_comment, $param_sender, $param_date);
                $param_comment = $comment;
                $param_sender = $sender;
                $param_date = $date;
                if(mysqli_stmt_execute($stmt) === false ) {
                    die(mysqli_stmt_execute_error());
                    //header("location: index.php");
                }
            }
            mysqli_stmt_close($stmt);
            mysqli_close($link);
        }
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
        <h1 class="text-center" style="color:rgb(223, 223, 223);">Zakamarki kryptografii</h1>
        <div class="sticky">
            <div class="container">
                <div class="row p-2">
                    <div class="col-12 col-sm-6 text-center">
                        <a class="navigation" href="index.php">Algorytm Goldwasser-Micali</a>
                    </div>
                    <div class="col-12 col-sm-6 text-center">
                        <a class="navigation active" href="s.php">Schemat Shamira</a>
                    </div>
                </div>
            </div>
            <div class="col-auto float-left py-3">
                <div class="navleft p-3">
                    <div class="col"><a class="left" href="#">Schemat Sekretu Shamira</a></div>
                    <div class="col"><a class="left" href="#1.">Interpolacja Lagrange’a</a></div>
                    <div class="col"><a class="left" href="#2.">Przykład</a></div>
                </div>
                <div style="background: #59595f; height: 10px;">

                </div>
                <div class="navleft p-3 text-center">
                    <?php
                        session_start();
                        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) { 
                    ?>
                        <span><?php echo $_SESSION["username"] ?></span>
                        <a href="logout.php"><button class="btn btn-outline-primary">Wyloguj</button></a>
                    <?php } else { ?>
                        <a href="login.php"><button class="btn btn-outline-primary">Zaloguj</button></a>
                        <a href="register.php"><button class="btn btn-primary">Zarejestruj</button></a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="container col-6 mt-4">
            <div class="item">
                <h3> Schemat progowy \((t, n)\) dzielenia Sekretu Shamira</h3>
                <b>Cel:</b>
                Zaufana Trzecia Strona T ma sekret \(S \geq 0\), który chce podzielic pomiędzy \(n\) uczestników
                tak, aby dowolnych t sposród nich mogło sekret odtworzyć <br>
                <b>Faza inicjalizacji</b>
                <ul>
                    <li>\( \text{T wybiera liczbę pierwszą p > max(S, n) i definiuje }a_{0} = S,\)</li>
                    <li> T wybiera losowo i niezaleznie \(t-1\) współczynników \(a_{1},\ldots,a_{t-1} \in
                        \mathbb {Z}_{p}\)</li>
                    <li> T definiuje wielomian nad \(\mathbb {Z}_{p}\)
                        $$ f(x)= a_{0} + \sum^{t-1}_{j=1}a_{j}x^{j}$$
                    </li>
                    <li> Dla \( 1 \leq i \leq n\) Zaufana Trzecia Strona T wybiera losowo \( x_{i} \in \mathbb
                        {Z}_{p}\), oblicza: \( S_{i} = f(x_{i)}\) mod p i bezpiecznie przekazuje parę
                        \((x_{i},S_{i})\) uzytkownikowi \(P_{i}\)</li>
                </ul>
                <b>Faza łączenia udziałów w sekret</b>
                Dowolna grupa t lub więcej uzytkowników łączy swoje udziały - t róznych punktów \((x_{i},
                S_{i})\) wielomianu f i dzięki interpolacji Lagrange’a odzyskuje sekret \(S = a_{0} = f(0)\).
            </div>
            <div class="item">
                <h3 id="1."> Interpolacja Lagrange’a</h3>
                Mając dane t róznych punktów \((x_{i}
                , y_{i})\) nieznanego wielomianu \(f\) stopnia mniejszego od t mozemy policzyć jego
                współczynniki
                korzystajćc ze wzoru:
                $$ f(x) = \sum^{t}_{i=1}\left( y_{i} \prod_{1 \leq j \leq t, j \neq
                i}\frac{x-x_{j}}{x_{i}-x_{j}}\right) \text{ mod p}$$
            </div>
            <div class="item">
                <h3 id="2."> Przykład </h3>
                \(Sekret=10,\text{ n}=5, \) \(t=3\)
                <ul>
                    <li>Zaufana Trzecia Strona bierze liczbę pierwszą \(p > max(S, n) p=11\)</li>
                    <li> Wybrane wspolczynniki to \(a_{1}=4\) \(a_{2}=10\) więc zdefiniowany wielomian to
                        \(f(x)=a_{0}+a_{1}*x_{1} +a_{2}*x_{2}^{2}\)</li>
                    <li> Obliczone kolejno dla \(x_{1}=3 x_{2}=4 x_{3}=10\) $$ f_{1}(3)=2,
                        f_{2}(4)=10,f_{3}(10)=5
                        $$</li>
                    <li>Ostatecznie łączymy udziały za pomocą interpolacji Lagrange’a
                        \( f(0)=( 2* (\frac{4}{4-3}) * (\frac{10}{10-3}) ) + ( 10 * (\frac{3}{3-4}) *
                        (\frac{10}{10-4}) )+( 5 * (\frac{3}{3-10}) * (\frac{4}{4-10} ) \text{mod p} \)</li>
                    <li> \(f(0)=10 \). Znaleźliśmy sekret</li>
                </ul>
            </div>
            <div class="item">
                <h3>Comments</h3>
                <?php
                    session_start();
                    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) { 
                ?>
                    <form action="" method="post">
                        <div class="form-group py-2 my-2">
                            <textarea type="text" name="comment" style="background: #59595f; color: rgb(200, 200, 200);" 
                            class="form-control w-100" placeholder="Write your comment here" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-primary">Post comment</button>
                        </div>
                    </form>
                <?php } ?>
                <?php
                    require_once("config.php");
                    $sql = "SELECT * FROM scomments ORDER BY id DESC";
                    $res = mysqli_query($link, $sql);
                    while ($r = mysqli_fetch_assoc($res)) {
                ?>
                    <div class="row p-1 pt-2">
                        <div class="col-6">
                            <small>posted by</small>&nbsp;<strong><?php echo $r["sender"]; ?></strong>
                        </div>
                        <div class="col-6">
                            <span class="float-right">
                                <small>at</small>&nbsp;<strong><?php echo $r["added"]; ?></strong>
                            </span>
                        </div>
                    </div>
                    <div class="col">
                        <?php echo $r["val"]; ?>
                    </div>
                    <hr>
                <?php 
                    }
                    mysqli_close($link);
                ?>
            </div>
        </div>
        <div style="height: 30px;"></div>
        <div class="container text-center" style="color: rgb(200, 200, 200);">
            Visits:&nbsp;
            <?php echo $visits ?>
        </div>
        <div style="height: 30px;"></div>
    </div>

</body>

</html>