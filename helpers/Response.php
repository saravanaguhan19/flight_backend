<?php
class Response {
    public static function send($statusCode, $message, $data = []) {
        http_response_code($statusCode);
        echo json_encode(["status" =>  "success" , "message" => $message, "data" => $data]);
        exit;
    }
    public static function error($statusCode, $message, $data = []) {
        http_response_code($statusCode);
        echo json_encode(["status" =>  "error", "message" => $message, "data" => $data]);
        exit;
    }
}
