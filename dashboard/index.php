<?php
session_start();
include '../Common.php';
$common = new Common();
if (!$common->is_admin_logged_in($_SESSION['admin_authToken'] ?? null)){
    header("Location: ../login/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out both; }

        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .slide-in-left { animation: slideInLeft 0.6s ease-out both; }

        @keyframes slideInUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .slide-in-up { animation: slideInUp 0.6s ease-out both; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 flex flex-col min-h-screen">

<header class="h-16 flex items-center justify-between px-6 bg-indigo-600 text-white shadow-lg slide-in-left">
    <h1 class="text-xl md:text-2xl font-semibold tracking-tight">Admin Dashboard</h1>
    <div class="flex items-center space-x-4 text-sm">
        <span class="hidden sm:inline">Welcome, Admin</span>
        <a href="../login/logout.php" class="underline hover:text-indigo-200 transition">Logout</a>
    </div>
</header>

<main class="flex flex-col md:flex-row flex-1 overflow-hidden">
    <aside class="md:w-72 w-full bg-white border-r border-gray-200 overflow-y-auto p-4 space-y-2 slide-in-left z-10" x-data="{active: ''}">
        <h2 class="text-lg font-semibold mb-4">Purchases</h2>
        <?php
        $api = (new ApiBuilder())
            ->init()
            ->setMethod("GET")
            ->setPath("/getAllPurchase/")
            ->setHeaders(["x-authorization" => "Bearer " . $_SESSION['admin_authToken']])
            ->execute();
        $response = $api->getResponse();
        foreach ($response as $purchase) {
            $purchaseId = htmlspecialchars($purchase->purchase_id);
            $phone = isset($purchase->phone) ? htmlspecialchars($purchase->phone) : 'N/A';
            $address = isset($purchase->address) ? htmlspecialchars(str_replace('+', ' ', $purchase->address)) : 'N/A';
            $status = isset($purchase->status) ? htmlspecialchars($purchase->status) : 'UNKNOWN';

            $statusColorMap = [
                'PLACED' => 'bg-gray-300 border-yellow-400',
                'CONFIRMED' => 'bg-blue-700 border-blue-400',
                'PACKAGING_IN_PROGRESS' => 'bg-yellow-700 border-indigo-400',
                'PACKAGING_COMPLETED' => 'bg-yellow-500 border-indigo-500',
                'OUT_FOR_DELIVERY' => 'bg-orange-400 border-orange-400',
                'CANCELLED' => 'bg-red-400 border-red-400',
                'DELIVERED_WITH_PAYMENT_PENDING' => 'bg-pink-600 border-pink-400',
                'DELIVERED_WITH_PAYMENT_SUCCESS' => 'bg-green-600 border-green-400',
                'UNKNOWN' => 'bg-gray-700 border-gray-400'
            ];
            $statusClass = $statusColorMap[$status] ?? $statusColorMap['UNKNOWN'];
            ?>
            <a href="viewPurchase.php?purchaseId=<?= $purchaseId; ?>"
               target="purchaseFrame"
               @click="active='<?= $purchaseId; ?>'"
               :class="active==='<?= $purchaseId; ?>' ? '<?= $statusClass; ?> text-white' : 'hover:bg-gray-50 text-gray-800'"
               class="block px-4 py-3 rounded-md transition transform hover:scale-105 fade-in shadow-sm border <?= $statusClass; ?>">
                <div class="font-semibold text-sm">Purchase ID Suffix: <?= explode("-", $purchaseId)[2]; ?></div>
                <div class="text-xs">📞 <?= $phone; ?></div>
                <div class="text-xs truncate">🏠 <?= $address; ?></div>
                <div class="text-xs italic mt-1">Status: <?= $status; ?></div>
            </a>
        <?php } ?>
    </aside>

    <section class="flex-1 relative bg-white slide-in-up">
        <iframe name="purchaseFrame" class="absolute inset-0 w-full h-full"></iframe>
    </section>
</main>

<footer class="bg-white text-center py-3 text-sm text-gray-500 border-t slide-in-up">
    &copy; <?= date("Y"); ?> Admin Panel. All rights reserved.
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const firstLink = document.querySelector('aside a');
        if (firstLink) firstLink.click();
    });
</script>
</body>
</html>
