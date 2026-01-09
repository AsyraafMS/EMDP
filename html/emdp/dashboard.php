<?php
include_once('includes/auth.php');
include_once('includes/config.php');

include_once('includes/header.php');

$queryItem = "SELECT count(itemID) As total FROM items";
$executeItem = mysqli_query($connection, $queryItem);
$getItemTotal = mysqli_fetch_assoc($executeItem);
$itemTotal = $getItemTotal['total'];

$queryPendingInvoice = "SELECT count(invoiceID) As total FROM invoices WHERE status='Pending'";
$executePendingInvoice = mysqli_query($connection, $queryPendingInvoice);
$getPendingInvoice = mysqli_fetch_assoc($executePendingInvoice);
$totalPendingInvoice = $getPendingInvoice['total'];

$queryTotalInvoice = "SELECT count(invoiceID) As total FROM invoices";
$executeTotalInvoice = mysqli_query($connection, $queryTotalInvoice);
$getTotalInvoice = mysqli_fetch_assoc($executeTotalInvoice);
$totalTotalInvoice = $getTotalInvoice['total'];


$queryUsers = "SELECT count(userID) As total FROM users";
$executeUsers = mysqli_query($connection, $queryUsers);
$getUsers = mysqli_fetch_assoc($executeUsers);
$totalUsers = $getUsers['total'];


?>

<head>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <link href="../src/assets/css/light/dashboard/dash_1.css" rel="stylesheet" type="text/css" />
    <link href="../src/assets/css/dark/dashboard/dash_1.css" rel="stylesheet" type="text/css" />

    <link href="../src/assets/css/light/dashboard/dash_2.css" rel="stylesheet" type="text/css" />

    <link href="../src/assets/css/dark/dashboard/dash_2.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="../src/plugins/src/table/datatable/datatables.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/dt-global_style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/custom_dt_custom.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/dt-global_style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/custom_dt_custom.css">

    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/custom_dt_miscellaneous.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css">

    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
</head>

