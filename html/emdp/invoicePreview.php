<?php

include_once('includes/auth.php');
include_once('includes/config.php');

// initialize variables

#view
if (isset($_GET['view'])) {
    $id = $_GET['view'];
    $record = mysqli_query($connection, "SELECT 
  inv.invoiceID,
  inv.invoice_date,
  inv.invoice_dueDate,
  inv.total_amount AS invoice_total,
  inv.status AS invoice_status,
  
  ii.itemID,
  ii.quantity,
  
  i.name AS item_name,
  i.type AS item_type,
  i.category AS item_category,
  i.price AS unit_price,
  (i.price * ii.quantity) AS line_total,
  
  s.name AS supplier_name,
  s.phone_num AS supplier_phone,
  s.email AS supplier_email,
  s.address AS supplier_address,
  
  u.name AS handled_by,
  u.email AS handler_email
FROM invoices inv
JOIN invoice_items ii ON inv.invoiceID = ii.invoiceID
JOIN items i ON ii.itemID = i.itemID
JOIN suppliers s ON inv.supplierID = s.supplierID
JOIN users u ON inv.userID = u.userID
WHERE inv.invoiceID = $id;
");

    $n = mysqli_fetch_array($record);
}

include_once('includes/header.php');
?>

<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="../src/plugins/src/table/datatable/datatables.css">

<link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/dt-global_style.css">
<link href="../src/assets/css/light/apps/invoice-list.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/dt-global_style.css">

<link href="../src/assets/css/light/apps/invoice-preview.css" rel="stylesheet" type="text/css" />
<link href="../src/assets/css/dark/apps/invoice-preview.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">

<!-- END PAGE LEVEL CUSTOM STYLES -->
<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="middle-content container-xxl p-0">

            <div class="row invoice layout-top-spacing layout-spacing">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                    <div class="doc-container">

                    <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button> 
                                <?php
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                ?>
                                </div>
                    <?php endif; ?>

                        <div class="row">

                            <div class="col-xl-9">

                                <div class="invoice-container">
                                    <div class="invoice-inbox">

                                        <div id="ct" class="">

                                            <div class="invoice-00001">
                                                <div class="content-section">

                                                    <div class="inv--head-section inv--detail-section">

                                                        <div class="row">

                                                            <div class="col-sm-6 col-12 mr-auto">
                                                                <div class="d-flex">
                                                                    <img class="company-logo"
                                                                        src="../src/assets/img/logo.svg" alt="company">
                                                                    <h3 class="in-heading align-self-center">FARMASI
                                                                        BIRUNI</h3>
                                                                </div>
                                                                <p class="inv-street-addr mt-3">No. 12, Jalan Universiti
                                                                    2,</p>
                                                                <p class="inv-email-address">biruni@company.com</p>
                                                                <p class="inv-email-address">011-1092 4930</p>
                                                            </div>

                                                            <div class="col-sm-6 text-sm-end">
                                                                <p class="inv-list-number mt-sm-3 pb-sm-2 mt-4"><span
                                                                        class="inv-title">Invoice : </span> <span
                                                                        class="inv-number">#<?php echo htmlspecialchars(str_pad($n['invoiceID'], 5, '0', STR_PAD_LEFT), ENT_QUOTES, 'UTF-8'); ?></span>
                                                                </p>
                                                                <p class="inv-created-date mt-sm-5 mt-3"><span
                                                                        class="inv-title">Invoice Date : </span> <span
                                                                        class="inv-date"><?php echo htmlspecialchars($n['invoice_date'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                                                                <p class="inv-due-date"><span class="inv-title">Due Date
                                                                        : </span> <span class="inv-date"><?php echo htmlspecialchars($n['invoice_dueDate'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="inv--detail-section inv--customer-detail-section">

                                                        <div class="row">
                                                            <div
                                                                class="col-xl-8 col-lg-7 col-md-6 col-sm-4 align-self-center">
                                                                <p class="inv-to">Bill To</p>
                                                            </div>

                                                        </div>
                                                        <div class="col-xl-8 col-lg-7 col-md-6 col-sm-4">
                                                            <p class="inv-customer-name"><?php echo htmlspecialchars($n['supplier_name'], ENT_QUOTES, 'UTF-8'); ?>
                                                            </p>
                                                            <p class="inv-street-addr"><?php echo htmlspecialchars($n['supplier_address'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                            <p class="inv-email-address"><?php echo htmlspecialchars($n['supplier_email'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                            <p class="inv-email-address"><?php echo htmlspecialchars($n['supplier_phone'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                        </div>
                                                    </div>

                                                    <div class="inv--product-table-section">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead class="">
                                                                    <tr>
                                                                        <th scope="col">No.</th>
                                                                        <th scope="col">Items</th>
                                                                        <th class="text-end" scope="col">Qty</th>
                                                                        <th class="text-end" scope="col">Price</th>
                                                                        <th class="text-end" scope="col">Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    if (isset($_GET['view'])) {
                                                                        $id = $_GET['view'];
                                                                        $query = "  SELECT 
                                                                                            ii.*,
                                                                                            i.name AS item_name,
                                                                                            i.price AS item_price,
                                                                                            (i.price * ii.quantity) AS total_amount
                                                                                        FROM 
                                                                                            invoice_items ii
                                                                                        JOIN 
                                                                                            items i ON ii.itemID = i.itemID
                                                                                        WHERE 
                                                                                            ii.invoiceID = ?
                                                                                    ";

                                                                        $stmt = $connection->prepare($query);
                                                                        $stmt->bind_param("i", $id);
                                                                        $stmt->execute();
                                                                        $result = $stmt->get_result();
                                                                    }

                                                                    $sum = 0;
                                                                    $counter = 1;
                                                                    while ($row = $result->fetch_assoc()) {

                                                                        

                                                                        echo "<tr>";
                                                                        echo "<td>" . $counter++ . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                                                        echo "<td class='text-end'>" . $row['quantity'] . "</td>";
                                                                        echo "<td class='text-end'>RM" . number_format($row['item_price'], 2) . "</td>";
                                                                        echo "<td class='text-end'>RM" . number_format($row['total_amount'], 2) . "</td>";
                                                                        echo "</tr>";
                                                                        $sum += $row['total_amount'];
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <div class="inv--total-amounts">

                                                        <div class="row mt-4">
                                                            <div class="col-sm-5 col-12 order-sm-0 order-1">
                                                            </div>
                                                            <div class="col-sm-7 col-12 order-sm-1 order-0">
                                                                <div class="text-sm-end">
                                                                    <div class="row">
                                                                        <div class="col-sm-8 col-7">
                                                                            <p class="">Sub Total :</p>
                                                                        </div>
                                                                        <div class="col-sm-4 col-5">
                                                                            <p class="">RM<?php echo $sum?></p>
                                                                        </div>
                                                                        <div
                                                                            class="col-sm-8 col-7 grand-total-title mt-3">
                                                                            <h4 class="">Grand Total : </h4>
                                                                        </div>
                                                                        <div
                                                                            class="col-sm-4 col-5 grand-total-amount mt-3">
                                                                            <h4 class="">RM<?php echo $sum?></h4>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="inv--note">

                                                        <div class="row mt-4">
                                                            <div class="col-sm-12 col-12 order-sm-0 order-1">
                                                                <p>Note: Thank you for doing business with us.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3">

                                <div class="invoice-actions-btn">

                                    <div class="invoice-action-btn">

                                        <div class="row">

                                            <div class="col-xl-12 col-md-3 col-sm-6">
                                                <a href="javascript:void(0);"
                                                    class="btn btn-secondary btn-print  action-print" onclick="window.print()">Print</a>
                                            </div>
                                            <?php 
if ($n['invoice_status'] == 'Pending') {
    echo '<div class="col-xl-12 col-md-3 col-sm-6">
        <a href="invoicePay.php?pay=' . $n['invoiceID'] . '" class="btn btn-success btn-download">Make Payment</a>
    </div>';
} 
?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
<!--  END FOOTER  -->
</div>
<!--  END CONTENT AREA  -->
</div>

</div>


<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../src/plugins/src/table/datatable/datatables.js"></script>
<script src="../src/plugins/src/table/datatable/button-ext/dataTables.buttons.min.js"></script>
<script src="../src/plugins/src/table/datatable/button-ext/jszip.min.js"></script>
<script src="../src/plugins/src/table/datatable/button-ext/buttons.html5.min.js"></script>
<script src="../src/plugins/src/table/datatable/button-ext/buttons.print.min.js"></script>
<script src="../src/plugins/src/table/datatable/datatables.js"></script>
<script src="../src/plugins/src/table/datatable/button-ext/dataTables.buttons.min.js"></script>
<script src="../src/assets/js/apps/invoice-list.js"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="../src/plugins/src/global/vendors.min.js"></script>
<script src="../src/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="../src/plugins/src/mousetrap/mousetrap.min.js"></script>
<script src="../layouts/collapsible-menu/app.js"></script>
<script src="../src/assets/js/custom.js"></script>

<!-- END GLOBAL MANDATORY SCRIPTS -->
<!-- END MAIN CONTAINER -->