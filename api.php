<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "api_example";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$request_method = $_SERVER["REQUEST_METHOD"];
switch($request_method) {
    case 'GET':
        if (!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            get_item($id);
        } else {
            get_items();
        }
        break;
    case 'POST':
        insert_item();
        break;
    case 'PUT':
        $id = intval($_GET["id"]);
        update_item($id);
        break;
    case 'DELETE':
        $id = intval($_GET["id"]);
        delete_item($id);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_items() {
    global $conn;
    $query = "SELECT * FROM items";
    $response = array();
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
    echo json_encode($response);
}

function get_item($id) {
    global $conn;
    $query = "SELECT * FROM items WHERE id=$id";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(array("message" => "Item not found."));
    }
}

function insert_item() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data["name"];
    $description = $data["description"];
    $query = "INSERT INTO items(name, description) VALUES('$name', '$description')";
    if($conn->query($query)) {
        echo json_encode(array("message" => "Item added successfully."));
    } else {
        echo json_encode(array("message" => "Item addition failed."));
    }
}

function update_item($id) {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data["name"];
    $description = $data["description"];
    $query = "UPDATE items SET name='$name', description='$description' WHERE id=$id";
    if($conn->query($query)) {
        echo json_encode(array("message" => "Item updated successfully."));
    } else {
        echo json_encode(array("message" => "Item update failed."));
    }
}

function delete_item($id) {
    global $conn;
    $query = "DELETE FROM items WHERE id=$id";
    if($conn->query($query)) {
        echo json_encode(array("message" => "Item deleted successfully."));
    } else {
        echo json_encode(array("message" => "Item deletion failed."));
    }
}

$conn->close();
