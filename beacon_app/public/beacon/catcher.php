<?php

declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

$debugMode = isset($_GET['debug_mode']);

// Hacking quickly to handle Cross Origin Requests
// Better if we implement this in NIGIX level
$origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
$originHeader = 'Access-Control-Allow-Origin: '.$origin;

if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
    header($originHeader);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: X-Requested-With, Keep-Alive, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
    header('Access-Control-Max-Age: 86400');

    exit;
}

header($originHeader);
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Keep-Alive, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');

use App\BasicRum\EventsStorage\FileSystem\Raw;

/*
 * We use this in order to return 200 to the user as soon as possible.
 *
 * So far we will use this solution instead of doing with webserver approach
 */
if (!$debugMode) {
    header('Content-Type: image/gif');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
    header("HTTP/1.0 204 No Content");

    fastcgi_finish_request();
}

$storage = new Raw();

// Depending on the size of beacon data Boomerang may send GET or POST
$beacon = !empty($_GET) ? $_GET : $_POST;

if (!empty($beacon)) {
    $beacon['user_agent'] = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $beacon['created_at'] = date('Y-m-d H:i:s');
    $beaconJson = json_encode($beacon);

    $storage->storeBeacon($beaconJson);

    return;
}

echo '<strong>No beacon baby!</strong>';
