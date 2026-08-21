<?php
// Log all requests
$fp = fopen('/tmp/api_requests.log', 'a');
fwrite($fp, "\n\n=== REQUEST AT " . date('Y-m-d H:i:s') . " ===\n");
fwrite($fp, "Method: " . $_SERVER['REQUEST_METHOD'] . "\n");
fwrite($fp, "Path: " . $_SERVER['REQUEST_URI'] . "\n");
fwrite($fp, "Headers: " . print_r(getallheaders(), true) . "\n");
fwrite($fp, "Body: " . file_get_contents('php://input') . "\n");
fclose($fp);
