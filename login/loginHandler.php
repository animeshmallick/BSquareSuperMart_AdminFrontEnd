<?php
session_start();
include "../ApiBuilder.php";
$loginDetails = [
    'phone' => $_POST['PHONE'],      // Extract 'phone' input from POST form
    'password'    => $_POST['PASSWORD']    // Extract 'password' input from POST form
];
$redirect = $_POST['redirect'] ?? null;
$api = (new ApiBuilder())->init()
    ->setMethod("POST")
    ->setPath("/adminLogin")
    ->setRequestBody($loginDetails)
    ->execute();
$response = $api->getResponse();
if(isset($response->adminAuthToken)) {
    $_SESSION['admin_authToken'] = $response->adminAuthToken;
    if ($redirect == null)
        header("Location: ../dashboard/");
    else
        header("Location: ../" . $redirect . "/");
}else{
    echo "Error Getting Admin Auth Token from Server";
    print_r($response);
}
?>


