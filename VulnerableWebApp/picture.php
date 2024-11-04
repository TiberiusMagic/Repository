<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PhotoBook</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<?php
include_once('db_connection.php');
//include_once('pictures_listing.php');

if (!session_id()) {
    session_start();
}

//Getting data
$pic = oci_fetch_array(list_picture_by_id($_GET['id']), OCI_ASSOC + OCI_RETURN_NULLS);
$pic_cats = list_category_for_picture($_GET['id']);
$all_cats = list_categories();
$coms = list_comment_for_picture($_GET['id']);

//If changing data from picture
if (isset($_POST['submit_picture_change'])) {
    //Title
    if (!empty($_POST['picture_title'])) {
        update_picture_title($_GET['id'], $_POST['picture_title']);
        header("Location: ../picture.php/?id=" . $_GET['id']);
    }

    //Place
    if (!empty($_POST['picture_country_name']) && !empty($_POST['picture_city_name'])) {
        add_country($_POST['picture_country_name']);
        add_city($_POST['picture_city_name'], $_POST['picture_country_name']);
        update_picture_city($_GET['id'], $_POST['picture_city_name'], $_POST['picture_country_name']);
        header("Location: ../picture.php/?id=" . $_GET['id']);
    }


    //Camera
    if (!empty($_POST['picture_camera_maker']) && !empty($_POST['picture_camera_type'])) {
        add_camera_maker($_POST['picture_camera_maker']);
        add_camera_type($_POST['picture_camera_type'], $_POST['picture_camera_maker']);
        update_picture_camera($_GET['id'], $_POST['picture_camera_type'], $_POST['picture_camera_maker']);
        header("Location: ../picture.php/?id=" . $_GET['id']);
    }

    //Categories
    if (!empty($_POST['picture_category_remove'])) {
        delete_category_from_picture($_GET['id'], $_POST['picture_category_remove']);
        header("Location: ../picture.php/?id=" . $_GET['id']);
    }
    if (!empty($_POST['picture_category_add'])) {
        add_picture_to_category_by_id($_GET['id'], $_POST['picture_category_add']);
        header("Location: ../picture.php/?id=" . $_GET['id']);
    }

    if (!empty($_POST['new_category_add'])) {
        add_category($_POST['new_category_add']);
        add_picture_to_category_by_id($_GET['id'], $_POST['new_category_add']);
        header("Location: ../picture.php/?id=" . $_GET['id']);
    }
}

//If adding picture to contest
if (isset($_POST['submit_picture_contest_add'])) {
    if (!empty($_POST['picture_contest_add'])) {
        add_picture_to_contest($_GET['id'], $_POST['picture_contest_add']);
        header("Location: ../picture.php/?id=" . $_GET['id']);
    }
}

//If deleting comment
if (isset($_POST['comment_delete_button'])) {
    delete_single_comment($_GET['id'], $_POST['comment_id']);
    header("Location: ../picture.php/?id=" . $_GET['id']);
}

//If sending comment
if (isset($_POST['comment_send'])) {
    add_comment($_POST['comment_text'], $_SESSION['email'], $_GET['id']);
    header("Location: ../picture.php/?id=" . $_GET['id']);
}

if (isset($_SESSION['email'])) {
    $liked = already_liked($_GET['id'], $_SESSION['email']);
}
//If like/unlike
if (isset($_POST['submit_like'])) {
    if ($liked == 0) {
        add_like($_GET['id'], $_SESSION['email']);
        $pic['NUM_OF_LIKES'] += 1;
    } else {
        delete_like($_GET['id'], $_SESSION['email']);
    }
    header("Location: ../picture.php/?id=" . $_GET['id']);
}

//If deleting picture
if (isset($_POST['delete_picture_send']) && isset($_POST['delete_picture_title']) && $_POST['delete_picture_title'] === $pic['TITLE']) {
    unlink('Pictures/' . $pic['FILENAME']);
    //delete_all_likes_from_picture($_GET['id']);
    //delete_all_comments_from_picture($_GET['id']);
    //delete_all_categories_from_picture($_GET['id']);
    //delete_all_votes_from_picture($_GET['id']);
    delete_picture($_GET['id']);
    header("Location: ../index.php");
}


