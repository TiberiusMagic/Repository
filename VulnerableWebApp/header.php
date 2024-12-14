<header>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    include_once('db_connection.php');

    if (!session_id()) {
        session_start();
    }

    ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/Repository/VulnerableWebApp/index.php">KépregényMánia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Kategóriák:
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            $categories = list_categories();
                            if ($categories === false) {
                                die("Hiba történt a kategóriák lekérdezésekor.");
                            }foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach;?>

                        </ul>
                    </li>


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
                                echo '<li><a class="dropdown-item" href="/Repository/VulnerableWebApp/my_comics.php">Képregényeim</a></li>
                                          <li><a class="dropdown-item" href="/Repository/VulnerableWebApp/my_account.php">Beállítások</a></li>
                                          <li><a class="dropdown-item" href="/Repository/VulnerableWebApp/logout.php?logout=true">Kijelentkezés</a></li>';
                            } else {
                                echo '<li><a class="dropdown-item" href="/Repository/VulnerableWebApp/login.php">Bejelentkezés</a></li>';
                                echo '<li><a class="dropdown-item" href="/Repository/VulnerableWebApp/register.php">Regisztráció</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="balance_adding.php" role="button" aria-expanded="false">
                             <?php
                            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
                                $money = money_of_user($_SESSION['email']);
                                echo $money['money_forint'] . " Forint";
                            }
                            ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
