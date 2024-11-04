<header>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    include_once('db_connection.php');

    if (!session_id()) {
        session_start();
    }

    if (isset($_GET['delete']) && $_GET['delete'] = 'true') {
        delete_notifications($_SESSION['email']);
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
    ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/photo-book/PhotoBook/index.php">PhotoBook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Kategóriák
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            $stid = list_categories();
                            while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                echo '<li><a class="dropdown-item" href="/photo-book/PhotoBook/?category='.$row['NAME'].'">'
                                    .$row['NAME'].'<span style="float: right">'.$row['NUM_OF_PICTURES'].' '
                                    .'<i class="bi bi-images"></i></span>'.'</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/photo-book/PhotoBook/contests.php">Pályázatok</a>
                    </li>
                    <li class="nav-item">
                        <form class="d-flex" role="search" method="get" action="/photo-book/PhotoBook/index.php">
                            <input class="form-control me-2" type="search" placeholder="Keresés" aria-label="Search" name="search">
                            <button class="btn btn-outline-success" type="submit">Keresés</button>
                        </form>
                    </li>
                    <?php
                    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
                        echo '
                             <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Értesítések
                                </a>
                                <ul class="dropdown-menu">';
                        $stid = list_notifications($_SESSION['email']);
                        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo '<li><a class="dropdown-item">'.$row['DESCRIPTION'].'</a></li>';
                        }
                        echo '
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/photo-book/PhotoBook/header.php/?delete=true">Törlés</a></li>
                                </ul>
                            </li>';
                    }
                    ?>
                    <li class="nav-item dropdown">
                        <?php
                        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
                            echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">'
                                . $_SESSION['email'] .
                                '</a>';
                        } else {
                            echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Saját fiók
                                </a>';
                        }
                        ?>
                        <ul class="dropdown-menu">
                            <?php
                            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
                                echo '<li><a class="dropdown-item" href="/photo-book/PhotoBook/my_pictures.php">Képeim</a></li>
                                          <li><a class="dropdown-item" href="/photo-book/PhotoBook/my_account.php">Beállítások</a></li>
                                          <li><a class="dropdown-item" href="/photo-book/PhotoBook/logout.php?logout=true">Kijelentkezés</a></li>';
                            } else {
                                echo '<li><a class="dropdown-item" href="/photo-book/PhotoBook/login.php">Bejelentkezés</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
