<?php
include_once('includes/auth.php');
include_once('includes/config.php');

$feedback = null;
$feedbackType = 'success';
$pageMessage = '';
$selectedItemID = 0;
$descriptionHtml = '';
$isPublic = 1;
$existingMedia = [];

$canEdit = isset($_SESSION['acctype']) && in_array($_SESSION['acctype'], ['Superuser', 'Admin'], true);
$productID = isset($_GET['id']) ? (int) $_GET['id'] : 0;

function deleteProductMedia($connection, $productID, $mediaID, &$errorMessage)
{
    $errorMessage = '';
    $mediaQuery = $connection->prepare('SELECT file_path, is_primary FROM product_media WHERE mediaID = ? AND productID = ?');
    if (!$mediaQuery) {
        $errorMessage = 'Failed to load media.';
        return false;
    }
    $mediaQuery->bind_param('ii', $mediaID, $productID);
    $mediaQuery->execute();
    $mediaResult = $mediaQuery->get_result();
    $mediaRow = $mediaResult ? $mediaResult->fetch_assoc() : null;
    $mediaQuery->close();
    if (!$mediaRow) {
        $errorMessage = 'Media not found.';
        return false;
    }
    $filePath = $mediaRow['file_path'] ?? '';
    $wasPrimary = (int) ($mediaRow['is_primary'] ?? 0);

    $deleteStmt = $connection->prepare('DELETE FROM product_media WHERE mediaID = ? AND productID = ?');
    if (!$deleteStmt) {
        $errorMessage = 'Failed to delete media.';
        return false;
    }
    $deleteStmt->bind_param('ii', $mediaID, $productID);
    $deleteStmt->execute();
    $deleteStmt->close();

    if (is_string($filePath) && strpos($filePath, 'media/') === 0) {
        $fullPath = __DIR__ . DIRECTORY_SEPARATOR . $filePath;
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    if ($wasPrimary === 1) {
        $newPrimary = mysqli_query($connection, "SELECT mediaID FROM product_media WHERE productID = {$productID} ORDER BY sort_order ASC, mediaID ASC LIMIT 1");
        if ($newPrimary && ($primaryRow = mysqli_fetch_assoc($newPrimary))) {
            $primaryID = (int) $primaryRow['mediaID'];
            mysqli_query($connection, "UPDATE product_media SET is_primary = 1 WHERE mediaID = {$primaryID}");
        }
    }

    return true;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete-media') {
    header('Content-Type: application/json');
    if (!$canEdit) {
        echo json_encode(['ok' => false, 'message' => 'Not authorized.']);
        exit;
    }

    $mediaID = isset($_POST['media_id']) ? (int) $_POST['media_id'] : 0;
    $requestProductID = isset($_POST['product_id']) ? (int) $_POST['product_id'] : $productID;

    if ($mediaID <= 0 || $requestProductID <= 0) {
        echo json_encode(['ok' => false, 'message' => 'Invalid request.']);
        exit;
    }

    $deleteError = '';
    if (deleteProductMedia($connection, $requestProductID, $mediaID, $deleteError)) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'message' => $deleteError]);
    }
    exit;
}

if (!$canEdit) {
    $pageMessage = 'You do not have permission to edit products.';
} elseif ($productID <= 0) {
    $pageMessage = 'No product selected.';
} else {
    $productQuery = "SELECT p.productID, p.itemID, p.description, p.is_public FROM products p WHERE p.productID = {$productID} LIMIT 1";
    $productResult = mysqli_query($connection, $productQuery);
    if ($productResult && mysqli_num_rows($productResult) > 0) {
        $product = mysqli_fetch_assoc($productResult);
        $selectedItemID = (int) $product['itemID'];
        $descriptionHtml = $product['description'] ?? '';
        $isPublic = (int) $product['is_public'] === 1 ? 1 : 0;

    } else {
        $pageMessage = 'Product not found.';
    }
}

