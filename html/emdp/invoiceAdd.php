<?php
include_once('includes/auth.php');
include_once('includes/config.php');






include_once('includes/header.php');
?>

<!--  BEGIN CUSTOM STYLE FILE  -->

<!-- Scrollspy Navigation CSS -->
<link href="../src/assets/css/light/scrollspyNav.css" rel="stylesheet" type="text/css" />
<link href="../src/assets/css/dark/scrollspyNav.css" rel="stylesheet" type="text/css" />

<!-- Vanilla SelectBox CSS -->
<link rel="stylesheet" type="text/css" href="../src/plugins/css/light/vanillaSelectBox/custom-vanillaSelectBox.css">
<link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/vanillaSelectBox/custom-vanillaSelectBox.css">
<link rel="stylesheet" type="text/css" href="../src/plugins/src/vanillaSelectBox/vanillaSelectBox.css">

<!-- Flatpickr CSS -->
<link href="../src/plugins/src/flatpickr/flatpickr.css" rel="stylesheet" type="text/css">
<link href="../src/plugins/css/light/flatpickr/custom-flatpickr.css" rel="stylesheet" type="text/css">
<link href="../src/plugins/css/dark/flatpickr/custom-flatpickr.css" rel="stylesheet" type="text/css">

<!-- Invoice Page CSS -->
<link href="../src/assets/css/light/apps/invoice-add.css" rel="stylesheet" type="text/css" />
<link href="../src/assets/css/dark/apps/invoice-add.css" rel="stylesheet" type="text/css" />
<link href="../src/assets/css/light/apps/invoice-preview.css" rel="stylesheet" type="text/css" />
<link href="../src/assets/css/dark/apps/invoice-preview.css" rel="stylesheet" type="text/css" />

<!-- Alert Component CSS -->
<link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
<link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">


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


<!--  END CUSTOM STYLE FILE  -->



<!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="middle-content container-xxl p-0">
                    <!-- BREADCRUMB -->
            <div class="page-meta">
                <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Payments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Invoice</li>
                    </ol>
                </nav>
            </div>
            <!-- /BREADCRUMB -->
                    
                    <div class="row invoice layout-top-spacing layout-spacing">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            
                            <div class="doc-container">
                                    <form id="invoiceForm" method="POST" action="invoiceAddProcess.php">
    <div class="row">
        <div class="col-xl-9">
            <br>
            <div class="invoice-content">
                <div class="invoice-detail-body">
                    <div class="invoice-detail-header">
                        <div class="row justify-content-between">
                            <div class="col-md-3">
                                <div class="form-group mb-4">
                                    <label for="number">Select Supplier</label>
                                    <select class="form-select" name="supplier_id" id="selectSupplier" required>
    <option disabled selected value="">Please Choose</option>
    <?php
    $sql = "SELECT * FROM suppliers";
    $result = mysqli_query($connection, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['supplierID'] . '">' . htmlspecialchars($row['name']) . '</option>';
    }
    ?>
</select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-4">
                                    <label for="due">Due Date</label>
                                    <input type="text" class="form-control form-control-sm" id="due" name="due_date" placeholder="None">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="invoice-detail-items">
                        <div class="table-responsive">
                            <table class="table item-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                    <tr aria-hidden="true" class="mt-3 d-block table-row-hidden"></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="delete-item-row">
                                            <ul class="table-controls">
                                                <li><a href="javascript:void(0);" class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></li>
                                            </ul>
                                        </td>
                                        <td class="description">
                                            <select class="form-select item-select" name="item_id[]" id="itemDropdown" required>
    <option disabled selected value="">Please Choose</option>
    <!-- Items will be loaded here via AJAX -->
</select>
                                        </td>
                                        <td class="rate">
                                            <input type="text" step="0.01" class="form-control form-control-sm item-rate" name="rate[]" placeholder="Price" required>
                                        </td>
                                        <td class="text-right qty">
                                            <input type="text" class="form-control form-control-sm item-qty" name="quantity[]" placeholder="Quantity" value="1" required>
                                        </td>
                                        <td class="text-right amount">
                                            <span class="editable-amount">
                                                <span class="currency">RM</span> 
                                                <span class="amount">0.00</span>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-dark additem">Add Item</button>
                    </div>

                    <div class="invoice-detail-total">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="totals-row">
                                    <div class="invoice-totals-row invoice-summary-subtotal">
                                        <div class="invoice-summary-label">Subtotal</div>
                                        <div class="invoice-summary-value">
                                            <div class="subtotal-amount">
                                                <span class="currency">RM</span>
                                                <span class="amount">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invoice-totals-row invoice-summary-balance-due">
                                        <div class="invoice-summary-label">Total</div>
                                        <div class="invoice-summary-value">
                                            <div class="balance-due-amount">
                                                <span class="currency">RM</span>
                                                <span>0.00</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-success btn-download w-100">Save</button>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


                                
                            </div>
    
                        </div>
                    </div>

                </div>

            </div>

    <?php include_once('includes/footer.php');?>

<!-- END MAIN CONTAINER -->
 <script>
    // Preload item prices
    const itemPrices = {
        <?php
        $sql = "SELECT itemID, price FROM items";
        $result = mysqli_query($connection, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "'{$row['itemID']}': {$row['price']},";
        }
        ?>
    };
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#selectSupplier').on('change', function() {
    var supplierID = $(this).val();

    $.ajax({
        url: 'get_items_by_supplier.php',
        type: 'POST',
        data: { supplierID: supplierID },
        success: function(response) {
            $('#itemDropdown').html(response);
        },
        error: function() {
            alert('Failed to fetch items.');
        }
    });
});
</script>
<script src="../src/plugins/src/vanillaSelectBox/vanillaSelectBox.js"></script>
<script src="../src/plugins/src/vanillaSelectBox/selectboxInvoice.js"></script>

<script src="../src/plugins/src/flatpickr/flatpickr.js"></script>
<script src="../src/assets/js/apps/invoice-add.js"></script>




