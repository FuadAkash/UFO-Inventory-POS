<?php
    include('auth/connection.php');
    $conn= connect();

    $email= $_POST['email'];

    $sq= "SELECT * FROM users_info WHERE email='$email'";
    $flaggy= $conn->query($sq);

    $returno['success']= false;
    if(mysqli_num_rows($flaggy)>0){
        $returno['success']= true;
    }
    echo json_encode($returno);
?>