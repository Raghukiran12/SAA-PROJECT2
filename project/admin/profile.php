<?php
include '../components/connect.php';
require '../vendor/autoload.php'; // Assuming you have the AWS SDK for PHP installed via Composer

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// Check if tutor_id cookie is set
if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location: login.php');
    exit; // Stop script execution after redirect
}

// Reusable function for executing prepared statements
function executeQuery($conn, $sql, $params) {
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit; // Stop script execution on error
    }
}


$bucket = 'saa-project-bucket';
$region = 'us-east-1'; // e.g., 'us-east-1'

// Create an S3 client
$s3 = new S3Client([
    'version'     => 'latest',
    'region'      => $region,
    'credentials' => $credentials,
]);

// Fetch tutor's profile details
$select_profile = executeQuery($conn, "SELECT * FROM `tutors` WHERE id = ?", [$tutor_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// Get total playlists for the tutor
$select_playlists = executeQuery($conn, "SELECT * FROM `playlist` WHERE tutor_id = ?", [$tutor_id]);
$total_playlists = $select_playlists->rowCount();

// Get total contents for the tutor
$select_contents = executeQuery($conn, "SELECT * FROM `content` WHERE tutor_id = ?", [$tutor_id]);
$total_contents = $select_contents->rowCount();

// Get total likes for the tutor
$select_likes = executeQuery($conn, "SELECT * FROM `likes` WHERE tutor_id = ?", [$tutor_id]);
$total_likes = $select_likes->rowCount();

// Get total comments for the tutor
$select_comments = executeQuery($conn, "SELECT * FROM `comments` WHERE tutor_id = ?", [$tutor_id]);
$total_comments = $select_comments->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="tutor-profile" style="min-height: calc(100vh - 19rem);">

    <h1 class="heading">profile details</h1>

    <div class="details">
        <div class="tutor">
            <?php
            // Generate a signed URL for the S3 image
            $imageKey = 'uploaded_files/' . $fetch_profile['image'];
            $imageUrl = $s3->getObjectUrl($bucket, $imageKey, '+10 minutes');
            ?>
            <img src="<?= $imageUrl; ?>" alt="">
            <h3><?= $fetch_profile['name']; ?></h3>
            <span><?= $fetch_profile['profession']; ?></span>
            <a href="update.php" class="inline-btn">update profile</a>
        </div>
        <div class="flex">
            <div class="box">
                <span><?= $total_playlists; ?></span>
                <p>total playlists</p>
                <a href="playlists.php" class="btn">view playlists</a>
            </div>
            <div class="box">
                <span><?= $total_contents; ?></span>
                <p>total videos</p>
                <a href="contents.php" class="btn">view contents</a>
            </div>
            <div class="box">
                <span><?= $total_likes; ?></span>
                <p>total likes</p>
                <a href="contents.php" class="btn">view contents</a>
            </div>
            <div class="box">
                <span><?= $total_comments; ?></span>
                <p>total comments</p>
                <a href="comments.php" class="btn">view comments</a>
            </div>
        </div>
    </div>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
