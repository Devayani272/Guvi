<?php
// require('connectionController.php');
$serverName = "sql12.freesqldatabase.com";
$userName = "sql12609621";
$password = "8xtCsbagDJ";
$databaseName = "sql12609621";
$c = new mysqli($serverName,$userName,$password,$databaseName);
function isEmailValid($email)
{
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
}

function isEmailUnique($email)
{
    global $c;
    $query = $c->prepare("SELECT * FROM userAccount WHERE email=?");
    $query->bind_param("s", $email);
    $query->execute();
    $query->store_result();
    $result = $query->num_rows;
    $query->close();
    return $result;
}

function initialInsert($name, $email, $password)
{
    global $c;
    $query = $c->prepare("INSERT INTO userAccount (name, email ,password) VALUES (?,?,?)");
    $query->bind_param("sss", $name, $email, $password);
    $result = $query->execute();
    $query->close();
    return $result;
}

function isCredentialsValid($email, $password)
{
    global $c;
    $query = $c->prepare("SELECT id,name,password FROM userAccount WHERE email=?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $query->close();
        return $row;
    } else
        return -1;
}

function getAllData($id)
{
    global $c;
    $query = $c->prepare("SELECT name,email,age,dob,contact FROM userAccount WHERE id=?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $query->close();
        return $row;
    } else
        return -1;
}

function updateName($name, $id)
{
    global $c;
    $query = $c->prepare("UPDATE userAccount SET name=? WHERE id=?");
    $query->bind_param("si", $name, $id);
    $result = $query->execute();
    $query->close();
    return $result;
}

function updateDOB($dob, $id)
{
    $age = date_diff(date_create($dob), date_create('today'))->y;
    global $c;
    $query = $c->prepare("UPDATE userAccount SET dob=?,age=? WHERE id=?");
    $query->bind_param("ssi", $dob, $age, $id);
    $result = $query->execute();
    $query->close();
    return $result;
}

function updateContact($contact, $id)
{
    global $c;
    $query = $c->prepare("UPDATE userAccount SET contact=? WHERE id=?");
    $query->bind_param("ii", $contact, $id);
    $result = $query->execute();
    $query->close();
    return $result;
}

function storeInJSON()
{
    global $c;
    if ($result = $c->query("SELECT * FROM userAccount")) {
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $data[] = $row;
        }
        $data = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('../../users.json', $data);
    }
}

//Initializing error codes
$errorCode = array("name" => 0, "email" => 0, "password" => 0);

// Name Validation
if (empty($_POST["name"])) {
    $errorCode["name"] = 1;
} else {
    $name = $_POST["name"];
}

// Email Validation
if (empty($_POST["email"])) {
    $errorCode["email"] = 1;
} else if (!isEmailValid($_POST["email"])) {
    $errorCode["email"] = 2;
} else if (isEmailUnique($_POST["email"]) > 0) {
    $errorCode["email"] = 3;
} else {
    $email = $_POST["email"];
}

// Password Validation
if (empty($_POST["password"])) {
    $errorCode["password"] = 1;
} else if (strlen($_POST["password"]) < 6) {
    $errorCode["password"] = 2;
} else {
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
}

//Database Interaction (Insertion)
if ($errorCode["name"] == 0 && $errorCode["email"] == 0 && $errorCode["password"] == 0) {
    if (initialInsert($name, $email, $password) === TRUE) {
        storeInJSON();
        echo json_encode(['code' => $errorCode, 'result' => "success"]);
    }
} else {
    echo json_encode(['code' => $errorCode, 'result' => null]);
}
