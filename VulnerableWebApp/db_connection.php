<?php /** @noinspection ALL */

function connect(){
    $tns = "
(DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    )
    (CONNECT_DATA =
      (SID = orania2)
    )
  )";

    $connection = oci_connect('C##XYNDL3', 'h049900', $tns, 'UTF8');
    if (!$connection) {
        $error = oci_error();
        echo $error['message'] . "\n";
        die();
    }

    return $connection;
}

function get_password_and_admin($email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'SELECT password, admin FROM PB_user WHERE email = :email';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function check_email($email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'SELECT COUNT(email) AS contains FROM PB_user WHERE email = :email';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_categories() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, 'SELECT name, id, COUNT(picture) AS num_of_pictures
    FROM PB_Category, PB_In_Category WHERE PB_In_Category.category(+) = PB_Category.id GROUP BY name, id');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_cities() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, 'SELECT name, id FROM PB_City');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_popular_foreign_cities() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection,
        'SELECT name FROM PB_City
                WHERE id IN (SELECT PB_Picture.city FROM PB_Picture, PB_User 
                            WHERE PB_Picture.photographer = PB_User.email AND PB_Picture.city != PB_User.city
                            GROUP BY PB_Picture.city ORDER BY count(PB_Picture.id) DESC FETCH FIRST 3 ROWS ONLY)');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_cameras_with_makers() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, 'SELECT PB_Camera.type AS name, PB_Camera.id AS id, PB_Maker.name AS maker
            FROM PB_Camera, PB_Maker WHERE PB_Maker.id = PB_Camera.maker');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_pictures() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, 'SELECT PB_Picture.id, PB_Picture.filename, PB_Picture.title,
        COALESCE(PB_Likes_View.num_of_likes, 0) as num_of_likes,
        PB_User.last_name, PB_User.first_name FROM PB_Picture, PB_Likes_View , PB_User
        WHERE PB_Picture.Photographer = PB_User.email AND (PB_Likes_View.picture(+) = PB_Picture.id) --plus means left outer join
        ORDER BY PB_Picture.id DESC');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_best_pictures() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, 'SELECT PB_Picture.id, PB_Picture.filename, PB_Picture.title, num_of_likes,
        PB_User.last_name, PB_User.first_name FROM PB_Picture, PB_Likes_View, PB_User
        WHERE PB_Picture.Photographer = PB_User.email AND PB_Likes_View.picture = PB_Picture.id
        ORDER BY num_of_likes DESC FETCH FIRST 4 ROWS ONLY');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_pictures_from_category($category) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'SELECT PB_Picture.id, PB_Picture.filename, PB_Picture.title,
        COALESCE(PB_Likes_View.num_of_likes, 0) as num_of_likes, PB_User.last_name, PB_User.first_name
        FROM PB_Picture, PB_User, PB_Category, PB_IN_Category, PB_Likes_View
        WHERE PB_Picture.Photographer = PB_User.email AND PB_In_Category.category = PB_Category.id
        AND PB_In_Category.picture = PB_Picture.id AND PB_Category.name = :category AND (PB_Likes_View.picture(+) = PB_Picture.id)
        ORDER BY PB_Picture.id DESC';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':category', $category);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_pictures_from_contest($contest) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'SELECT PB_Picture.id, PB_Picture.filename, PB_Picture.title, PB_Votes_View.contest,
        num_of_votes, PB_User.last_name, PB_User.first_name
        FROM PB_Picture, PB_User, PB_Votes_View
        WHERE PB_Picture.Photographer = PB_User.email AND PB_Picture.id = PB_Votes_View.picture
        AND PB_Votes_View.contest = :contest
        ORDER BY PB_Picture.id DESC';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':contest', $contest);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_pictures_from_closed_contest($contest) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'SELECT PB_Picture.id, PB_Picture.filename, PB_Picture.title, PB_User.last_name, PB_User.first_name,
        CASE
            WHEN PB_Contest.first = PB_Picture.id THEN 1
            WHEN PB_Contest.second = PB_Picture.id THEN 2
            WHEN PB_Contest.third = PB_Picture.id THEN 3
        END AS place
        FROM PB_Picture, PB_User, PB_Contest
        WHERE PB_Picture.Photographer = PB_User.email AND (PB_Picture.id = PB_Contest.first
        OR PB_Picture.id = PB_Contest.second OR PB_Picture.id = PB_Contest.third)
        AND PB_Contest.id = :contest
        ORDER BY place';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':contest', $contest);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function search_pictures($phrase) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "SELECT PB_Picture.id, PB_Picture.filename, PB_Picture.title,
        COALESCE(PB_Likes_View.num_of_likes, 0) as num_of_likes, PB_User.last_name, PB_User.first_name
        FROM PB_Picture, PB_Likes_View, PB_User WHERE PB_Picture.Photographer = PB_User.email
        AND (PB_Likes_View.picture(+) = PB_Picture.id) AND LOWER(PB_Picture.title) LIKE '%' || :phrase || '%'";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $converted_phrase = strtolower($phrase);
    oci_bind_by_name($stid, ':phrase', $converted_phrase);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_picture_by_id($pid) {
    if (!($connection = connect())){
        return false;
    }


    $stid = oci_parse($connection, 'SELECT 
    PB_Picture.id, PB_Picture.photographer, PB_Picture.filename, PB_Picture.title, PB_Picture.camera,
    PB_User.first_name, PB_User.last_name,
    PB_Country.name as country_name,
    PB_City.country, PB_City.name as city_name,
    PB_Camera.type,
    PB_Maker.name as maker_name,
    COALESCE(PB_Likes_View.num_of_likes, 0) as num_of_likes --if null then return 0
FROM PB_Picture, PB_User, PB_Country, PB_City, PB_Camera, PB_Maker, PB_Likes_View
WHERE PB_Picture.id = :pid
    AND PB_Picture.photographer = PB_User.email
    AND PB_Picture.city = PB_City.id
    AND PB_City.country = PB_Country.id
    AND PB_Maker.id = PB_Camera.maker
    AND PB_Camera.id = PB_Picture.camera
    AND (PB_Likes_View.picture(+) = PB_Picture.id) --plus means left outer join
    ');


    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_user_pictures($email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'SELECT id, filename, title, COALESCE(PB_Likes_View.num_of_likes, 0) as num_of_likes
            FROM PB_Picture, PB_Likes_View WHERE (PB_Likes_View.picture(+) = PB_Picture.id) AND photographer = :email';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_category_for_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, 'SELECT *
    FROM PB_In_Category, PB_Category
    WHERE PB_In_Category.picture = :pid AND PB_In_Category.category = PB_Category.id');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_comment_for_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, 'SELECT PB_User.email, PB_User.first_name, PB_User.last_name, 
    PB_Comment.id, PB_Comment.text, PB_Comment.writer, PB_Comment.picture
    FROM PB_User, PB_Comment
    WHERE PB_Comment.picture = :pid AND PB_User.email = PB_Comment.writer
    ORDER BY PB_Comment.id');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_notifications($email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'SELECT description FROM PB_Notification WHERE email = :email ORDER BY id DESC';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function register_user($email, $password, $lastName, $firstName, $city) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_User(email, password, last_name, first_name, city)
            VALUES(:email, :password, :lastName, :firstName, :city)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':password', $password);
    oci_bind_by_name($stid, ':lastName', $lastName);
    oci_bind_by_name($stid, ':firstName', $firstName);
    oci_bind_by_name($stid, ':city', $city);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $result;
}

function add_picture($filename, $title, $photographer, $camera, $city) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Picture(filename, title, photographer, camera, city) 
            VALUES(:filename, :title, :photographer, :camera, :city)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':filename', $filename);
    oci_bind_by_name($stid, ':title', $title);
    oci_bind_by_name($stid, ':photographer', $photographer);
    oci_bind_by_name($stid, ':camera', $camera);
    oci_bind_by_name($stid, ':city', $city);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $result;
}

function add_picture_to_category($filename, $category) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_In_Category(category, picture)
            VALUES(:category, (SELECT id FROM PB_Picture WHERE filename = :filename))";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':filename', $filename);
    oci_bind_by_name($stid, ':category', $category);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $result;
}

