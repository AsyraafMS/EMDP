<?php
include_once('includes/auth.php');
include_once('includes/config.php');

// initialize variables
$supplierID = $name = $type = $manufactureDate = $category = $expiryDate = $quantity = $price = $description = $status = "";
$update = false;

if (isset($_POST['submit'])) {
    $supplierID = $_POST['supplier'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $manufactureDate = $_POST['manufactured_date'];
    $expiryDate = $_POST['expired_date'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Convert empty date fields to NULL for SQL
    $manufactureDate = trim($manufactureDate) === "" ? NULL : $manufactureDate;
    $expiryDate = trim($expiryDate) === "" ? NULL : $expiryDate;

    // Sanitize and validate input as needed here

    $query = "INSERT INTO 
        items (supplierID, name, type, category, price, quantity, manufactured_date, expired_date, description, status) 
        VALUES (
            '$supplierID',
            '$name',
            '$type',
            '$category',
            '$price',
            '$quantity',
            " . ($manufactureDate === NULL ? "NULL" : "'$manufactureDate'") . ",
            " . ($expiryDate === NULL ? "NULL" : "'$expiryDate'") . ",
            '$description',
            '$status'
        )";

    $result = mysqli_query($connection, $query);

    if ($result && mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = "Item added successfully!";
    } else {
        $_SESSION['message'] = "Update failed or no changes made. $query";
    }
    header('Location: itemView.php');
    exit();
}
include_once('includes/header.php');
?>

<head>
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="../src/plugins/src/vanillaSelectBox/vanillaSelectBox.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/vanillaSelectBox/custom-vanillaSelectBox.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/vanillaSelectBox/custom-vanillaSelectBox.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <link href="../src/assets/css/light/scrollspyNav.css" rel="stylesheet" type="text/css" />
    <link href="../src/assets/css/dark/scrollspyNav.css" rel="stylesheet" type="text/css" />

    <!--  END CUSTOM STYLE FILE  -->

    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="../src/plugins/src/flatpickr/flatpickr.css" rel="stylesheet" type="text/css">
    <link href="../src/plugins/src/noUiSlider/nouislider.min.css" rel="stylesheet" type="text/css">
    <!-- END THEME GLOBAL STYLES -->

    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="../src/assets/css/light/scrollspyNav.css" rel="stylesheet" type="text/css" />
    <link href="../src/plugins/css/light/flatpickr/custom-flatpickr.css" rel="stylesheet" type="text/css">

    <link href="../src/assets/css/dark/scrollspyNav.css" rel="stylesheet" type="text/css" />
    <link href="../src/plugins/css/dark/flatpickr/custom-flatpickr.css" rel="stylesheet" type="text/css">
    <!--  END CUSTOM STYLE FILE  -->
</head>

<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
    <div class="container">

        <div class="container">

            <!-- BREADCRUMB -->
            <div class="page-meta">
                <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Item</li>
                    </ol>
                </nav>
            </div>
            <!-- /BREADCRUMB -->

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-x close" data-bs-dismiss="alert">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg></button>
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>


            <form class="needs-validation"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php include_once('forms/itemform.php'); ?>
            </form>

        </div>
    </div>


    <?php include_once('includes/footer.php'); ?>

    <!-- BEGIN PAGE LEVEL SCRIPTS -->

    <script src="../src/assets/js/scrollspyNav.js"></script>
    <script src="../src/plugins/src/vanillaSelectBox/vanillaSelectBox.js"></script>
    <script src="../src/plugins/src/input-mask/jquery.inputmask.bundle.min.js"></script>
    <script src="../src/plugins/src/vanillaSelectBox/selectboxItem.js"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../src/assets/js/scrollspyNav.js"></script>
    <script src="../src/plugins/src/flatpickr/flatpickr.js"></script>
    <script src="../src/plugins/src/flatpickr/custom-flatpickr.js"></script>
    
        <!--   <script src="../src/assets/js/forms/bootstrap_validation/itemValidate.js"></script>-->
    <script src="../src/plugins/src/input-mask/input-mask.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const itemTypeSelect = document.getElementById("selectItemType");
            const manufactureDateRow = document.getElementById("manufactureDateRow");
            const expiryDateRow = document.getElementById("expiryDateRow");
            const manufactureInput = document.getElementById("manufactureDate");
            const expiryInput = document.getElementById("expiryDate");

            function toggleDateFields() {
                if (itemTypeSelect.value === "Equipment") {
                    // Hide and set values to "None"
                    manufactureDateRow.style.display = "none";
                    expiryDateRow.style.display = "none";
                    manufactureInput.value = "";
                    expiryInput.value = "";
                    manufactureInput.disabled = true;
                    expiryInput.disabled = true;
                } else {
                    // Show and enable
                    manufactureDateRow.style.display = "";
                    expiryDateRow.style.display = "";
                    if (manufactureInput.value === "None") manufactureInput.value = "";
                    if (expiryInput.value === "None") expiryInput.value = "";
                    manufactureInput.disabled = false;
                    expiryInput.disabled = false;
                }
            }

            // Initial check
            toggleDateFields();

            // Bind change event
            itemTypeSelect.addEventListener("change", toggleDateFields);
        });
        
    </script>
    <script>
function formatPrice(input) {
    let value = input.value;

    // Remove invalid characters (anything except digits and dot)
    value = value.replace(/[^0-9.]/g, '');

    // Allow only one dot
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts[1];
    }

    // Limit to 2 decimal places
    if (parts.length === 2) {
        parts[1] = parts[1].substring(0, 2);
        value = parts[0] + '.' + parts[1];
    }

    // Prevent leading dot (e.g., ".50" → "0.50")
    if (value.startsWith('.')) {
        value = '0' + value;
    }

    // Remove leading zeros (except when "0.xx")
    if (/^0[0-9]/.test(value)) {
        value = value.replace(/^0+/, '');
    }

    input.value = value;

    
}
</script>
<script>
function validateQuantity(input) {
    // Remove non-digits and leading zeros
    let value = input.value.replace(/[^0-9]/g, '');
    
    // Remove leading zeros unless it's just "0"
    value = value.replace(/^0+/, '');
    
    // Prevent empty value from clearing the field
    if (value === '') {
        input.value = '';
    } else {
        input.value = parseInt(value);
    }
}
</script>


   


    <!-- END PAGE LEVEL SCRIPTS -->