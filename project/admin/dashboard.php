<?php
include '../components/connect.php';

// Check if tutor_id cookie is set
$tutor_id = isset($_COOKIE['tutor_id']) ? $_COOKIE['tutor_id'] : '';

// Redirect to login page if tutor_id is not set
if (empty($tutor_id)) {
    header('location: login.php');
    exit;
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

// Get total contents for the tutor
$select_contents = executeQuery($conn, "SELECT * FROM `content` WHERE tutor_id = ?", [$tutor_id]);
$total_contents = $select_contents->rowCount();

// Get total playlists for the tutor
$select_playlists = executeQuery($conn, "SELECT * FROM `playlist` WHERE tutor_id = ?", [$tutor_id]);
$total_playlists = $select_playlists->rowCount();

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
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="dashboard">

   <h1 class="heading">dashboard</h1>

   <div class="box-container">

      <div class="box">
         <h3>welcome!</h3>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="profile.php" class="btn">view profile</a>
      </div>

      <div class="box">
         <h3><?= $total_contents; ?></h3>
         <p>total contents</p>
         <a href="add_content.php" class="btn">add new content</a>
      </div>

      <div class="box">
         <h3><?= $total_playlists; ?></h3>
         <p>total playlists</p>
         <a href="add_playlist.php" class="btn">add new playlist</a>
      </div>

      <div class="box">
         <h3><?= $total_likes; ?></h3>
         <p>total likes</p>
         <a href="contents.php" class="btn">view contents</a>
      </div>

      <div class="box">
         <h3><?= $total_comments; ?></h3>
         <p>total comments</p>
         <a href="comments.php" class="btn">view comments</a>
      </div>

      <div class="box">
         <h3>quick select</h3>
         <p>login or register</p>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      </div>

   </div>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>