if ($pageMessage === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = isset($_POST['productID']) ? (int) $_POST['productID'] : 0;
    $deleteMediaID = isset($_POST['delete_media_id']) ? (int) $_POST['delete_media_id'] : 0;

    if ($deleteMediaID > 0 && $productID > 0) {
        $deleteError = '';
        if (deleteProductMedia($connection, $productID, $deleteMediaID, $deleteError)) {
            $feedback = 'Media deleted successfully.';
            $feedbackType = 'success';
        } else {
            $feedback = $deleteError !== '' ? $deleteError : 'Media not found.';
            $feedbackType = 'danger';
        }
    } else {
        $selectedItemID = isset($_POST['itemID']) ? (int) $_POST['itemID'] : 0;
        $descriptionHtml = trim($_POST['description'] ?? '');
        $isPublic = (isset($_POST['is_public']) && $_POST['is_public'] === '1') ? 1 : 0;

        if ($productID <= 0 || $selectedItemID <= 0) {
            $feedback = 'Please select a valid item.';
            $feedbackType = 'danger';
        } else {
            $checkItem = $connection->prepare('SELECT itemID FROM items WHERE itemID = ?');
            if ($checkItem) {
                $checkItem->bind_param('i', $selectedItemID);
                $checkItem->execute();
                $checkItem->store_result();
                $itemExists = $checkItem->num_rows > 0;
                $checkItem->close();
            } else {
                $itemExists = false;
            }

            if (!$itemExists) {
                $feedback = 'Selected item was not found.';
                $feedbackType = 'danger';
            } else {
                $checkDuplicate = $connection->prepare('SELECT productID FROM products WHERE itemID = ? AND productID <> ?');
                if ($checkDuplicate) {
                    $checkDuplicate->bind_param('ii', $selectedItemID, $productID);
                    $checkDuplicate->execute();
                    $checkDuplicate->store_result();
                    $duplicateExists = $checkDuplicate->num_rows > 0;
                    $checkDuplicate->close();
                } else {
                    $duplicateExists = false;
                }

                if ($duplicateExists) {
                    $feedback = 'This item already has another product listing.';
                    $feedbackType = 'danger';
                } else {
                    $updateProduct = $connection->prepare('UPDATE products SET itemID = ?, description = ?, is_public = ?, updated_at = NOW() WHERE productID = ?');
                    if ($updateProduct) {
                        $descriptionValue = $descriptionHtml !== '' ? $descriptionHtml : null;
                        $updateProduct->bind_param('isii', $selectedItemID, $descriptionValue, $isPublic, $productID);
                        $updateOk = $updateProduct->execute();
                        $updateProduct->close();
                    } else {
                        $updateOk = false;
                    }

                    if ($updateOk) {
                        $uploadIssues = [];
                        $mediaCount = 0;

                        if (!empty($_FILES['media']) && is_array($_FILES['media']['name'])) {
                            $mediaDir = __DIR__ . DIRECTORY_SEPARATOR . 'media';
                            if (!is_dir($mediaDir)) {
                                mkdir($mediaDir, 0755, true);
                            }

                            $maxSizeBytes = 50 * 1024 * 1024;
                            $allowedImageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            $allowedVideoExt = ['mp4', 'webm', 'ogg', 'mov'];
                            $finfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;

                            $uploadErrorMessages = [
                                UPLOAD_ERR_INI_SIZE => 'exceeds the upload_max_filesize limit.',
                                UPLOAD_ERR_FORM_SIZE => 'exceeds the MAX_FILE_SIZE limit.',
                                UPLOAD_ERR_PARTIAL => 'was only partially uploaded.',
                                UPLOAD_ERR_NO_FILE => 'was not uploaded.',
                                UPLOAD_ERR_NO_TMP_DIR => 'is missing a temporary folder.',
                                UPLOAD_ERR_CANT_WRITE => 'failed to write to disk.',
                                UPLOAD_ERR_EXTENSION => 'was blocked by a PHP extension.'
                            ];

                            $existingPrimary = 0;
                            $primaryResult = mysqli_query($connection, "SELECT COUNT(*) AS total FROM product_media WHERE productID = {$productID} AND is_primary = 1");
                            if ($primaryResult) {
                                $primaryRow = mysqli_fetch_assoc($primaryResult);
                                $existingPrimary = (int) ($primaryRow['total'] ?? 0);
                            }

                            $maxSort = 0;
                            $sortResult = mysqli_query($connection, "SELECT COALESCE(MAX(sort_order), 0) AS max_sort FROM product_media WHERE productID = {$productID}");
                            if ($sortResult) {
                                $sortRow = mysqli_fetch_assoc($sortResult);
                                $maxSort = (int) ($sortRow['max_sort'] ?? 0);
                            }

                            $mediaInsert = $connection->prepare('INSERT INTO product_media (productID, media_type, file_path, sort_order, is_primary, created_at) VALUES (?, ?, ?, ?, ?, NOW())');

                            $fileCount = count($_FILES['media']['name']);
                            for ($i = 0; $i < $fileCount; $i++) {
                                if ($_FILES['media']['error'][$i] !== UPLOAD_ERR_OK) {
                                    $errorCode = $_FILES['media']['error'][$i];
                                    $errorText = $uploadErrorMessages[$errorCode] ?? 'failed to upload.';
                                    $uploadIssues[] = $_FILES['media']['name'][$i] . ' ' . $errorText;
                                    continue;
                                }

                                if ($_FILES['media']['size'][$i] > $maxSizeBytes) {
                                    $uploadIssues[] = $_FILES['media']['name'][$i] . ' exceeds 50MB.';
                                    continue;
                                }

                                $originalName = $_FILES['media']['name'][$i];
                                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                                $tmpName = $_FILES['media']['tmp_name'][$i];
                                $mimeType = $finfo ? $finfo->file($tmpName) : (function_exists('mime_content_type') ? mime_content_type($tmpName) : '');

                                $mediaType = null;
                                if (is_string($mimeType) && strpos($mimeType, 'image/') === 0) {
                                    $mediaType = 'image';
                                } elseif (is_string($mimeType) && strpos($mimeType, 'video/') === 0) {
                                    $mediaType = 'video';
                                } elseif (in_array($extension, $allowedImageExt, true)) {
                                    $mediaType = 'image';
                                } elseif (in_array($extension, $allowedVideoExt, true)) {
                                    $mediaType = 'video';
                                }

                                if ($mediaType === null) {
                                    $uploadIssues[] = $originalName . ' has an unsupported file type.';
                                    continue;
                                }

                                $safeName = uniqid('media_', true) . '.' . $extension;
                                $targetPath = $mediaDir . DIRECTORY_SEPARATOR . $safeName;
                                if (!move_uploaded_file($tmpName, $targetPath)) {
                                    $uploadIssues[] = 'Failed to upload ' . $originalName . '.';
                                    continue;
                                }

                                if ($mediaInsert) {
                                    $relativePath = 'media/' . $safeName;
                                    $sortOrder = $maxSort + $mediaCount + 1;
                                    $isPrimaryFlag = ($existingPrimary === 0 && $mediaCount === 0) ? 1 : 0;
                                    $mediaInsert->bind_param('issii', $productID, $mediaType, $relativePath, $sortOrder, $isPrimaryFlag);
                                    $mediaInsert->execute();
                                }

                                $mediaCount++;
                            }

                            if ($mediaInsert) {
                                $mediaInsert->close();
                            }
                        }

                        if (!empty($uploadIssues)) {
                            $feedback = 'Product updated, but some uploads failed: ' . implode(' ', $uploadIssues);
                            $feedbackType = 'danger';
                        } elseif (empty($_FILES['media']) || !isset($_FILES['media']['name'])) {
                            $feedback = 'Product updated, but no files were received by the server.';
                            $feedbackType = 'danger';
                        } elseif (!empty($_FILES['media']) && is_array($_FILES['media']['name']) && $mediaCount === 0) {
                            $feedback = 'Product updated, but no files were uploaded.';
                            $feedbackType = 'danger';
                        } else {
                            $feedback = 'Product updated successfully.';
                            $feedbackType = 'success';
                        }
                    } else {
                        $feedback = 'Failed to update product.';
                        $feedbackType = 'danger';
                    }
                }
            }
        }
    }
}

