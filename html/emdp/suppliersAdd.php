<?php
include_once('includes/auth.php');
include_once('includes/config.php');

// initialize variables
$name = $phone_num = $email = $address = "";
$update = false;

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $phone_num = $_POST['phoneNum'];
    $email = $_POST['emailAddress'];
    $address = $_POST['address'];

    // Sanitize and validate input as needed here

    $query = "INSERT INTO 
        suppliers (name, phone_num, email, address) 
        VALUES (
            '$name',
            '$phone_num',
            '$email',
            '$address'
        )";

    $result = mysqli_query($connection, $query);

    if ($result && mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = "Supplier added successfully!";
    } else {
        $_SESSION['message'] = "Update failed or no changes made. $query";
    }
    header('Location: suppliersView.php');
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
                        <li class="breadcrumb-item"><a href="#">Payments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Invoice</li>
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


            <form class="suppliers-form"   action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php include_once('forms/suppliersForm.php'); ?>
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
       <!-- <script src="../src/assets/js/forms/bootstrap_validation/supplierValidate.js"></script>-->

    <!-- END PAGE LEVEL SCRIPTS -->