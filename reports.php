<?php
// reports.php
session_start();
include("config.php"); // Using your existing configuration file

// Check if the form was submitted via the 'Submit Report' button
if (isset($_POST['report'])) {
    
    // 1. Capture form data
    // 'Anonymous' or 'Not provided' act as fallbacks for optional fields
    $name = !empty($_POST['n']) ? mysqli_real_escape_string($conn, $_POST['n']) : "Anonymous";
    $email = !empty($_POST['em']) ? mysqli_real_escape_string($conn, $_POST['em']) : "Not provided";
    
    // These match your textarea 'name' attributes
    $subject = mysqli_real_escape_string($conn, $_POST['issuecateg']);
    $description = mysqli_real_escape_string($conn, $_POST['desc']);

    // 2. Simple validation for required fields
    if (empty($subject) || empty($description)) {
        echo "<script>alert('Please provide both a subject and a description.'); window.history.back();</script>";
        exit();
    }

    // 3. Insert into your 'reports' table
    $query = "INSERT INTO reports (name, email, subject, description) 
              VALUES ('$name', '$email', '$subject', '$description')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Report submitted successfully! We appreciate the feedback.');
                window.location.href = 'index.php';
              </script>";
    } else {
        // Displaying the error if the query fails
        echo "Error: " . mysqli_error($conn);
    }
}
?>