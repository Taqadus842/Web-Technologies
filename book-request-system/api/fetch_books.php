<?php
session_start();
require_once "../config/db.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user']['id'];
$category = $_POST['category'] ?? "";

try {

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM api_logs 
        WHERE user_id=? 
        AND request_time >= NOW() - INTERVAL 1 DAY
    ");

    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();

    if ($count >= 5) {
        echo json_encode(["error" => "Daily limit reached"]);
        exit();
    }

    // Log request
    $pdo->prepare("INSERT INTO api_logs (user_id) VALUES (?)")
        ->execute([$user_id]);

} catch (Exception $e) {
    error_log($e->getMessage());
}

$queryMap = [
    "App Development" => "web development",
    "Mobile Development" => "mobile development",
    "AI" => "artificial intelligence"
];

$query = $queryMap[$category] ?? "programming";

$url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query);

$response = @file_get_contents($url);

if ($response === false) {
    echo json_encode(["error" => "API failed"]);
    exit();
}

$data = json_decode($response, true);

if (!empty($data['items'])) {

    foreach ($data['items'] as $item) {

        $title = $item['volumeInfo']['title'] ?? "Unknown";
        $author = $item['volumeInfo']['authors'][0] ?? "Unknown";

        $check = $pdo->prepare("SELECT id FROM books WHERE title=?");
        $check->execute([$title]);

        if (!$check->fetch()) {
            $insert = $pdo->prepare("
                INSERT INTO books (title, author, category)
                VALUES (?,?,?)
            ");

            $insert->execute([$title, $author, $category]);
        }
    }
}

echo json_encode([
    "success" => true,
    "message" => "Books fetched successfully"
]);