<?php
include('include/db.php');
include('include/encrypt.php');

$alert = '';
$keyMatrixArray = generateMatrix("matkul kripto sangat menyenangkan");

if (isset($_POST['submit'])) {
	// name
	$name_plain = $_POST['name'];
    $formatted_name = formatMessage($name_plain);
    $name = encrypt($keyMatrixArray,$formatted_name);

	// email
	$email_plain = $_POST['email'];
    $formatted_email = formatMessage($email_plain);
    $email = encrypt($keyMatrixArray,$formatted_email);

	// subject
	$subject_plain = $_POST['subject'];
    $formatted_subject = formatMessage($subject_plain);
    $subject = encrypt($keyMatrixArray,$formatted_subject);

	// message
    $message_plain = $_POST['message'];
    $formatted_message = formatMessage($message_plain);
    $message = encrypt($keyMatrixArray,$formatted_message);

    try {
        $query = "INSERT INTO contact (cname, cemail, csubject, cmessage) ";
        $query .= "VALUES('$name','$email','$subject','$message')";
        
        $run = mysqli_query($db, $query);

        if ($run) {
            $alert = '<button class="alert btn-success"><strong>Success! Message Has Been Sent!</strong></button>';
        } else {
            // Provide more details about the database error
            throw new Exception("Database Error: " . mysqli_error($db));
        }
    } catch (Exception $e) {
        $alert = '<button class="alert btn-danger"><strong>Failed! Something Went Wrong!</strong></button>';
        
        // Log the exception details for debugging purposes
        error_log($e->getMessage());
    }

    echo $alert;
}
?>