$existingMedia = [];
if ($pageMessage === '' && $productID > 0) {
    $mediaResult = mysqli_query($connection, "SELECT mediaID, media_type, file_path, is_primary FROM product_media WHERE productID = {$productID} ORDER BY is_primary DESC, sort_order ASC, mediaID ASC");
    if ($mediaResult) {
        while ($row = mysqli_fetch_assoc($mediaResult)) {
            $existingMedia[] = $row;
        }
    }
}

$items = [];
$itemQuery = "SELECT i.itemID, i.name AS item_name, i.category, i.quantity, s.name AS supplier_name, s.phone_num, s.email
    FROM items i
    LEFT JOIN suppliers s ON i.supplierID = s.supplierID
    ORDER BY i.name ASC";
$itemResult = mysqli_query($connection, $itemQuery);
if ($itemResult) {
    while ($row = mysqli_fetch_assoc($itemResult)) {
        $items[] = $row;
    }
}

include_once('includes/header.php');
?>
<head>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link rel="stylesheet" href="../src/plugins/src/filepond/filepond.min.css">
    <link rel="stylesheet" href="../src/plugins/src/filepond/FilePondPluginImagePreview.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/src/tagify/tagify.css">

    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/forms/switches.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/tagify/custom-tagify.css">
    <link href="../src/plugins/css/light/filepond/custom-filepond.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/forms/switches.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/tagify/custom-tagify.css">
    <link href="../src/plugins/css/dark/filepond/custom-filepond.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <link rel="stylesheet" href="../src/assets/css/light/apps/ecommerce-create.css">
    <link rel="stylesheet" href="../src/assets/css/dark/apps/ecommerce-create.css">
    <!--  END CUSTOM STYLE FILE  -->
