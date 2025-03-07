<?php
require_once __DIR__ . '/../models/Flight.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../config/Auth.php';

class FlightController {
  public function create() {
    header("Content-Type: application/json");

    $decodedToken = Auth::verifyToken();
    if (!$decodedToken) {
        Response::error(401, "Unauthorized");
    }

    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) {
        Response::error(400, "Invalid JSON input");
    }

    $userId = $decodedToken->user_id; // Extract user ID from JWT
    $flight = new Flight();
    $flight->createFlight($input, $userId);
  }

  public function getAll() {
    header("Content-Type: application/json");

    $flight = new Flight();
    echo json_encode($flight->getFlights());
  }
  public function getUserBookings() {
    header("Content-Type: application/json");

    $decodedToken = Auth::verifyToken();
    if (!$decodedToken) {
        Response::send(401, "Unauthorized");
    }

    $userId = $decodedToken->user_id; // Extract user ID from JWT
    $flight = new Flight();
    $bookings =$flight->getUserBookings($userId);
    echo json_encode(["status" => "success", "data" => $bookings]);
  }
  // public function updateBooking($id) {
  //   // $headers = apache_request_headers();
  //   $decodedToken = Auth::verifyToken();
  //   $userId = $decodedToken->user_id;
  //   if (!$userId) {
  //       Response::send(401, "Unauthorized");
  //   }


  //   $data = json_decode(file_get_contents("php://input"), true);
  //   $flight = new Flight();
  //    $flight->updateBooking($id, $data);

  //   // Update each passenger
  //   foreach ($data["passengersDetails"] as $passenger) {
    
  //       $flight->updatePassenger($passenger);
  //   }

  //   echo json_encode(["status" => "success", "message" => "Booking updated successfully"]);
  // }


  public function updateBooking($id) {
    // ✅ Verify Token
    $decodedToken = Auth::verifyToken();
    $userId = $decodedToken->user_id ?? null;
    if (!$userId) {
        Response::send(401, "Unauthorized");
        return;
    }

    // ✅ Decode JSON Input
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        Response::send(400, "Invalid JSON input");
        return;
    }

    $flight = new Flight();
    
    // ✅ Update Flight Details
    $updateFlightStatus = $flight->updateBooking($id, $data);
    if (!$updateFlightStatus) {
        Response::send(500, "Failed to update booking details");
        return;
    }

    // ✅ Update Each Passenger (Check if passenger ID exists)
    foreach ($data["passengersDetails"] as $passenger) {
        if (!isset($passenger["id"])) {
            Response::send(400, "Missing passenger ID for update");
            return;
        }
        
        $updatePassengerStatus = $flight->updatePassenger($passenger);
        if (!$updatePassengerStatus) {
            Response::send(500, "Failed to update passenger ID: " . $passenger["id"]);
            return;
        }
    }

    Response::send(200, "Booking updated successfully");
}


  public function getById($flightId) {
    header("Content-Type: application/json");

    $decodedToken = Auth::verifyToken();
    if (!$decodedToken) {
        Response::send(401, "Unauthorized");
    }

    $userId = $decodedToken->user_id; // Extract user ID from JWT
    $flight = new Flight();
    $flight->getFlightById($flightId, $userId);
  }
  public function updatePassenger($passengerId) {
        header("Content-Type: application/json");

        $decodedToken = Auth::verifyToken();
        if (!$decodedToken) {
            Response::send(401, "Unauthorized - Invalid Token");
        }

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            Response::send(400, "Invalid JSON input");
        }

        $userId = $decodedToken->user_id;
        $flight = new Flight();
        $flight->updatePassenger($passengerId, $input, $userId);
  }

  public function deletePassenger($passengerId) {
        header("Content-Type: application/json");

        $decodedToken = Auth::verifyToken();
        if (!$decodedToken) {
            Response::send(401, "Unauthorized - Invalid Token");
        }

        $userId = $decodedToken->user_id;
        $flight = new Flight();
        $flight->deletePassenger($passengerId, $userId);
  }


  public function deleteBooking($id) {
    $decodedToken = Auth::verifyToken();
    $userId = $decodedToken->user_id ?? null;
    if (!$userId) {
        Response::send(401, "Unauthorized");
        return;
    }
    $flight = new Flight();
    if (!$flight->deleteBooking($id)) {
        Response::send(500, "Failed to delete booking");
        return;
    }

    Response::send(200, "Booking deleted successfully");
}

}
