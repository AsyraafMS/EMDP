<?php
include_once('includes/auth.php');
include_once('includes/config.php');

$products = [];
$productQuery = "SELECT p.productID,
        p.is_public,
        i.name AS item_name,
        i.category,
        i.price,
        (
            SELECT pm.file_path
            FROM product_media pm
            WHERE pm.productID = p.productID
                AND pm.media_type = 'image'
            ORDER BY pm.is_primary DESC, pm.sort_order ASC, pm.mediaID ASC
            LIMIT 1
        ) AS image_path
    FROM products p
    INNER JOIN items i ON p.itemID = i.itemID
    ORDER BY i.name ASC";
$productResult = mysqli_query($connection, $productQuery);
if ($productResult) {
    while ($row = mysqli_fetch_assoc($productResult)) {
        $products[] = $row;
    }
}

include_once('includes/header.php');
?>
<head>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <!-- END GLOBAL MANDATORY STYLES -->

    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <!--  END CUSTOM STYLE FILE  -->
</head>
<body class="" data-bs-spy="scroll" data-bs-bs-target="#navSection" data-bs-offset="140">
    
    

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">

            <div class="layout-px-spacing">

                <div class="middle-content container-xxl p-0">
    
                    <!-- BREADCRUMB -->
                    <div class="page-meta">
                        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Products</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- /BREADCRUMB -->
    
                    <br><br>
                    
                    <div class="row">
                        <?php if (empty($products)): ?>
                            <div class="col-12">
                                <div class="alert alert-light-danger border-0 mb-4" role="alert">
                                    No products available yet.
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <?php
                                    $imagePath = $product['image_path'] ?? '';
                                    $hasImage = $imagePath !== '';
                                    $fallbackImage = '../src/assets/img/fallback.png';
                                    $imageSrc = $hasImage ? htmlspecialchars($imagePath) : $fallbackImage;
                                    $priceValue = $product['price'] !== null ? number_format((float) $product['price'], 2) : '0.00';
                                    $categoryLabel = $product['category'] ?? '';
                                ?>
                                <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <a class="card style-6" href="productsView.php?id=<?php echo (int) $product['productID']; ?>">
                                        
                                        <img src="<?php echo $imageSrc; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['item_name']); ?>">
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <b><?php echo htmlspecialchars($product['item_name']); ?></b>
                                                </div>
                                                <?php if ($categoryLabel !== ''): ?>
                                                    <div class="col-12 mb-2">
                                                        <span class="text-muted"><?php echo htmlspecialchars($categoryLabel); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-12 text-end">
                                                    <div class="pricing d-flex justify-content-end">
                                                        <p class="text-success mb-0">RM<?php echo $priceValue; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
                
            </div>

            <?php include_once('includes/footer.php');?>
    
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <script src="../src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../src/plugins/src/mousetrap/mousetrap.min.js"></script>
    <script src="../layouts/collapsible-menu/app.js"></script>
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <!-- END PAGE LEVEL SCRIPTS -->    
</body>
</html>