</head>
<body class="">

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">

            <div class="layout-px-spacing">

                <div class="middle-content container-xxl p-0">

                    <!-- BREADCRUMB -->
                    <div class="page-meta">
                        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Products</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- /BREADCRUMB -->

                    <?php if ($pageMessage !== ''): ?>
                        <div class="alert alert-light-danger border-0 mb-4" role="alert">
                            <?php echo htmlspecialchars($pageMessage); ?>
                        </div>
                    <?php else: ?>
                        <?php if ($feedback !== null): ?>
                            <div class="alert alert-light-<?php echo htmlspecialchars($feedbackType); ?> alert-dismissible fade show border-0 mb-4" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                                <?php echo htmlspecialchars($feedback); ?>
                            </div>
                        <?php endif; ?>

                    <form id="product-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?id=<?php echo (int) $productID; ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="productID" value="<?php echo (int) $productID; ?>">
                    <div class="row mb-4 layout-spacing layout-top-spacing">

                        <div class="col-xxl-9 col-xl-12 col-lg-12 col-md-12 col-sm-12">

                            <div class="widget-content widget-content-area ecommerce-create-section">

                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <label for="itemID">Product Name</label>
                                        <input type="text" class="form-control mb-2" id="item-search" placeholder="Search items...">
                                        <select class="form-select" id="itemID" name="itemID" required>
                                            <option value="">Choose...</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?php echo (int) $item['itemID']; ?>"
                                                    data-supplier-name="<?php echo htmlspecialchars($item['supplier_name'] ?? ''); ?>"
                                                    data-category="<?php echo htmlspecialchars($item['category'] ?? ''); ?>"
                                                    data-stock="<?php echo htmlspecialchars((string) ($item['quantity'] ?? '')); ?>"
                                                    <?php echo ($selectedItemID === (int) $item['itemID']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <label>Description</label>
                                        <div id="product-description"><?php echo $descriptionHtml; ?></div>
                                        <input type="hidden" name="description" id="description-input" value="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="product-images">Upload Images / Video</label>
                                        <div class="multiple-file-upload">
                                            <input type="file"
                                                class="filepond file-upload-multiple"
                                                name="media[]"
                                                id="product-images"
                                                multiple
                                                accept="image/*,video/*"
                                                data-allow-reorder="true"
                                                data-instant-upload="false"
                                                data-store-as-file="true"
                                                data-max-file-size="50MB"
                                                data-max-files="10">
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="switch form-switch-custom switch-inline form-switch-primary mt-4">
                                            <input type="hidden" name="is_public" value="0">
                                            <input class="switch-input" type="checkbox" role="switch" id="showPublicly" name="is_public" value="1" <?php echo $isPublic === 1 ? 'checked' : ''; ?>>
                                            
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-xxl-3 col-xl-12 col-lg-12 col-md-12 col-sm-12">

                            <div class="row">
                                <div class="col-xxl-12 col-xl-8 col-lg-8 col-md-7 mt-xxl-0 mt-4">
                                    <div class="widget-content widget-content-area ecommerce-create-section">
                                        <div class="row">
                                            <div class="col-sm-12 mb-4">
                                                <label for="supplier-name">Supplier Name</label>
                                                <input readonly type="text" class="form-control" id="supplier-name" value="">
                                            </div>
                                            <div class="col-sm-12 mb-4">
                                                <label for="item-category">Category</label>
                                                <input readonly type="text" class="form-control" id="item-category" value="">
                                            </div>
                                            <div class="col-sm-12 mb-4">
                                                <label for="current-stock">Current Stock</label>
                                                <input readonly type="text" class="form-control" id="current-stock" value="">
                                            </div>
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-success w-100">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php include_once('includes/footer.php');?>

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../src/plugins/src/editors/quill/quill.js"></script>
    <script src="../src/plugins/src/filepond/filepond.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginFileValidateType.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageExifOrientation.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImagePreview.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageCrop.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageResize.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageTransform.min.js"></script>
    <script src="../src/plugins/src/filepond/filepondPluginFileValidateSize.min.js"></script>

    <script src="../src/plugins/src/tagify/tagify.min.js"></script>

    <script src="../src/assets/js/apps/ecommerce-create.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var itemSelect = document.getElementById('itemID');
            var searchInput = document.getElementById('item-search');
            var supplierName = document.getElementById('supplier-name');
            var itemCategory = document.getElementById('item-category');
            var itemStock = document.getElementById('current-stock');
            var form = document.getElementById('product-form');
            var descriptionInput = document.getElementById('description-input');

            function updateItemDetails() {
                var selectedOption = itemSelect.options[itemSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    supplierName.value = '';
                    itemCategory.value = '';
                    itemStock.value = '';
                    return;
                }

                supplierName.value = selectedOption.getAttribute('data-supplier-name') || '';
                itemCategory.value = selectedOption.getAttribute('data-category') || '';
                itemStock.value = selectedOption.getAttribute('data-stock') || '';
            }

            function filterItems() {
                var term = searchInput.value.trim().toLowerCase();
                Array.prototype.forEach.call(itemSelect.options, function (option) {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }
                    option.hidden = term !== '' && option.textContent.toLowerCase().indexOf(term) === -1;
                });

                if (itemSelect.selectedOptions.length && itemSelect.selectedOptions[0].hidden) {
                    itemSelect.selectedIndex = 0;
                    updateItemDetails();
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterItems);
            }
            if (itemSelect) {
                itemSelect.addEventListener('change', updateItemDetails);
                updateItemDetails();
            }

            if (form) {
                form.addEventListener('submit', function () {
                    if (typeof quill !== 'undefined') {
                        descriptionInput.value = quill.root.innerHTML.trim();
                    }
                    if (typeof ecommerce !== 'undefined') {
                        var pondFiles = ecommerce.getFiles();
                        var dataTransfer = new DataTransfer();
                        pondFiles.forEach(function (fileItem) {
                            if (fileItem && fileItem.getMetadata && fileItem.getMetadata('mediaID')) {
                                return;
                            }
                            var fileObject = null;
                            if (fileItem && typeof fileItem.getFile === 'function') {
                                fileObject = fileItem.getFile();
                            } else if (fileItem && fileItem.file) {
                                fileObject = fileItem.file;
                            }
                            if (fileObject instanceof Blob) {
                                if (!(fileObject instanceof File)) {
                                    var fallbackName = (fileItem && (fileItem.filename || (fileItem.file && fileItem.file.name))) || 'upload';
                                    try {
                                        fileObject = new File([fileObject], fallbackName, { type: fileObject.type || 'application/octet-stream' });
                                    } catch (err) {
                                    }
                                }
                                dataTransfer.items.add(fileObject);
                            }
                        });
                        var fileInput = document.querySelector('input.file-upload-multiple');
                        if (fileInput) {
                            fileInput.files = dataTransfer.files;
                        }
                    }
                });
            }

            if (typeof ecommerce !== 'undefined') {
                ecommerce.setOptions({
                    server: {
                        load: function (source, load, error, progress, abort) {
                            fetch(source)
                                .then(function (response) { return response.blob(); })
                                .then(function (blob) { load(blob); })
                                .catch(function () { error('Failed to load file.'); });

                            return {
                                abort: function () {
                                    abort();
                                }
                            };
                        }
                    }
                });

                var existingFiles = <?php echo json_encode(array_map(function ($media) {
                    $filePath = $media['file_path'];
                    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . $filePath;
                    $fileSize = 0;
                    $fileType = '';
                    if (is_file($fullPath)) {
                        $fileSize = filesize($fullPath);
                        if (function_exists('mime_content_type')) {
                            $fileType = mime_content_type($fullPath);
                        }
                    }
                    return [
                        'source' => $filePath,
                        'options' => [
                            'type' => 'local',
                            'file' => [
                                'name' => basename($filePath),
                                'size' => $fileSize,
                                'type' => $fileType
                            ],
                            'metadata' => [
                                'mediaID' => (int) $media['mediaID']
                            ]
                        ]
                    ];
                }, $existingMedia)); ?>;
                if (Array.isArray(existingFiles) && existingFiles.length > 0) {
                    ecommerce.setOptions({ files: existingFiles });
                }

                ecommerce.on('removefile', function (error, file) {
                    if (error) {
                        return;
                    }
                    var mediaID = file.getMetadata('mediaID');
                    if (!mediaID) {
                        return;
                    }
                    var formData = new FormData();
                    formData.append('media_id', mediaID);
                    formData.append('product_id', <?php echo (int) $productID; ?>);

                    fetch('productsEdit.php?action=delete-media&id=<?php echo (int) $productID; ?>', {
                        method: 'POST',
                        body: formData
                    }).then(function (response) {
                        return response.json();
                    }).then(function (data) {
                        if (!data || !data.ok) {
                            var message = data && data.message ? data.message : 'Failed to delete media.';
                            alert(message);
                        }
                    }).catch(function () {
                        alert('Failed to delete media.');
                    });
                });
            }
        });
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->
</body>
</html>