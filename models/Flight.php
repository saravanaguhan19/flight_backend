<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Response.php';

class Flight {
    private $conn;
    private $table_flight = "flights";
    private $table_passenger = "passengers";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    public function createFlight($data, $userId) {
        try {
            if (!isset($data['origin'], $data['destination'], $data['date'], $data['passengersDetails'])) {
                Response::send(400, "Missing required fields");
            }

            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_flight . " (origin, destination, date, passengers_count, user_id) 
                        VALUES (:origin, :destination, :date, :passengers_count, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':origin' => $data['origin'],
                ':destination' => $data['destination'],
                ':date' => $data['date'],
                ':passengers_count' => count($data['passengersDetails']),
                ':user_id' => $userId  // Link flight to user
            ]);

            $flightId = $this->conn->lastInsertId();

            // Insert passenger details
            $query = "INSERT INTO " . $this->table_passenger . " (flight_id, first_name, last_name, age, gender) 
                        VALUES (:flight_id, :first_name, :last_name, :age, :gender)";
            $stmt = $this->conn->prepare($query);

            foreach ($data['passengersDetails'] as $passenger) {
                $stmt->execute([
                    ':flight_id' => $flightId,
                    ':first_name' => $passenger['firstName'],
                    ':last_name' => $passenger['lastName'],
                    ':age' => $passenger['age'],
                    ':gender' => $passenger['gender']
                ]);
            }

            $this->conn->commit();
            Response::send(201, "Flight booked!", ["flight_id" => $flightId]);
        } catch (Exception $e) {
            $this->conn->rollBack();
            Response::send(500, "Database error: " . $e->getMessage());
        }
    }
    public function getFlights() {
        try {
            $query = "SELECT * FROM " . $this->table_flight;
            $stmt = $this->conn->query($query);
            $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$flights) {
                Response::send(404, "No flights found");
            }

            Response::send(200, "Flights retrieved", $flights);
        } catch (Exception $e) {
            Response::send(500, "Database error: " . $e->getMessage());
        }
    }
    public function getUserBookings($userId) {
        $query = "SELECT flights.*,
                    CONCAT('[', 
                        GROUP_CONCAT(
                            JSON_OBJECT(
                                'id', passengers.id,
                                'firstName', passengers.first_name,
                                'lastName', passengers.last_name,
                                'age', passengers.age,
                                'gender', passengers.gender
                            )
                        ), 
                    ']') AS passengersDetails
                FROM flights
                LEFT JOIN passengers ON passengers.flight_id = flights.id
                WHERE flights.user_id = :userId
                GROUP BY flights.id
                ORDER BY flights.id DESC";  // ✅ Latest bookings first

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ✅ Convert string JSON into actual JSON array
        foreach ($result as &$row) {
            $row['passengersDetails'] = json_decode($row['passengersDetails'], true);
        }

        return $result;
    }