function add_like($pictureId, $email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Like(picture, user_id) VALUES(:picture, :email)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':picture', $pictureId);
    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
    }

    oci_close($connection);
    return $result;
}

function add_vote($contest, $pictureId, $email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Vote(contest, picture, user_id) VALUES(:contest, :picture, :email)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':contest', $contest);
    oci_bind_by_name($stid, ':picture', $pictureId);
    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
    }

    oci_close($connection);
    return $result;
}

function delete_notifications($email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE FROM PB_notification WHERE email = :email';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function delete_category_from_picture($picture, $category) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE FROM PB_In_Category WHERE picture = :picture AND category = (SELECT id FROM PB_Category WHERE name = :category)';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':picture', $picture);
    oci_bind_by_name($stid, ':category', $category);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_category($category) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Category(name)
            VALUES(:category)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':category', $category);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_picture_to_category_by_id($id, $category) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_In_Category(category, picture)
            VALUES((SELECT id FROM PB_Category WHERE name = :category), :id)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':id', $id);
    oci_bind_by_name($stid, ':category', $category);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $result;
}

function update_picture_title($id, $title) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "UPDATE PB_Picture
            SET title = :title
            WHERE id = :id";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':id', $id);
    oci_bind_by_name($stid, ':title', $title);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_country($country) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Country(name)
            SELECT :country FROM dual
            WHERE NOT EXISTS (SELECT 1 FROM PB_Country WHERE name = :country)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':country', $country);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_city($city, $country) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_City(name, country)
        SELECT :city, id FROM PB_Country WHERE name = :country AND NOT EXISTS (
            SELECT 1 FROM PB_City WHERE name = :city AND country = (
                SELECT id FROM PB_Country WHERE name = :country
            )
        )";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':city', $city);
    oci_bind_by_name($stid, ':country', $country);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function update_picture_city($id, $city, $country) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "UPDATE PB_Picture
            SET city =
                (SELECT id FROM PB_City WHERE name = :city AND country =
                    (SELECT id FROM PB_Country WHERE name = :country))
            WHERE id = :id";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':id', $id);
    oci_bind_by_name($stid, ':city', $city);
    oci_bind_by_name($stid, ':country', $country);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_camera_maker($maker) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Maker(name)
            SELECT :maker FROM dual
            WHERE NOT EXISTS (SELECT 1 FROM PB_Maker WHERE name = :maker)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':maker', $maker);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_camera_type($type, $maker) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Camera(type, maker)
        SELECT :type, id FROM PB_Maker WHERE name = :maker AND NOT EXISTS (
            SELECT 1 FROM PB_Camera WHERE type = :type AND maker = (
                SELECT id FROM PB_Maker WHERE name = :maker
            )
        )";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':type', $type);
    oci_bind_by_name($stid, ':maker', $maker);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function update_picture_camera($id, $type, $maker) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "UPDATE PB_Picture
            SET camera =
                (SELECT id FROM PB_Camera WHERE type = :type AND maker =
                    (SELECT id FROM PB_Maker WHERE name = :maker))
            WHERE id = :id";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':id', $id);
    oci_bind_by_name($stid, ':type', $type);
    oci_bind_by_name($stid, ':maker', $maker);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_open_contests_for_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, "
    SELECT DISTINCT PB_Contest.title,
    to_char(PB_Contest.end_date,'YYYY. MM. DD') as end_date
    FROM PB_Vote, PB_Contest
    WHERE PB_Vote.picture = :pid
    AND PB_Contest.id = PB_Vote.contest
    AND PB_Contest.closed = 0
    ");

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_open_contests() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, '
    SELECT PB_Contest.title, PB_Contest.end_date
    FROM PB_Contest
    WHERE PB_Contest.closed = 0
    ');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_contests() {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, "SELECT id, title, description,to_char(end_date,'YYYY. MM. DD') as end_date, closed
            FROM PB_Contest ORDER BY id DESC");

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_picture_to_contest($id, $contest) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Vote(contest, picture, user_id)
            VALUES((SELECT id FROM PB_Contest WHERE title = :contest), :id, 'SYSTEM')";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':id', $id);
    oci_bind_by_name($stid, ':contest', $contest);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $result;
}

