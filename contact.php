<?php

define("JSON_PATH", "contact.json");

function abort($message) {
    die(json_encode([
        "status" => false,
        "message" => $message
    ], JSON_UNESCAPED_UNICODE));
}

function succeed($message) {
    die(json_encode([
        "status" => true,
        "message" => $message
    ], JSON_UNESCAPED_UNICODE));
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") abort("Contact.php only accepts requests using POST method");

$name    = $_POST["name"];
$city    = $_POST["city"];
$email   = $_POST["email"];
$phone   = $_POST["phone"];
$message = $_POST["message"];

if (!isset($name) || !isset($city) || !isset($email) ||  !isset($phone) || !isset($message)) abort("Please fill in every input");

$jsonString = file_get_contents(JSON_PATH);
$json       = json_decode($jsonString, true, 2048); 

$json[]     = [
    "name"    => $name,
    "city"    => $city,
    "email"   => $email,
    "phone" => $phone,
    "message" => $message,
    "time"    => time() // store time as unix value so the format can be changed later
];

$jsonString = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$pieces = str_split($jsonString, 512 * 1024); // I guess 512KB per loop is the best size (tested for speed)

$handle = fopen(JSON_PATH, "w");

while ($pieces) {
    fwrite($handle, $pieces[0], strlen($pieces[0]));
    array_shift($pieces);
}

succeed("Email has been successfully sent");