<?php
    include_once('includes/auth.php');
    include_once('includes/config.php');
    // initialize variables
    $name = $type = $manufactureDate = $category = $expiryDate = $quantity = $price = $description = $status = "";
    $update= false;

    #edit
	if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        $update = true;
        $record = mysqli_query($connection, "SELECT * FROM items WHERE itemID=$id");

        $n = mysqli_fetch_array($record);
    }

    # Update
if (isset($_POST['update'])) {
    $id = $_POST['itemID'];
    $name = $_POST['name'];
    $type = $_POST['type']; 
    $category = $_POST['category']; 
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $manufactureDate = $_POST['manufactured_date']; 
    $expiryDate = $_POST['expired_date'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Sanitize and validate input as needed here

    $query = "UPDATE items SET 
    name = '$name',
    type = '$type',
    category = '$category',
    price = '$price',
    quantity = '$quantity',
    manufactured_date = " . ($manufactureDate === NULL ? "NULL" : "'$manufactureDate'") . ",
    expired_date = " . ($expiryDate === NULL ? "NULL" : "'$expiryDate'") . ",
    description = '$description',
    status = '$status'
    WHERE itemID = $id";


    $result = mysqli_query($connection, $query);

    if ($result && mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = "Item row successfully updated!";
    } else {
        $_SESSION['message'] = "Update failed or no changes made. ";
    }

    header('location: itemView.php');
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
                                <li class="breadcrumb-item active" aria-current="page">Edit Item</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- /BREADCRUMB -->
                
                    <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button> 
                                <?php
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                ?>
                                </div>
                    <?php endif; ?>


					<form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<?php  include_once('forms/itemForm.php'); ?>
					</form>
                    
                </div>
            </div>

			
			<?php include_once('includes/footer.php');?>

			<!-- BEGIN PAGE LEVEL SCRIPTS -->
            
			<script src="../src/assets/js/scrollspyNav.js"></script>
			<script src="../src/plugins/src/vanillaSelectBox/vanillaSelectBox.js"></script>
			<script src="../src/plugins/src/input-mask/jquery.inputmask.bundle.min.js"></script>
			<script src="../src/plugins/src/vanillaSelectBox/selectboxItem.js"></script>
            <!-- END PAGE LEVEL SCRIPTS -->
            <!-- BEGIN PAGE LEVEL SCRIPTS -->
                <script src="../src/assets/js/scrollspyNav.js"></script>
                <script src="../src/plugins/src/flatpickr/flatpickr.js"></script>
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
            
                <!-- END PAGE LEVEL SCRIPTS -->
			