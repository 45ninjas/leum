<?php
include_once 'leum.api.php';

try {
    $API = new LeumApi($_REQUEST['request']);
    echo $API->processAPI();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
?>