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
    
            $sql = "INSERT INTO comments(val, sender, added) VALUES (?, ?, ?)";
            
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
                        <a class="navigation active" href="index.php">Algorytm Goldwasser-Micali</a>
                    </div>
                    <div class="col-12 col-sm-6 text-center">
                        <a class="navigation" href="s.php">Schemat Shamira</a>
                    </div>
                </div>
            </div>
            <div class="col-auto float-left py-3">
                <div class="navleft p-3">
                    <div class="col"><a class="left" href="#">Algorytm Goldwasser-Micali</a></div>
                    <div class="col point"><a class="left" href="#1.">> Algorytm generowania kluczy</a></div>
                    <div class="col point"><a class="left" href="#2.">> Algorytm szyfrowania</a></div>
                    <div class="col point"><a class="left" href="#3.">> Algorytm deszyfrowania</a></div>
                    <div class="col"><a class="left" href="#4.">Reszta/niereszta kwadratowa</a></div>
                    <div class="col"><a class="left" href="#5.">Symbol Legendre'a</a></div>
                    <div class="col point"><a class="left" href="#6.">> Własnosci</a></div>
                    <div class="col"><a class="left" href="#7.">Symbol Jacobiego</a></div>
                    <div class="col point"><a class="left" href="#8.">> Własnosci</a></div>
                    <div class="col"><a class="left" href="#9.">Algorytm obliczania symboli</a></div>
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
        <div class="container col-6">
            <div class="tag">
                <span id="1.">Algorytm Goldwasser-Micali</span>
            </div>
            <div class="item">
                <h3>Algorytm generowania kluczy</h3>
                <ul>
                    <li>Wybierz losowo dwie duze liczby pierwsze \(p\) oraz \(q\) (podobnego rozmiaru),</li>
                    <li>Policz \(n = pq\)</li>
                    <li>Wybierz \(y \in Zn\), takie, ze \(y\) jest nieresztą kwadratową modulo
                        \(n\) i
                        symbol Jacobiego \((\frac{x}{n})=1\) (czyli \(y\) jest pseudokwadratem
                        modulo
                        \(n\)),</li>
                    <li> Klucz publiczny stanowi para \((n, y)\), zaś odpowiadający mu klucz prywatny to para \((p,
                        q)\)
                    </li>
                </ul>
                <span id="2."></span>
            </div>
            <div class="item">
                <h3>Algorytm szyfrowania</h3>
                Chcąc zaszyfrowac wiadomość \(m\) przy uzyciu klucza publicznego \((n, y)\) wykonaj kroki:
                <ul>
                    <li>Przedstaw \(m\) w postaci łańcucha binarnego \(m = m_{1},m_{2}\ldots m_{t} \) długosci \(t\)
                    </li>
                    <li style="font-family: monospace, monospace;">
                        For i from 1 to t do <br>
                        . wybierz losowe \(x \in \mathbb{Z}_{n}^{*}\) <br>
                        . If \(m_{i} = 1\) then set \(c_{i} \leftarrow y x^{2}\) mod n <br>
                        . Otherwise set \(c_{i} \leftarrow x^{2}\) mod n <br></li>
                    <li> Kryptogram wiadomosci \(m\) stanowi \(c = (c_{1}, c_{2},\ldots , c_{t}\)</li>
                </ul>
                <span id="3."></span>
            </div>
            <div class="item">
                <h3>Algorytm deszyfrowania</h3>
                Chcąc odzyskać wiadomość z kryptogramu \(c\) przy uyciu klucza prywatnego \((p, q)\) wykonaj kroki:
                <ul>
                    <li style="font-family: monospace, monospace;">
                        For i from 1 to t do <br>
                        . policz symbol Legendre’a \(e_{i}= (\frac{c_{i}}{p})\)<br>
                        . If \(e_{i} = 1\) then set \(m_{i} \leftarrow 0\) <br>
                        . Otherwise set \(m_{i} \leftarrow 1\)
                        <span id="4."></span>
                    </li>
                    <li> Zdeszyfrowana wiadomość to \(m = m_{1} m_{2} \ldots m_{t}\)</li>
                </ul>
            </div>
            <div class="tag">
                <span>Reszta/niereszta kwadratowa</span>
            </div>
            <div class="item">
                <h2>Definicja</h2>
                <p>
                    Niech \(a \in \mathbb {Z}_{n}\). Mówimy, ze a jest resztą kwadratową modulo n <i>(kwadratem
                        modulo
                        n)</i>, jezeli istnieje \(x \in \mathbb{Z}_{n}^{*}\)
                    takie, ze \(x^{2} \equiv a (mod p)\)
                    Jezeli takie \(x\) nie istnieje, to wówczas \(a\) nazywamy nieresztą kwadratową
                    modulo n. <span id="5."></span> Zbiór wszystkich reszt kwadratowych modulo n oznaczamy
                    \(Q_{n}\), zas zbiór wszystkich niereszt kwadratowych modulo n oznaczamy \(\bar{Q}_{n}\)
                    .</p>
            </div>
            <div class="tag">
                <span>Symbol Legendre'a</span>
            </div>
            <div class="item">
                <h2>Definicja</h2>
                <p>
                    Niech p będzie nieparzystą liczbą pierwszą a a liczbą całkowitą.
                    Symbol Legendre’a \( (\frac{a}{p})\) jest zdefiniowany jako:
                    $$\big(\frac{a}{p}\big) =\begin{cases}&0 & \text{ jezeli } p|a \\ &1 & \text{ jezeli } a\in
                    Q_{p}\\-&1
                    &\text{ jezeli } a\in \bar{Q}_{p} \end{cases} $$
                </p>
                <span id="6."></span>
            </div>
            <div class="item">
                <h3>Własnosci symbolu Legendre’a</h3>
                Niech \(a,b \in \mathbb {Z}_{n}\), zaś p to nieparzysta liczba pierwsza. Wówczas:

                $$ \big(\frac{a}{p}\big) \equiv a^{\frac{p-1}{2}} \text{ (mod p)}$$
                $$ \big(\frac{ab}{p}\big)= \big(\frac{a}{p}\big) \big(\frac{b}{p}\big)$$
                $$ a\equiv b \text{ (mod p) } \Rightarrow\big(\frac{a}{p}\big) = \big(\frac{b}{p}\big)$$
                $$\big(\frac{2}{p}\big)= \big(-1\big)^{\frac{p^{2}-1}{8}}$$
                Jezeli q jest nieparzystą liczbą pierwszą inną od p to:<span id="7."></span>
                $$\big(\frac{p}{q}\big)=\big(\frac{p}{q}\big)\big(-1\big)^{\frac{(p-1)(q-1)}{4}}$$
                </ul>
            </div>
            <div class="tag">
                <span>Symbol Jacobiego</span>
            </div>
            <div class="item">
                <h2>Definicja</h2>
                <p>
                    Niech \(n \geq 3\) będzie liczbą nieparzystą, a jej rozkład na czynniki pierwsze to \(n =
                    p_{1}^{e_{1}}p_{2}^{e_{2}} \cdots p_{k}^{e_{k}}\). Symbol Jacobiego
                    \(\big(\frac{a}{n}\big)\) jest zdefiniowany
                    jako:
                    $$\big(\frac{a}{n}\big) = \big(\frac{a}{p_{1}}\big)^{e_{1}} \big(\frac{a}{p_{2}}\big)^{e_{2}}
                    \cdots
                    \big(\frac{a}{p_{k}}\big)^{e_{k}}$$
                    <b>Jezeli n jest liczbą pierwszą, to symbol Jacobieo jest symbolem Legendre’a.</b>
                </p>
                </p>
                <span id="8."></span>
            </div>
            <div class="item">
                <h3>Własnosci symbolu Jacobiego</h3>
                Niech \(a,b \in \mathbb {Z}_{n}\), zaś \(m,n \geq 3\) to nieparzyste liczby całkowite. Wówczas:
                $$\big(\frac{a}{n}\big)=0,1,albo -1. \text{Ponadto }\big(\frac{a}{n}\big)=0
                \Longleftrightarrow
                gcd(a,n) \neq 1$$
                $$ \big(\frac{ab}{n}\big)= \big(\frac{a}{n}\big) \big(\frac{b}{n}\big)$$
                $$ \big(\frac{a}{mn}\big)= \big(\frac{a}{m}\big) \big(\frac{a}{n}\big)$$
                $$a \equiv b \text{ (mod n) }\big(\frac{1}{n}\big)=\big(\frac{b}{n}\big)$$
                $$\big(\frac{1}{n}\big)=1$$
                $$\big(\frac{-1}{n}\big)= \big(-1\big)^{\frac{(n-1)}{2}}$$
                $$\big(\frac{2}{n}\big)= \big(-1\big)^{\frac{(n^{2}-1)}{8}}$$
                $$\big(\frac{m}{n}\big)= \big(\frac{n}{m}\big)\big(-1\big)^{\frac{(m-1)(n-1)}{4}}$$

                Z własnosci symbolu Jacobiego wynika, ze jezeli n nieparzyste oraz a nieparzyste i w postaci
                \(a =
                2^{e}a_{1}\), gdzie \(a_{1}\) tez nieparzyste to:
                $$\big(\frac{a}{n}\big)=\big(\frac{2^{e}}{n}\big)
                \big(\frac{a_{1}}{n}\big)=\big(\frac{2}{n}\big)^{e}\big(\frac{n\text{ mod }
                a_{1}}{a_{1}}\big)\big(-1\big)^{\frac{(a_{1}-1)(n-1)}{4}}$$
                <span id="9."></span>
            </div>
            <div class="item">
                <h3><b>Algorytm obliczania symbolu Javobiego \(\big(\frac{m}{n}\big)\) (i Legendre'a)</b></h3>
                \( \text{dla nieparzystej liczby całkowitej }n \geq 3 \text{ oraz całkowitego } 0 \leq a \leq n \)
                <p style="font-family: monospace, monospace;">
                    JACOBI(a,n) <br>
                    . If a=0 then return 0 <br>
                    . If a=1 then retrun 1 <br>
                    . Write \(a= 2^{e}a_{1} \text{, gdzie } a_{1} \) nieparzyste <br>
                    . If e parzyste set \(s \leftarrow 1\) <br>
                    . Otherwise set \(s \leftarrow 1 \text{ if n} \equiv 1 \text{ or 7 (mod 8), or set s} \leftarrow
                    -1 if n
                    \equiv 3 \text{ or 5 (mod8)}\) <br>
                    . If \(n \equiv 3 \text{ (mod 4) and }a_{1}\equiv 3 \text{ (mod4) then set) then set }
                    s\leftarrow -s\)
                    <br>
                    . Set \(a_{1}=1\) then return \(s\)<br>
                    . If \(a_{1}=1\) then return \(s\)<br>
                    . Otherwise return \(s \ast JACOBI(n_{1},a_{1})\)<br>
                </p>
                <h4>Algorytm działa w czasie \( O ((lg n)^{2} )\) operacji bitowych </h4>
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
                    $sql = "SELECT * FROM comments ORDER BY id DESC";
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