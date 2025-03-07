<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!file_exists(__DIR__ . '/../controllers/AuthController.php')) {
    die("Error: AuthController.php not found in " . realpath(__DIR__ . '/../controllers/'));
}

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/FlightController.php';
require_once __DIR__ . '/../helpers/Response.php';

header("Content-Type: application/json");

$uri = explode("/", $_SERVER['REQUEST_URI']);
$controller = null;

switch ($uri[1]) {

    case 'ping':
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            print json_encode(["status" => "success" , "message"=> "pong"]);
        }
        break;

    case "register":
      if ($_SERVER['REQUEST_METHOD'] === "POST") {
          $controller = new AuthController();
          $controller->register();
      }
      break;
    case "login":
      if ($_SERVER['REQUEST_METHOD'] === "POST") {
          $controller = new AuthController();
          $controller->login();
      }
      break;
    case "create-flight":
      if ($_SERVER['REQUEST_METHOD'] === "POST") {
          $controller = new FlightController();
          $controller->create();
      }
      break;
    case "flights":
      if ($_SERVER['REQUEST_METHOD'] === "GET") {
          $controller = new FlightController();
          $controller->getAll();
      }
      break;
    case "flight":
      if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($uri[2])) {
          $controller = new FlightController();
          $controller->getById($uri[2]);
      }
      break;
//   case "update-flight":
//       if ($_SERVER['REQUEST_METHOD'] === "PUT" && isset($uri[2])) {
//           $controller = new FlightController();
//           $controller->update($uri[2]);
//       }
//       break;
    case "update-passenger":
      if ($_SERVER['REQUEST_METHOD'] === "PUT" && isset($uri[2])) {
          $controller = new FlightController();
          $controller->updatePassenger($uri[2]);
      }
        break;
//   case "delete-flight":
//       if ($_SERVER['REQUEST_METHOD'] === "DELETE" && isset($uri[2])) {
//           $controller = new FlightController();
//           $controller->delete($uri[2]);
//       }
//       break;
    case "delete-passenger":
      if ($_SERVER['REQUEST_METHOD'] === "DELETE" && isset($uri[2])) {
            $controller = new FlightController();
            $controller->deletePassenger($uri[2]);
      }
        break;
    case "bookings":
     if ($_SERVER['REQUEST_METHOD'] === "GET") {
                $controller = new FlightController();
                $controller->getUserBookings();
            }
        break;
    case "update-booking":
     if ($_SERVER['REQUEST_METHOD'] === "PUT" && isset($uri[2]) && is_numeric($uri[2])) {
            $controller = new FlightController();        
            $controller->updateBooking($uri[2]);
            } else {
                Response::send(400, "Invalid booking ID");
            }
        break;
    case "delete-booking":
        if ($_SERVER['REQUEST_METHOD'] === "DELETE" && isset($uri[2])) {
                $controller = new FlightController();
                $controller->deleteBooking($uri[2]);
            }
            break;
        
    default:
      Response::error(400, "Invalid request");
      break;
}








