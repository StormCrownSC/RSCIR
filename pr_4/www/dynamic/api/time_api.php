<?php
function openmysqli(): mysqli {
    $connection = new mysqli('mysql', 'user', 'password', 'appDB');
    return $connection;
}
function outputStatus($status, $message)
{
    echo '{status: ' . $status . ', message: "' . $message . '"}';
}
try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            addSubject();
            break;
        case 'DELETE':
            removeSubject();
            break;
        case 'PATCH':
            updateSubjectAuditorium();
            break;
        case 'GET':
            getSubjectByID();
            break;
        default:
            outputStatus(2, 'Invalid Mode');
    }
}
catch (Exception $e) {
    $message = $e->getMessage();
    outputStatus(2, $message);
};

function addSubject() {
    $data = json_decode(file_get_contents('php://input'));
    if (!isset($data['title']) || !isset($data['auditorium'])) {
        throw new Exception("No input provided");
    }
    $mysqli = openMysqli();
    $subTitle = $data['title'];
    $subAuditorium = $data['auditorium'];
    $result = $mysqli->query("SELECT * FROM timetable WHERE title = '{$subTitle}';");
    if ($result->num_rows === 1) {
        $message = 'subject '. $subTitle . ' already exists';
        outputStatus(1, $message);
    } else {
        $query = "INSERT INTO timetable (title, auditorium)
        VALUES ('" . $subTitle . "', '" . $subAuditorium . "');";
        $mysqli->query($query);
        $mysqli->close();
        $message = 'Added subject ' . $subTitle;
        outputStatus(0, $message);
    }
}
function removeSubject()
{
    $data = json_decode(file_get_contents('php://input'), true);
    echo "$data";
    if (!isset($data['title'])) {
        throw new Exception("No input provided");
    }
    $mysqli = openMysqli();
    $subTitle = $data['title'];
    $result = $mysqli->query("SELECT * FROM timetable WHERE title = '{$subTitle}';");
    if ($result->num_rows === 1) {
        $query = "DELETE FROM timetable WHERE title = '" . $subTitle . "';";
        $mysqli->query($query);
        $mysqli->close();
        $message = 'Removed subject ' . $subTitle;
        outputStatus(0, $message);
    } else {
        $message = 'Subject ' . $subTitle . ' does not exist';
        outputStatus(1, $message);
    }
}
function updateSubjectAuditorium()
{
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['title']) || !isset($data['password'])) {
        throw new Exception("No input provided");
    }
    $mysqli = openMysqli();
    $subTitle = $data['title'];
    $subAuditorium = $data['auditorium'];
    $result = $mysqli->query("SELECT * FROM timetable WHERE title = '{$subTitle}';");
    if ($result->num_rows === 1) {
        $query = "UPDATE timetable SET auditorium = '" . $subAuditorium . "' WHERE title = '" . $subTitle . "';";
        $mysqli->query($query);
        $mysqli->close();
        $message = 'Changed auditorium for ' . $subTitle;
        outputStatus(0, $message);
    } else {
        $message = $subTitle . ' does not exist';
        outputStatus(1, $message);
    }
}
function getSubjectByID()
{
    if (!isset($_GET['id'])) {
        throw new Exception("No input provided");
    }
    $mysqli = openMysqli();
    $subID = $_GET['id'];
    $result = $mysqli->query("SELECT * FROM titmetable WHERE ID = '{$subID}';");
    if ($result->num_rows === 1) {
        foreach ($result as $info) {
            echo "{status: 0, title: '" . $info['title'] . "}";
        }
        $mysqli->close();
    } else {
        $message = 'Subject ID '. $subID . ' does not exist';
        outputStatus(1, $message);
    }
}
?>