function delete_single_comment($picture, $id) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE FROM PB_Comment WHERE id = :id AND picture = :picture';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':picture', $picture);
    oci_bind_by_name($stid, ':id', $id);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function add_comment($text, $email, $pid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Comment(text, writer, picture)
            VALUES(:text, :email, :pid)";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);
    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':text', $text);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $result;
}

function delete_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM PB_Picture
    WHERE id = :pid';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function delete_all_likes_from_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM PB_Like
    WHERE picture = :pid';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function delete_all_comments_from_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM PB_Comment
    WHERE picture = :pid';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function delete_all_categories_from_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM PB_In_Category
    WHERE picture = :pid';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function delete_all_votes_from_picture($pid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM PB_Vote
    WHERE picture = :pid';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function list_picture_in_contest_podium_finishes($pid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "SELECT 
            CASE
                WHEN PB_Contest.first = :pid THEN 1
                WHEN PB_Contest.second = :pid THEN 2
                WHEN PB_Contest.third = :pid THEN 3
            END AS place,
            PB_Contest.title,
            to_char(end_date,'YYYY. MM. DD') as end_date
        FROM PB_Contest
        WHERE PB_Contest.closed = 1
            AND (:pid IN (PB_Contest.first, PB_Contest.second, PB_Contest.third))
    ORDER BY place
    ";

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function already_liked($pid, $email) {
    if (!($connection = connect())){
        return false;
    }

    $stid = oci_parse($connection, '
    SELECT Count(*) as count
    FROM PB_Like
    WHERE PB_Like.picture = :pid
    AND PB_Like.user_id = :email
    ');

    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);
    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }
    //important!!
    $row = oci_fetch_array($stid, OCI_ASSOC);
    $count = $row['COUNT'];

    oci_close($connection);

    return (int)$count;
}

function delete_like($pid, $email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE FROM PB_Like WHERE user_id = :email AND picture = :pid';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':pid', $pid);
    oci_bind_by_name($stid, ':email', $email);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}

function close_contest($contest) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'BEGIN
                PB_close_contest(:contest);
            END;';

    $stid = oci_parse($connection, $sql);
    if (!$stid) {
        $error = oci_error($connection);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_bind_by_name($stid, ':contest', $contest);

    $result = oci_execute($stid);
    if (!$result) {
        $error = oci_error($stid);
        oci_close($connection);
        echo $error['message'] . "\n";
        die();
    }

    oci_close($connection);
    return $stid;
}