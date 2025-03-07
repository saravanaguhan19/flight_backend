<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Response.php';

class AuthController {
    public function register() {
        header("Content-Type: application/json");
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            Response::send(400, "Invalid JSON input");
        }

        $user = new User();
        $result = $user->register($input['username'], $input['email'], $input['password']);
        Response::send($result['status'], $result['message'] ,isset($result['token']) ? ["token" => $result['token']] : []);
    }

    public function login() {
        header("Content-Type: application/json");
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            Response::send(400, "Invalid JSON input");
        }

        $user = new User();
        $result = $user->login($input['email'], $input['password']);
        Response::send($result['status'], $result['message'], isset($result['token']) ? ["token" => $result['token']] : []);
    }
}
