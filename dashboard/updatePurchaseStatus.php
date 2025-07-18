<?php
session_start();
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['status']) || !isset($_POST['purchaseId']) ||
        !$common->is_admin_logged_in($_SESSION['admin_authToken'] ?? null)) {
    header( "Location: index.php");
    exit();
}
$status = $_POST['status'];
$pid = $_POST['purchaseId'];
$api = (new ApiBuilder())->init()
    ->setMethod("POST")
    ->setHeaders(["x-authorization" => "Bearer ".$_SESSION['admin_authToken'] ?? null])
    ->setPath("/changePurchaseStatus/".$pid."/".$status)
    ->setRequestBody([])
    ->execute();

header("Location: viewPurchase.php?purchaseId=".$pid);