<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="middle-content container-xxl p-0">

            <div class="row layout-top-spacing">

                <!--CARD ONE-->
                <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-card-four">
                        <div class="widget-heading">
                            <h5 class="">Inventory Items</h5>
                        </div>
                        <div class="w-content">
                            <div class="w-info">
                                <p class="value"> <?php echo $itemTotal; ?> </p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-users">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>

                </div>


                <!--CARD TWO-->
                <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-card-four">
                        <div class="widget-heading">
                            <h5 class="">Pending Invoice</h5>
                        </div>
                        <div class="w-content ">
                            <div class="w-info ">
                                <p class="value "> <?php echo $totalPendingInvoice; ?></p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-user-check">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>
                <!--CARD THREE  (COL-XL-3) for 4 -->
                <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-card-four">
                        <div class="widget-heading">
                            <h5 class="">Total Invoice</h5>
                        </div>
                        <div class="w-content ">
                            <div class="w-info ">
                                <p class="value "> <?php echo $totalTotalInvoice; ?></p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-user-check">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>
                <!--CARD FOUR-->
                <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-card-four">
                        <div class="widget-heading">
                            <h5 class="">Users</h5>
                        </div>
                        <div class="w-content ">
                            <div class="w-info ">
                                <p class="value "> <?php echo $totalUsers; ?> </p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-user-check">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6 layout-spacing">
                    <div class="widget widget-activity-five">

                        <div class="widget-heading">
                            <h5 class="">Low Stock Alerts</h5>
                        </div>

                        <div class="widget-content">

                            <div class="w-shadow-top"></div>

                            <div class="mt-container mx-auto">
                                <div class="timeline-line">
                                    <?php
                                    $queryLowStock = "SELECT * FROM items WHERE status = 'Low Stock' ";
                                    $executeLowStock = mysqli_query($connection, $queryLowStock);
                                    if (!$executeLowStock) {
                                        die("Invalid query: " . mysqli_error($connection));
                                    }
                                    while ($row = mysqli_fetch_assoc($executeLowStock)) {
                                        $itemName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                                        $itemType = htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8');
                                        $itemQuantity = htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8');
                                        $itemStatus = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');


                                        echo '<div class="item-timeline timeline-new">
                                                <div class="t-dot">
                                                    <div class="t-warning">

<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div>
                                                </div>
                                                <div class="t-content">
                                                    <div class="t-uppercontent">
                                                        <h5> Name: ' . $itemName . '  <br>Quantity: ' . $itemQuantity . ' pcs </h5>
                                                        <span class=""></span>
                                                    </div>
                                                    <p>' . $itemStatus . '</p>
                                                </div>
                                            </div>';
                                    }

                                    ?>
                                </div>
                            </div>

                            <div class="w-shadow-bottom"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-activity-five">

                        <div class="widget-heading">
                            <h5 class="">Out of Stock Alerts</h5>
                        </div>

                        <div class="widget-content">

                            <div class="w-shadow-top"></div>

                            <div class="mt-container mx-auto">
                                <div class="timeline-line">
                                    <?php
                                    $queryLowStock = "SELECT * FROM items WHERE status = 'Out of Stock' ";
                                    $executeLowStock = mysqli_query($connection, $queryLowStock);
                                    if (!$executeLowStock) {
                                        die("Invalid query: " . mysqli_error($connection));
                                    }
                                    while ($row = mysqli_fetch_assoc($executeLowStock)) {
                                        $itemName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                                        $itemType = htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8');
                                        $itemQuantity = htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8');
                                        $itemStatus = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');


                                        echo '<div class="item-timeline timeline-new">
                                                <div class="t-dot">
                                                    <div class="t-danger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>
                                                </div>
                                                <div class="t-content">
                                                    <div class="t-uppercontent">
                                                        <h5> Name: ' . $itemName . '  <br>Quantity: ' . $itemQuantity . ' pcs </h5><br>
                                                     
                                                        <span class=""></span>
                                                    </div>
                                                    <p>' . $itemStatus . '</p>
                                                </div>
                                            </div>';
                                    }

                                    ?>
                                </div>
                            </div>

                            <div class="w-shadow-bottom"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="widget widget-table-two">
    
                                <div class="widget-heading">
                                    <h5 class="">Unpaid Invoices</h5>
                                </div>
    
                                <div class="widget-content">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th><div class="th-content">Supplier</div></th>
                                                    <th><div class="th-content">Invoice</div></th>
                                                    <th><div class="th-content">Due Date</div></th>
                                                    <th><div class="th-content">Price</div></th>
                                                    <th><div class="th-content">Status</div></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
// Assuming you have an active DB connection in $conn

$sql = "
    SELECT 
        i.invoiceID, 
        s.name AS supplier_name, 
        i.total_amount, 
        i.invoice_dueDate,
        i.status
    FROM invoices i
    LEFT JOIN suppliers s ON i.supplierID = s.supplierID
    WHERE i.status = 'Pending'
";


$result = $connection->query($sql);

if ($result->num_rows > 0): 
    while($row = $result->fetch_assoc()):
?>
<tr>
    <td><a href="./invoicePreview.php?view=<?php echo $row['invoiceID'] ?>"><div class="td-content supplier-name"><?php echo htmlspecialchars($row['supplier_name'] ?? 'Unknown'); ?></div></a></td>
    <td><a href="./invoicePreview.php?view=<?php echo $row['invoiceID'] ?>"><div class="td-content invoice-id text-primary text-center">#<?php echo htmlspecialchars(str_pad($row['invoiceID'], 5, '0', STR_PAD_LEFT)); ?></div></a></td>
    <td><a href="./invoicePreview.php?view=<?php echo $row['invoiceID'] ?>"><div class="td-content"><span ><?php echo htmlspecialchars($row['invoice_dueDate']); ?></span></div></a></td>
    <td><a href="./invoicePreview.php?view=<?php echo $row['invoiceID'] ?>"><div class="td-content "><span>RM<?php echo number_format($row['total_amount'], 2); ?></span></div></a></td>
    <td><a href="./invoicePreview.php?view=<?php echo $row['invoiceID'] ?>"><div class="td-content "><span class="badge badge-light-warning"><?php echo htmlspecialchars($row['status']); ?></span></div></a></td>
</tr>
<?php 
    endwhile; 
