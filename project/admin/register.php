<?php
include '../components/connect.php';
require '../vendor/autoload.php'; // Assuming you have the AWS SDK for PHP installed via Composer

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

if (isset($_POST['submit'])) {
    $id = uniqid();
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $profession = filter_var($_POST['profession'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $cpass = sha1($_POST['cpass']);

    $image = $_FILES['image']['name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = uniqid() . '.' . $ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];

    // Set your AWS S3 bucket details
    $bucket = 'saa-project-bucket';
    $region = 'US East (N. Virginia) us-east-1'; // e.g., 'us-east-1'

    // Create an S3 client
    $s3 = new S3Client([
        'version' => 'latest',
        'region' => $region,
    ]);

    // Use prepared statement to prevent SQL injection
    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
    $select_tutor->execute([$email]);

    if ($select_tutor->rowCount() > 0) {
        $message[] = 'Email already taken!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Confirm password not matched!';
        } else {
            // Use prepared statement to prevent SQL injection
            $insert_tutor = $conn->prepare("INSERT INTO `tutors` (id, name, profession, email, password, image) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_tutor->execute([$id, $name, $profession, $email, $cpass, $rename]);

            try {
                // Upload the file to S3
                $s3->putObject([
                    'Bucket' => $bucket,
                    'Key' => 'uploaded_files/' . $rename,
                    'Body' => fopen($image_tmp_name, 'rb'),
                    'ACL' => 'public-read', // Adjust the ACL as needed
                ]);

                $message[] = 'New tutor registered! Please login now';
            } catch (S3Exception $e) {
                $message[] = 'Error uploading file to S3: ' . $e->getMessage();
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0;">

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message form">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- register section starts  -->

<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>register new</h3>
      <div class="flex">
         <div class="col">
            <p>your name <span>*</span></p>
            <input type="text" name="name" placeholder="eneter your name" maxlength="50" required class="box">
            <p>your profession <span>*</span></p>
            <select name="profession" class="box" required>
               <option value="" disabled selected>-- select your profession</option>
               <option value="developer">developer</option>
               <option value="desginer">desginer</option>
               <option value="musician">musician</option>
               <option value="biologist">biologist</option>
               <option value="teacher">teacher</option>
               <option value="engineer">engineer</option>
               <option value="lawyer">lawyer</option>
               <option value="accountant">accountant</option>
               <option value="doctor">doctor</option>
               <option value="journalist">journalist</option>
               <option value="photographer">photographer</option>
            </select>
            <p>your email <span>*</span></p>
            <input type="email" name="email" placeholder="enter your email" maxlength="20" required class="box">
         </div>
         <div class="col">
            <p>your password <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
            <p>confirm password <span>*</span></p>
            <input type="password" name="cpass" placeholder="confirm your password" maxlength="20" required class="box">
            <p>select pic <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>
      <p class="link">already have an account? <a href="login.php">login now</a></p>
      <input type="submit" name="submit" value="register now" class="btn">
   </form>

</section>

<!-- registe section ends -->












<script>

let darkMode = localStorage.getItem('dark-mode');
let body = document.body;

const enabelDarkMode = () =>{
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

const disableDarkMode = () =>{
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

if(darkMode === 'enabled'){
   enabelDarkMode();
}else{
   disableDarkMode();
}

</script>
   
</body>
</html>