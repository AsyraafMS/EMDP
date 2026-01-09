<?php
include_once('includes/auth.php');
    include_once('includes/config.php');

if (isset($_POST['supplierID'])) {
    $supplierID = intval($_POST['supplierID']);
    $sql = "SELECT * FROM items WHERE supplierID = $supplierID";
    $result = mysqli_query($connection, $sql);

    echo '<option disabled selected value="">Please Choose</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['itemID'] . '" data-price="' . $row['price'] . '">' . htmlspecialchars($row['name']) . '</option>';
    }
}
?>