else: 
?>
<tr><td colspan="4">No invoices found.</td></tr>
<?php endif; ?>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                <div class="col-lg-12">
                    <div class="statbox  box box-shadow">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>All Inventory Stock</h4>
                                </div>
                            </div>
                        </div>


                        <div class="widget-content widget-content-area">
                            <table id="medicineTable" class="table style-3 dt-table-hover">
                                <thead>

                                    <tr>
                                        <th class="checkbox-column text-center"> No. </th>
                                        <th>Item Name</th>
                                        <th>Item Type</th>
                                        <th>Price (RM)</th>
                                        <th>Quantity</th>
                                        <th>Manufactured Date</th>
                                        <th>Expiry Date</th>


                                        <!--<th>Description</th>-->
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Tracker</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    //Read all row from database table
                                    $sql = "SELECT * FROM items";
                                    $result = $connection->query($sql);

                                    if (!$result) {
                                        die("Invalid query: " . $connection->error);
                                    }
                                    //Num. of rows
                                    $i = 1;

                                    //Read data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        if (strcasecmp('In Stock', $row['status']) == 0) {
                                            $badge = 'light-success';
                                        } elseif (strcasecmp('Out Of Stock', $row['status']) == 0) {
                                            $badge = 'light-danger';
                                        } else {
                                            $badge = 'light-warning';
                                        }

                                        if (empty($row['expired_date'])) {
                                            $badge2 = '';
                                            $tracker = null;
                                        } elseif (strtotime($row['expired_date']) < strtotime(date('Y-m-d'))) {
                                            $badge2 = 'light-danger';
                                            $tracker = "Expired";
                                        } elseif (strtotime($row['expired_date']) < strtotime('+10 days')) {
                                            $badge2 = 'light-warning';
                                            $tracker = "Expiring Soon";
                                        } else {
                                            $badge2 = 'light-success';
                                            $tracker = "Expires Later";
                                        }



                                        echo '<tr>
                                                <td class="checkbox-column text-center"> ' . $i . ' </td>
                                                <td>' . $row['name'] . '</td>
                                                <td>' . $row['type'] . '</td>
                                                <td>' . $row['price'] . '</td>
                                                <td>' . $row['quantity'] . '</td>
                                                <td>' . $row['manufactured_date'] . '</td>
                                                <td>' . $row['expired_date'] . '</td>
                                                <td class="text-center"><span class="shadow-none badge badge-' . $badge . '">' . $row['status'] . '</span></td>
                                                <td class="text-center"><span class="shadow-none badge badge-' . $badge2 . '">' . $tracker . '</span></td>
                                            </tr>';
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once('includes/footer.php'); ?>
            <!--  END CONTENT AREA  -->


            <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
            <script src="../src/plugins/src/apex/apexcharts.min.js"></script>
            <script src="../src/assets/js/dashboard/dash_1.js"></script>
            <script src="../src/assets/js/dashboard/dash_2.js"></script>
            <script src="../src/assets/js/dashboard/dashboard.js"></script>

            <script src="../src/plugins/src/table/datatable/datatables.js"></script>
            <script src="../src/plugins/src/table/datatable/button-ext/dataTables.buttons.min.js"></script>
            <script src="../src/plugins/src/table/datatable/button-ext/jszip.min.js"></script>
            <script src="../src/plugins/src/table/datatable/button-ext/buttons.html5.min.js"></script>
            <script src="../src/plugins/src/table/datatable/button-ext/buttons.print.min.js"></script>
            <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

            <script>

                c3 = $('#medicineTable').DataTable({
                    "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
                        "<'dt--top-section'<'col-4 col-sm-6 d-flex justify-content-sm-start justify-content-center'l>>" +
                        "<'table-responsive'tr>" +
                        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                    buttons: {
                        buttons: [
                            { extend: 'copy', className: 'btn' },
                            { extend: 'csv', className: 'btn' },
                            { extend: 'excel', className: 'btn' },
                            { extend: 'print', className: 'btn' }
                        ]
                    },
                    "oLanguage": {
                        "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                        "sInfo": "Showing page _PAGE_ of _PAGES_",
                        "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                        "sSearchPlaceholder": "Search...",
                        "sLengthMenu": "Results :  _MENU_",
                    },
                    "stripeClasses": [],
                    "lengthMenu": [5, 10, 20, 50, 100],
                    "pageLength": 10
                });
                multiCheck(c3);
            </script>