// ✅ Get Flight By ID
    public function getFlightById($flightId, $userId) {
        try {
            $query = "SELECT id, origin, destination, date, passengers_count FROM " . $this->table_flight . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $flightId, ':user_id' => $userId]);
            $flight = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$flight) {
                Response::send(403, "Access denied or flight not found"); // Prevent unauthorized access
            }

            // Get passenger details
            $query = "SELECT id, first_name, last_name, age, gender  FROM " . $this->table_passenger . " WHERE flight_id = :flight_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':flight_id' => $flightId]);
            $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $formattedPassengers = array_map(function ($passenger) {
                return [
                    "id" => $passenger["id"],
                    "firstName" => $passenger['first_name'],
                    "lastName" => $passenger['last_name'],
                    "age" => (int) $passenger['age'],
                    "gender" => $passenger['gender']
                ];
            }, $passengers);

            // Build response
            $response = [
                "origin" => $flight["origin"],
                "destination" => $flight["destination"],
                "date" => $flight["date"],
                "passengersCount" => (int) $flight["passengers_count"],
                "passengersDetails" => $formattedPassengers
            ];

            Response::send(200, "Flight retrieved", $response);
        } catch (Exception $e) {
            Response::send(500, "Database error: " . $e->getMessage());
        }
    }
    // public function updateBooking($bookingId, $data) {
    //     try {
    //         $this->conn->beginTransaction();

    //         // Update flight details
    //         $query = "UPDATE " . $this->table_flight . " SET origin = :origin, destination = :destination, date = :date WHERE id = :id";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->execute([
    //             ":id" => $bookingId,
    //             ":origin" => $data["origin"],
    //             ":destination" => $data["destination"],
    //             ":date" => $data["date"],
    //         ]);

    //         $this->conn->commit();
    //         return ["status" => "success"];
    //     } catch (Exception $e) {
    //         $this->conn->rollBack();
    //         return ["status" => "error", "message" => $e->getMessage()];
    //     }
    // }

    public function updateBooking($id, $data) {
        $query = "UPDATE flights 
                  SET origin = :origin, destination = :destination, date = :date, passengers_count = :passengers_count 
                  WHERE id = :flightId";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":origin", $data["origin"]);
        $stmt->bindParam(":destination", $data["destination"]);
        $stmt->bindParam(":date", $data["date"]);
        $stmt->bindParam(":passengers_count", $data["passengers_count"], PDO::PARAM_INT);
        $stmt->bindParam(":flightId", $id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    


 // ✅ Update passenger details
    // public function updatePassenger($passenger) {
    //     try {
    //         $query = "UPDATE " . $this->table_passenger . " SET first_name = :firstName, last_name = :lastName, age = :age, gender = :gender WHERE id = :id";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->execute([
    //             ":id" => $passenger["id"],
    //             ":firstName" => $passenger["firstName"],
    //             ":lastName" => $passenger["lastName"],
    //             ":age" => $passenger["age"],
    //             ":gender" => $passenger["gender"],
    //         ]);

    //         return ["status" => "success"];
    //     } catch (Exception $e) {
    //         return ["status" => "error", "message" => $e->getMessage()];
    //     }
    // }
    public function updatePassenger($passenger) {
        $query = "UPDATE passengers 
                  SET first_name = :firstName, last_name = :lastName, age = :age, gender = :gender 
                  WHERE id = :passengerId";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":firstName", $passenger["firstName"]);
        $stmt->bindParam(":lastName", $passenger["lastName"]);
        $stmt->bindParam(":age", $passenger["age"], PDO::PARAM_INT);
        $stmt->bindParam(":gender", $passenger["gender"]);
        $stmt->bindParam(":passengerId", $passenger["id"], PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    

    // ✅ Update Specific Passenger Field
    // public function updatePassenger($passengerId, $data, $userId) {
    //     $query = "UPDATE " . $this->table_passenger . " 
    //               SET 
    //               first_name = :first_name ,
    //               last_name = :last_name,
    //               age = :age,
    //               gender = :gender
    //               WHERE id = :id 
    //               AND flight_id IN (SELECT id FROM " . $this->table_flight . " WHERE user_id = :user_id)";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute([
    //         ':id' => $passengerId,
    //         ':first_name' => $data['firstName'],
    //         ':last_name' => $data['lastName'],
    //         ':age' => $data['age'],
    //         ':gender' => $data['gender'],
    //         ':user_id' => $userId
    //     ]);

    //     Response::send(200, "Passenger details updated successfully");
    // }

    // ✅ Delete Specific Passenger
    public function deletePassenger($passengerId, $userId) {
        $query = "DELETE FROM " . $this->table_passenger . " 
                  WHERE id = :id AND flight_id IN (SELECT id FROM " . $this->table_flight . " WHERE user_id = :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $passengerId, ':user_id' => $userId]);
        Response::send(200, "Passenger deleted successfully");
    }
    public function deleteBooking($flightId) {
        try {
            // ✅ Delete passengers first (to prevent foreign key constraint issues)
            $deletePassengersQuery = "DELETE FROM passengers WHERE flight_id = :flightId";
            $deletePassengersStmt = $this->conn->prepare($deletePassengersQuery);
            $deletePassengersStmt->bindParam(":flightId", $flightId, PDO::PARAM_INT);
            $deletePassengersStmt->execute();
    
            // ✅ Now delete the flight
            $deleteFlightQuery = "DELETE FROM flights WHERE id = :flightId";
            $deleteFlightStmt = $this->conn->prepare($deleteFlightQuery);
            $deleteFlightStmt->bindParam(":flightId", $flightId, PDO::PARAM_INT);
    
            return $deleteFlightStmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting booking: " . $e->getMessage());
            return false;
        }
    }
    
}
