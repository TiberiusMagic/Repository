<?php /** @noinspection ALL */

function connect(){
    try {
        $connection = new PDO("sqlite:identifier.sqlite");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (PDOException $e) {
        echo "Hiba történt a kapcsolat létrehozásakor: " . $e->getMessage();
        return false;
    }
}

function get_password_and_admin($email) {
    if (!($connection = connect())){
        return false;
    }

    try {
        $sql = "SELECT password, admin FROM user WHERE email = :email";
        $stmt = $connection->prepare($sql);

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result; // Asszociatív tömb formátum: ['password' => 'hashed_password', 'admin' => admin_value]
        } else {
            echo "Nem található felhasználó ezzel az email címmel.";
            return false;
        }
    } catch (PDOException $e) {
        echo "Hiba történt az adat lekérdezése során: " . $e->getMessage();
        return false;
    }
}

function check_email($email) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "SELECT COUNT(*) as count FROM user WHERE email = :email";

    try {
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0; //Igen, ha van már ilyen email a db-ben
    } catch (PDOException $e) {
        echo "Hiba történt az adatbázis lekérdezésekor: " . $e->getMessage();
        return false;
    }
}

function list_categories() {
    if (!($connection = connect())){
        return false;
    }

    $sql = "SELECT name FROM category";

    try {
        $stmt = $connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Hiba történt a kategóriák lekérdezésekor: " . $e->getMessage();
        return false;
    }

}

function register_user($email, $password, $name) {
    if (!($connection = connect())){
        return false;
    }
    try{
        $sql = "INSERT INTO user(email, password, name)
            VALUES(:email, :password, :name)";
        $stmt = $connection->prepare($sql);

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        $stmt->execute();
        echo "Felhasználó sikeresen regisztrálva!";
        return true;
    }catch (PDOException $e){
        echo "Hiba történt a felhasználó regisztrálása során: " . $e->getMessage();
        return false;
    }
}


function add_category($category) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO category(name)
            VALUES(:category)";

    //TODO: Befejezni SQLiteosan!!!!!
}


function update_comic_title($id, $title) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "UPDATE comic
            SET title = :title
            WHERE id = :id";

    //TODO: Befejezni SQLiteosan!!!!!
}


function delete_single_comment($id) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE FROM comment WHERE id = :id';

    //TODO: Befejezni SQLiteosan!!!!!
}

function add_comment($content, $email, $comic) {
    if (!($connection = connect())){
        return false;
    }

    $sql = "INSERT INTO PB_Comment(content, user_email, comic_id)
            VALUES(:content, :email, :comic)";

    //TODO: Befejezni SQLiteosan!!!!!
}

function delete_comic($cid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM comic
    WHERE id = :cid';

    //TODO: Befejezni SQLiteosan!!!!!
}



function delete_all_comments_from_picture($cid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM comment
    WHERE comic_id = :cid';

    //TODO: Befejezni SQLiteosan!!!!!
}

function delete_all_categories_from_comic($cid) {
    if (!($connection = connect())){
        return false;
    }

    $sql = 'DELETE
    FROM genre
    WHERE comic_id = :cid';

    //TODO: Befejezni SQLiteosan!!!!!
}

function money_of_user($email){
    if (!($connection = connect())){
        return false;
    }

    try {
        $sql = "SELECT money_forint FROM user WHERE email = :email";

        $stmt = $connection->prepare($sql);

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result;
        } else {
            echo "Nulla forint.";
            return false;
        }
    } catch (PDOException $e) {
        echo "Hiba történt a pénz lekérésekor: " . $e->getMessage();
        return false;
    }
}