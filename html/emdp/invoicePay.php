<?php
    include_once('includes/auth.php');
    include_once('includes/config.php');

if (isset($_GET['pay'])) {
    $invoiceID = intval($_GET['pay']);

    // Update query
    $sql = "UPDATE invoices SET status = 'Paid' WHERE invoiceID = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $invoiceID);
    $result = $stmt->execute();

    // Check if update was successful
    if ($result && mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = "Invoice successfully Paid!";
    } else {
        $_SESSION['message'] = "Update failed or invoice was already marked as Paid.";
    }

    $stmt->close();
    $connection->close();

    // Redirect back to invoice preview (or wherever appropriate)
    header("Location: invoicePreview.php?view=$invoiceID");
    exit();
}


    ?>