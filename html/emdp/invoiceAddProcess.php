<?php
include_once('includes/auth.php');
include_once('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $supplier_id = $_POST['supplier_id'];
    $due_date = $_POST['due_date'];
    $item_ids = $_POST['item_id'];
    $rates = $_POST['rate'];
    $quantities = $_POST['quantity'];
    
    // Calculate total amount
    $total_amount = 0;
    for ($i = 0; $i < count($item_ids); $i++) {
        $total_amount += $rates[$i] * $quantities[$i];
    }
    
    // Insert invoice using logged-in user ID
$user_id = $_SESSION['id']; // or $_SESSION['userID']
$invoice_sql = "INSERT INTO invoices (userID, supplierID, invoice_date, invoice_dueDate, total_amount, status)
                VALUES (?, ?, CURDATE(), ?, ?, 'Pending')";
$stmt = $connection->prepare($invoice_sql);
$stmt->bind_param("iiss", $user_id, $supplier_id, $due_date, $total_amount);
$result = $stmt->execute(); // Store result
$invoice_id = $stmt->insert_id;
$stmt->close();

// Check if insert was successful
if ($result && $invoice_id > 0) {
    $_SESSION['message'] = "Invoice successfully created!";
} else {
    $_SESSION['message'] = "Insert failed.";
}


    
    // Insert invoice items
    for ($i = 0; $i < count($item_ids); $i++) {
        $item_id = $item_ids[$i];
        $quantity = $quantities[$i];
        
        $item_sql = "INSERT INTO invoice_items (invoiceID, itemID, quantity)
                     VALUES (?, ?, ?)";
        $stmt = $connection->prepare($item_sql);
        $stmt->bind_param("iii", $invoice_id, $item_id, $quantity);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect to success page
    header("Location: invoicePreview.php?view=$invoice_id");
    exit;
}
?>