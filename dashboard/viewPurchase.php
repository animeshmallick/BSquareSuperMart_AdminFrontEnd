<?php
session_start();
include "../Common.php";
$common = new Common();

if(!$common->is_admin_logged_in($_SESSION['admin_authToken'] ?? null) || !isset($_GET['purchaseId'])) {
    header("Location: ../login/index.php");
    exit();
}
$pid = $_GET['purchaseId'];
$api = (new ApiBuilder())->init()
    ->setMethod("GET")
    ->setPath("/getPurchaseDoc/admin/".$pid)
    ->setHeaders(["x-authorization" => "Bearer " . $_SESSION['admin_authToken']])
    ->execute();
if(!isset($api->getResponse()->purchase_id) || $api->getResponse()->purchase_id != $pid) {
    echo "Invalid Purchase ID";
}
$response = $api->getResponse();
function getStatusThemeClass($status): string
{
    $themes = [
        "PLACED" => "status-placed",
        "CONFIRMED" => "status-confirmed",
        "PACKAGING_IN_PROGRESS" => "status-packaging",
        "PACKAGING_COMPLETED" => "status-packaging-complete",
        "OUT_FOR_DELIVERY" => "status-out-for-delivery",
        "CANCELLED" => "status-cancelled",
        "DELIVERED_WITH_PAYMENT_PENDING" => "status-payment-pending",
        "DELIVERED_WITH_PAYMENT_SUCCESS" => "status-payment-success"
    ];
    return $themes[$status] ?? "status-default";
}

$statusClass = getStatusThemeClass($response->status);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Summary</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

</head>
<body>

<div class="purchase-container <?= $statusClass ?>">
    <div class="form-container w-full p-2 rounded-2xl shadow-xl border border-gray-200 mb-3">
        <h2 class="text-2xl font-bold text-center text-gray-800">🚚 Update Purchase Status</h2>

        <form class="space-y-6 flex items-center justify-center" action="updatePurchaseStatus.php" method="POST">
            <!-- Dropdown -->
            <div>
                <label for="status" class="block mb-1 text-sm font-medium text-gray-700">Select Status</label>
                <input type="text" name="purchaseId" value="<?= $response->purchase_id ?>" hidden>
                <select id="status" name="status" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <option value="" disabled selected>Select status</option>
                    <?php if($response->status === "PLACED"){ ?>
                        <option value="CONFIRMED">CONFIRMED</option>
                        <option value="CANCELLED">CANCELLED</option>
                    <?php }elseif ($response->status === "CONFIRMED"){ ?>
                        <option value="PACKAGING_IN_PROGRESS">PACKAGING IN PROGRESS</option>
                        <option value="CANCELLED">CANCELLED</option>
                    <?php }elseif ($response->status === "PACKAGING_IN_PROGRESS"){ ?>
                        <option value="READY_TO_SHIP">READY TO SHIP</option>
                        <option value="CANCELLED">CANCELLED</option>
                    <?php }elseif ($response->status === "READY_TO_SHIP"){ ?>
                        <option value="OUT_FOR_DELIVERY">OUT FOR DELIVERY</option>
                        <option value="CANCELLED">CANCELLED</option>
                    <?php }elseif ($response->status === "OUT_FOR_DELIVERY"){ ?>
                        <option value="DELIVERED_WITH_PAYMENT_SUCCESS">DELIVERED WITH PAYMENT SUCCESS</option>
                        <option value="DELIVERED_WITH_PAYMENT_PENDING">DELIVERED WITH PAYMENT PENDING</option>
                        <option value="CANCELLED">CANCELLED</option>
                    <?php }elseif ($response->status === "CANCELLED"){ ?>
                        <option value="PLACED">PLACED</option>
                    <?php } ?>
                </select>
            </div>
            <div class="mx-5"></div>
            <!-- Submit Button -->
            <button type="submit"
                    class="px-6 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-xl">
                Change Status 🔄
            </button>
        </form>
    </div>
    <div class="header">
        <h2>Purchase ID: <?= htmlspecialchars($response->purchase_id) ?></h2>
        <div class="details">Customer ID: <?= htmlspecialchars($response->customer_id) ?></div>
        <div class="details">
            Status: <span class="status-badge"><?= htmlspecialchars($response->status) ?></span>
        </div>
        <div class="details">Purchased On: <?= date("d M Y, h:i A", strtotime($response->purchased_at)) ?></div>
    </div>

    <div class="address">
        <strong>Delivery Address:</strong><br>
        <?= htmlspecialchars($response->address->address_line_1) ?><br>
        <?= htmlspecialchars($response->address->address_line_2) ?>
    </div>

    <div class="payment">
        <strong>Payment Method:</strong> <?= htmlspecialchars($response->payment->payment) ?>
    </div>

    <h3 style="margin-top:30px; margin-bottom:15px;">Items Ordered</h3>
    <div class="products-grid">
        <?php foreach ($response->orders as $order):
            $product = $order->product; ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                <div class="product-content">
                    <h4><?= htmlspecialchars($product->name) ?></h4>
                    <p><?= htmlspecialchars($product->brand) ?> • <?= htmlspecialchars($product->size) ?></p>
                    <div class="price">₹<?= htmlspecialchars($product->selling_price) ?>
                        <span class="mrp">₹<?= htmlspecialchars($product->mrp) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