include_once('header.php');


//print_r($_GET);
//echo '<br>';
//print_r($_POST);
//echo '<br>';
//print_r($_SESSION);
//echo '<br>';
//print_r($pic);


//Picture
echo '<div class="card" style="width: 75%; margin: 0 auto">
        <div class="card-body">';
echo '<img class="card-img-top" src="../Pictures/' . $pic['FILENAME'] . '" alt="' . $pic['TITLE'] . '">';
echo '<br><br>';

$photographer_or_admin = False;
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && ($_SESSION['email'] === $pic['PHOTOGRAPHER']
        || $_SESSION['Admin'] === true)) {
    $photographer_or_admin = True;
}

//If user is the photographer or an admin
//TODO Maybe split the parts by the ifs so it is more readable
if ($photographer_or_admin === True) {
?>
<!--Title-->
<div class="d-flex justify-content-between w-100">
    <form class="form-floating" style="flex: 1;margin-right: 20px"
          action="../picture.php/?id=<?php echo $pic['ID']; ?>" method="post">
        <div class="input-group mb-3">
            <span class="input-group-text fs-5" id="basic-addon_title">Cím</span>
            <input type="text" class="form-control fs-5" value="<?php echo $pic['TITLE']; ?>"
                   aria-describedby="basic-addon_title">
        </div>

        <!--Photographer name-->
        <div class="form-floating mb-3">
            <div class="input-group-text fs-5 bg-white"><?php echo $pic['LAST_NAME'] . ' ' . $pic['FIRST_NAME'] ?></div>
        </div>

        <!--Place-->
        <div class="input-group mb-3">
            <i class="bi bi-map text-primary fs-1 me-3" id="basic-addon_place"></i>
            <div class="form-floating">
                <input class="form-control fs-6 " type="text" id="picture_city_name" name="picture_city_name"
                       value="<?php echo $pic['CITY_NAME'] ?>">
                <label for="picture_city_name">Város</label>
            </div>
            <div class="form-floating">
                <input class="form-control fs-6" type="text" id="picture_country_name" name="picture_country_name"
                       value="<?php echo $pic['COUNTRY_NAME'] ?>">
                <label for="picture_country_name">Ország</label>
            </div>
        </div>

        <!--Camera-->
        <div class="input-group mb-1">
            <i class="bi bi-camera2 text-primary fs-1 me-3" id="basic-addon_camera"></i>
            <div class="form-floating">
                <input class="form-control fs-6 " type="text" id="picture_camera_maker" name="picture_camera_maker"
                       value="<?php echo $pic['MAKER_NAME'] ?>">
                <label for="picture_camera_maker">Gyártó</label>
            </div>
            <div class="form-floating">
                <input class="form-control fs-6" type="text" id="picture_camera_type" name="picture_camera_type"
                       value="<?php echo $pic['TYPE'] ?>">
                <label for="picture_camera_type">Típus</label>
            </div>
        </div>

        <!--Likes (form at eof)-->
        <div class="d-flex justify-content-between align-items-center">
            <button type="submit" form="like" name="submit_like" class="btn btn-link p-0 like-button">
                <?php
                if ($liked == 0) {
                    echo '<i class="bi bi-heart-fill fs-2 text-black"></i>';
                } else {
                    echo '<i class="bi bi-heart-fill fs-2 text-danger"></i>';
                }
                ?>
            </button>
            <span class="card-body fs-3"><?php echo $pic['NUM_OF_LIKES'] ?></span>
        </div>

        <!--Categories-->

        <div class="d-flex justify-content-start align-items-center mb-3">
            <i class="bi bi-tag-fill text-primary fs-2">&nbsp;&nbsp;</i>
            <?php
            $existing_cats = array();
            while ($row = oci_fetch_array($pic_cats, OCI_ASSOC + OCI_RETURN_NULLS)) {
                $existing_cats[] = $row['NAME'];
                echo '<span class="fs-4 me-3">' . $row['NAME'] . '</span>';
            }
            ?>
        </div>

        <!--Category remove-->
        <div class="input-group mb-2">
            <div class="form-floating">
                <select class="form-select" name="picture_category_remove" id="picture_category_remove">
                    <option selected disabled>Válassz a listából</option>
                    <?php
                    foreach ($existing_cats as $one_cat) {
                        echo '<option value="' . $one_cat . '">' . $one_cat . '</option>';
                    }
                    ?>
                </select>
                <label for="picture_category_remove">Kategória kitörlése</label>
            </div>


            <!--Category add-->
            <div class="form-floating">
                <select class="form-select" name="picture_category_add" id="picture_category_add">
                    <option selected disabled>Válassz a listából</option>
                    <?php
                    while ($row = oci_fetch_array($all_cats, OCI_ASSOC + OCI_RETURN_NULLS)) {
                        if (!in_array($row['NAME'], $existing_cats)) {
                            echo '<option value="' . $row['NAME'] . '">' . $row['NAME'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <label for="picture_category_add">Kategória hozzáadása</label>
            </div>
        </div>

        <!--New category-->
        <div class="form-floating">
            <input type="text" class="form-control mb-2" name="new_category_add" id="new_category_add">
            <label for="new_category_add">Nincs még ilyen kategória? Hozd létre:</label>
        </div>
        <input class="btn btn-outline-primary btn me-md-2" type="submit" name="submit_picture_change"
               value="Adatok módosítása">
        <input class="btn btn-outline-secondary btn" type="reset" value="Mégsem">
    </form>

    <?php
    //Contest
    $pic_contests = list_open_contests_for_picture($_GET['id']);
    $all_contests = list_open_contests();

    echo '<div style="flex: 1">';
    echo '<h4>Pályázatra jelentkezések:</h4>';
    $no_contests = True;
    $existing_contests = array();
    while ($row = oci_fetch_array($pic_contests, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo '<p>' . $row['TITLE'] . '  Lejárat ideje: ' . $row['END_DATE'] . '</p>';
        $existing_contests[] = $row['TITLE'];
        $no_contests = False;
    }
    if ($no_contests) echo '<p>Ez a kép nincs egy pályázaton se. Jelentkezz fel egyre!</p>';

    echo '<form action="../picture.php/?id=' . $pic['ID'] . '" method="post">';
    echo '<select class="form-control" name="picture_contest_add">';
    echo '<option selected disabled>Válassz a listából</option>';
    while ($row = oci_fetch_array($all_contests, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if (!in_array($row['TITLE'], $existing_contests)) {
            echo '<option value="' . $row['TITLE'] . '">' . $row['TITLE'] . '</option>';
        }
    }
    echo '</select>';
    echo '<br>';
    echo '<input class="btn btn-outline-primary btn me-md-2" type="submit" name="submit_picture_contest_add" value="Beküldés">';
    echo '<input class="btn btn-outline-secondary btn" type="reset" value="Mégsem">';
    echo '</form>';
    echo '<br>';


    //Print winnings or delete picture
    $won_contest_exists = [False, False, False];
    $won_contest_list = list_picture_in_contest_podium_finishes($_GET['id']);

    while ($row = oci_fetch_array($won_contest_list, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if (!in_array(true, $won_contest_exists)) echo '<h4>Pályázati eredmények: </h4>';
        if ($row['PLACE'] == 1 && $won_contest_exists[0] === False) {
            echo '<p class="p-2 rounded-2" style="background-color: gold">&nbsp;I. helyezés:';
            $won_contest_exists[0] = True;
        } else if ($row['PLACE'] == 2 && $won_contest_exists[1] === False) {
            echo '<p class="p-2 rounded-2" style="background-color: silver">&nbsp;II. helyezés:';
            $won_contest_exists[1] = True;
        } else if ($row['PLACE'] == 3 && $won_contest_exists[2] === False) {
            echo '<p class="p-2 rounded-2" style="background-color: peru">&nbsp;III. helyezés:';
            $won_contest_exists[2] = True;
        }
        echo '<br>';
        echo '&nbsp;' . $row['TITLE'] . ' - ' . $row['END_DATE'] . '</p>';
    }

    if (!(in_array(true, $won_contest_exists))) {
        echo '<br>';
        echo '<form class="floating-form" enctype=multipart/form-data method="post" action="../picture.php/?id=' . $pic['ID'] . '">';
        echo '<input type="hidden" name="delete_picture_id" value="' . $pic['ID'] . '">';
        echo '<br>';
        echo '<label class="mb-2" for="delete_picture">Kép törlése: (a hitelesítéshez írd be a kép címét!)</label>';
        echo '<input class="form-control mb-2" type="text" name="delete_picture_title" id="delete_picture">';
        echo '<input class="btn btn-danger btn" type="submit" name="delete_picture_send" value="Törlés">';
        echo '</form>';
    }


    echo '</div></div>';
    echo '<br>';
    // Comments
    echo '<form method="post" action="../picture.php/?id=' . $pic['ID'] . '">';
    echo '<div class="form-floating mt-4">';
    echo '<textarea class="form-control" style="height:100px" name="comment_text" id="floatingTextarea"></textarea>';
    echo '<label for="floatingTextarea">Írj egy hozzászólást!</label>';
    echo '<input class="btn btn-outline-primary btn my-md-2" type="submit" name="comment_send" value="Küldés">';
    echo '</div>';
    echo '</form>';

    echo '<br>';

    while ($row = oci_fetch_array($coms, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo '<form method="post" action="../picture.php/?id=' . $pic['ID'] . '">';
        echo '<div class="bg-body-secondary p-2 rounded-3">
              <div class="fw-bold">' . $row['LAST_NAME'] . ' ' . $row['FIRST_NAME'] . '</div>
              <p>' . $row['TEXT'] . '</p>' .
            '<input type="hidden" name="comment_id" value="' . $row['ID'] . '">' .
            '<input class="btn btn-outline-danger" type="submit" name="comment_delete_button" value="Törlés">' .
            '</div>';
        echo '</form>';
        echo '<br>';
    }

    } else { // guest users
        ?>
        <div class="d-flex justify-content-between w-100">
    <form class="form-floating" style="flex: 1;margin-right: 20px"
          action="../picture.php/?id=<?php echo $pic['ID']; ?>" method="post">
        <div class="input-group mb-3">
            <span class="input-group-text fs-5" id="basic-addon_title">Cím</span>
            <input readonly type="text" class="form-control fs-5" value="<?php echo $pic['TITLE']; ?>"
                   aria-describedby="basic-addon_title">
        </div>

        <!--Photographer name-->
        <div class="form-floating mb-3">
            <div class="input-group-text fs-5 bg-white"><?php echo $pic['LAST_NAME'] . ' ' . $pic['FIRST_NAME'] ?></div>
</div>

<!--Place-->
<div class="input-group mb-3">
    <i class="bi bi-map text-primary fs-1 me-3" id="basic-addon_place"></i>
    <div class="form-floating">
        <input readonly class="form-control fs-6" type="text" id="picture_city_name" name="picture_city_name"
               value="<?php echo $pic['CITY_NAME'] ?>">
        <label for="picture_city_name">Város</label>
    </div>
    <div class="form-floating">
        <input readonly class="form-control fs-6" type="text" id="picture_country_name" name="picture_country_name"
               value="<?php echo $pic['COUNTRY_NAME'] ?>">
        <label for="picture_country_name">Ország</label>
    </div>
</div>

<!--Camera-->
<div class="input-group mb-1">
    <i class="bi bi-camera2 text-primary fs-1 me-3" id="basic-addon_camera"></i>
    <div class="form-floating">
        <input readonly class="form-control fs-6 " type="text" id="picture_camera_maker" name="picture_camera_maker"
               value="<?php echo $pic['MAKER_NAME'] ?>">
        <label for="picture_camera_maker">Gyártó</label>
    </div>
    <div class="form-floating">
        <input readonly class="form-control fs-6" type="text" id="picture_camera_type" name="picture_camera_type"
               value="<?php echo $pic['TYPE'] ?>">
        <label for="picture_camera_type">Típus</label>
    </div>
</div>

<!--Likes (form at eof)-->
<div class="d-flex justify-content-between align-items-center">
    <?php
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && $_SESSION['email'] !== $pic['PHOTOGRAPHER']) {
    echo'
    <button type="submit" form="like" name="submit_like" class="btn btn-link p-0 like-button">
                <i class="bi bi-heart-fill fs-2 text-danger"></i>
            </button>
            ';

    } else {
        echo '<i class="bi bi-heart-fill fs-2 text-danger"></i>';
    }
    ?>
    <span class="card-body fs-3"><?php echo $pic['NUM_OF_LIKES'] ?></span>

</div>

<!--Categories-->

<div class="d-flex justify-content-start align-items-center mb-3">
    <i class="bi bi-tag-fill text-primary fs-2">&nbsp;&nbsp;</i>
    <?php
    $existing_cats = array();
    while ($row = oci_fetch_array($pic_cats, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $existing_cats[] = $row['NAME'];
        echo '<span class="fs-4 me-3">' . $row['NAME'] . '</span>';
    }
    ?>
</div></form>
<?php

$won_contest_exists = [False, False, False];
$won_contest_list = list_picture_in_contest_podium_finishes($_GET['id']);

echo '<div style="flex: 1">';
while ($row = oci_fetch_array($won_contest_list, OCI_ASSOC + OCI_RETURN_NULLS)) {
    if (!in_array(true, $won_contest_exists)) echo '<h4>Pályázati eredmények: </h4>';
    if ($row['PLACE'] == 1 && $won_contest_exists[0] === False) {
        echo '<p class="p-2 rounded-2" style="background-color: gold">&nbsp;I. helyezés:';
        $won_contest_exists[0] = True;
    } else if ($row['PLACE'] == 2 && $won_contest_exists[1] === False) {
        echo '<p class="p-2 rounded-2" style="background-color: silver">&nbsp;II. helyezés:';
        $won_contest_exists[1] = True;
    } else if ($row['PLACE'] == 3 && $won_contest_exists[2] === False) {
        echo '<p class="p-2 rounded-2" style="background-color: peru">&nbsp;III. helyezés:';
        $won_contest_exists[2] = True;
    }
    echo '<br>';
    echo '&nbsp;' . $row['TITLE'] . ' - ' . $row['END_DATE'] . '</p>';
}
echo '</div></div>';
echo '<div>';
//If user is logged in and is not the photographer/admin
        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && $_SESSION['email'] !== $pic['PHOTOGRAPHER']) {
            //Writing a comment
            echo '<form method="post" action="../picture.php/?id=' . $pic['ID'] . '">';
            echo '<div class="form-floating mt-4">';
            echo '<textarea class="form-control" name="comment_text" id="floatingTextarea"></textarea>';
            echo '<label for="floatingTextarea">Írj egy hozzászólást!</label>';
            echo '<input class="btn btn-outline-primary btn my-md-2" type="submit" name="comment_send" value="Küldés">';
            echo '</div>';
            echo '</form>';
        }

        echo '<br>';

        while ($row = oci_fetch_array($coms, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo '<div class="bg-body-secondary p-2 rounded-3"><div class="fw-bold">' . $row['LAST_NAME'] . ' ' . $row['FIRST_NAME'] . '</div>
              <div>' . $row['TEXT'] . '</div>';
            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && $_SESSION['email'] === $row['WRITER']) {
                echo '<form method="post" action="../picture.php/?id=' . $pic['ID'] . '">';
                echo '<input type="hidden" name="comment_id" value="' . $row['ID'] . '">' .
                    '<input class="btn btn-outline-danger" type="submit" name="comment_delete_button" value="Törlés">';
                echo '</form>';
            }
            echo '</div>';
            echo '<br>';
        }
    }
    echo '</div></div>';
    ?>

    <form action="../picture.php/?id=<?php echo $pic['ID']; ?>" method="post" id="like"></form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
            crossorigin="anonymous"></script>
</body>
</html>