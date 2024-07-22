<?php
// Database connection settings
require "db.php";

// Retrieve form data
$client_name = $_POST['client_name'];
$n_of_posts = $_POST['n_of_posts'];
$n_of_videos = $_POST['n_of_videos'];
$days_of_posting = isset($_POST['days_of_posting']) ? implode(',', $_POST['days_of_posting']) : '';
$hashtags = $_POST['hashtags'];
$start_date = $_POST['start_date'];
$duration = $_POST['duration'];
$language = $_POST['language'];

// Convert start_date to the first day of the next month
$date = new DateTime($start_date);
$date->modify('first day of next month');
$start_date = $date->format('Y-m-d'); // Format for MySQL DATE

// Prepare SQL statement to insert client data
$sql = "INSERT INTO clients (
            client_name, n_of_posts, n_of_videos, days_of_posting, hashtags, start_date, duration, language
        ) VALUES (
            :client_name, :n_of_posts, :n_of_videos, :days_of_posting, :hashtags, :start_date, :duration, :language
        )";

$stmt = $pdo->prepare($sql);

// Bind parameters
$stmt->bindParam(':client_name', $client_name);
$stmt->bindParam(':n_of_posts', $n_of_posts, PDO::PARAM_INT);
$stmt->bindParam(':n_of_videos', $n_of_videos, PDO::PARAM_INT);
$stmt->bindParam(':days_of_posting', $days_of_posting);
$stmt->bindParam(':hashtags', $hashtags);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
$stmt->bindParam(':language', $language);

try {
    $stmt->execute();

    // Insert social media accounts
    $sql = "INSERT INTO social_media (client_name, X_account, instagram_account, linkedin_account, facebook_account, youtube_account, snapchat_account, tiktok_account) 
            VALUES (:client_name, :X_account, :instagram_account, :linkedin_account, :facebook_account, :youtube_account, :snapchat_account, :tiktok_account)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':client_name', $client_name);
    $stmt->bindParam(':X_account', $_POST['X_account']);
    $stmt->bindParam(':instagram_account', $_POST['instagram_account']);
    $stmt->bindParam(':linkedin_account', $_POST['linkedin_account']);
    $stmt->bindParam(':facebook_account', $_POST['facebook_account']);
    $stmt->bindParam(':youtube_account', $_POST['youtube_account']);
    $stmt->bindParam(':snapchat_account', $_POST['snapchat_account']);
    $stmt->bindParam(':tiktok_account', $_POST['tiktok_account']);

    $stmt->execute();

    include "thankyouadmin.html";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
