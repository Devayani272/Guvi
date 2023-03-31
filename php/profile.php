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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $details = array("name" => "", "email" => "", "age" => 0, "dob" => 0, "contact" => 0);

    $result = getAllData($_SESSION['id']);

    if ($result != -1) {
        $details["name"] = $result["name"];
        $details["email"] = $result["email"];
        $details["age"] = $result["age"];
        $details["dob"] = $result["dob"];
        $details["contact"] = $result["contact"];
        echo json_encode(['details' => $details, 'result' => "success"]);
    } else
        echo json_encode(['result' => 'error']);
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['name'])) {
        updateName($_POST['name'], $_SESSION['id']);
    }
    if (!empty($_POST['dob'])) {
        updateDOB($_POST['dob'], $_SESSION['id']);
    }
    if (!empty($_POST['contact']) && preg_match('/^[0-9]{10}+$/', $_POST['contact'])) {
        updateContact($_POST['contact'], $_SESSION['id']);
    }
    storeInJSON();
    echo json_encode(['result' => "success"]);
}
