<?php
session_start();
// require_once("connectionController.php");
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

$errorCode = array("email" => 0, "password" => 0);

if (empty($_POST["email"])) {
    $errorCode["email"] = 1;
} else if (!isEmailValid($_POST["email"])) {
    $errorCode["email"] = 2;
} else $email = $_POST["email"];

if (empty($_POST["password"])) {
    $errorCode["password"] = 1;
} else $password = $_POST["password"];

if (isset($email) && isset($password)) {
    $result = isCredentialsValid($email, $password);
    if ($result != -1 && password_verify($password, $result['password'])) {
        $_SESSION['login'] = TRUE;
        $_SESSION['id'] = $result['id'];
        $_SESSION['name'] = $result['name'];
        echo json_encode(['code' => $errorCode, 'result' => "success"]);
        exit;
    } else {
        $errorCode["email"] = 3;
        $errorCode["password"] = 2;
    }
}
echo json_encode(['code' => $errorCode, 'result' => null]);
