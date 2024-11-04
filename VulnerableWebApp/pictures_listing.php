<?php
include_once('db_connection.php');

if(!session_id()){
    session_start();
}

// add like or vote
if (isset($_POST['mode'])){
    if (!$_SESSION['loggedIn']) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
    if ($_POST['mode'] === "like") {
        echo $_POST['pictureId']." ";
        echo $_SESSION['email']." ";
        add_like($_POST['pictureId'], $_SESSION['email']);
    } else if ($_POST['mode'] === "vote") {
        echo $_POST['contestId']." ";
        echo $_POST['pictureId']." ";
        echo $_SESSION['email']." ";
        add_vote($_POST['contestId'], $_POST['pictureId'], $_SESSION['email']);
    }
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

function show_pictures($stid, $mode, $closed = false) {
    /*  mode (string):
            'likes'
            'user'
            'no_likes'
            'votes'
    */

    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo '<div class="col">
                <div class="card text-bg-light">';
        echo '<img src="Pictures/'.$row['FILENAME'].'" class="card-img-top" alt="'.$row['TITLE'].'">';
        echo '<div class="card-body">';
        echo    '<a class="card-title" href="picture.php/?id='.$row['ID'].'">'.$row['TITLE'].'</a>';

        if ($mode === 'likes') {
            echo '<p class="card-text">'.$row['LAST_NAME'].' '.$row['FIRST_NAME'].'</p>';
            echo '<form action="pictures_listing.php" method="post">';
            echo    '<input type="text" name="pictureId" hidden="hidden" value="'.$row['ID'].'">';
            echo    '<input type="text" name="mode" hidden="hidden" value="like">';
            echo    '<button type="submit" class="like-vote-button"><i class="bi bi-heart-fill"></i></button>';

            echo    '<span class="card-text">'.$row['NUM_OF_LIKES'].'</span>';
            echo '</form>';

        } else if ($mode === 'user') {
            echo '<div>';
            echo '<i class="bi bi-heart-fill" style="padding-right: 5px"></i>';
            echo '<span class="card-text">'.$row['NUM_OF_LIKES'].'</span>';
            echo '</div>';

        } else if ($mode === 'votes') {
            echo '<p class="card-text">'.$row['LAST_NAME'].' '.$row['FIRST_NAME'].'</p>';
            if (!$closed) {
                echo '<form action="pictures_listing.php" method="post">';
                echo    '<input type="text" name="contestId" hidden="hidden" value="'.$row['CONTEST'].'">';
                echo    '<input type="text" name="pictureId" hidden="hidden" value="'.$row['ID'].'">';
                echo    '<input type="text" name="mode" hidden="hidden" value="vote">';
                echo    '<button type="submit" class="like-vote-button"><i class="bi bi-arrow-up-circle-fill"></i></button>';
                echo    '<span class="card-text">'.$row['NUM_OF_VOTES'].'</span>';
                echo '</form>';
            } else {
                echo '<div>';
                if ($row['PLACE'] == 1) {
                    echo '<i class="bi bi-trophy-fill" style="color: gold"></i>';
                } elseif ($row['PLACE'] == 2) {
                    echo '<i class="bi bi-trophy-fill" style="color: silver"></i>';
                } else {
                    echo '<i class="bi bi-trophy-fill" style="color: peru"></i>';
                }
                echo '</div>';
            }
        }

        echo '</div>
                </div>
            </div>';
    }
}

function homepage_listing() {
    echo '<div class="card" style="margin: 10px">
            <div class="card-body">
                <h5 class="card-title">Felkapott</h5>
                <div class="row row-cols-1 row-cols-md-4 g-4">';
    $stid = list_best_pictures();
    show_pictures($stid, 'likes');
    echo '</div>
    </div>
</div>';

    echo '<div class="card" style="margin: 10px">
            <div class="card-body">
                <h5 class="card-title">Összes kép</h5>
                <div class="row row-cols-1 row-cols-md-4 g-4">';
    $stid = list_pictures();
    show_pictures($stid, 'likes');
        echo '</div>
    </div>
</div>';
}

function category_listing($category) {
    echo '<div class="card" style="margin: 10px">
            <div class="card-body">';
    echo    '<h5 class="card-title">'.$category.'</h5>';
    echo    '<div class="row row-cols-1 row-cols-md-4 g-4">';
    $stid = list_pictures_from_category($category);
    show_pictures($stid, 'likes');
    echo '</div>
    </div>
</div>';
}

function contest_listing($contest, $closed) {
    echo '<div>';

    if (!$closed) {
        echo    '<div class="row row-cols-1 row-cols-md-4 g-4">';
        $stid = list_pictures_from_contest($contest);
        show_pictures($stid, 'votes');
    } else {
        echo    '<div class="row row-cols-1 row-cols-md-3 g-4">';
        $stid = list_pictures_from_closed_contest($contest);
        show_pictures($stid, 'votes', true);
    }

    echo '    </div>
          </div>';
}

function search_listing($phrase) {
    echo '<div class="card" style="margin: 10px">
            <div class="card-body">
                <h5 class="card-title">Találatok</h5>
                <div class="row row-cols-1 row-cols-md-4 g-4">';
    $stid = search_pictures($phrase);
    show_pictures($stid, 'likes');
    echo '</div>
    </div>
</div>';
}

function user_pictures_listing($email) {
    echo '<div class="card" style="margin: 10px">
            <div class="card-body">';
    echo    '<h5 class="card-title"></h5>';
    echo    '<div class="row row-cols-1 row-cols-md-4 g-4">';
    $stid = list_user_pictures($email);
    show_pictures($stid, 'user');
    echo '</div>
    </div>
</div>';
}
?>

