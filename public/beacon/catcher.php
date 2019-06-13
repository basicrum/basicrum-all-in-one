<?php
declare(strict_types=1);

// Hacking quickly to handle Cross Origin Requests
// Better if we implemente this in NIGIX level
$origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
$originHeader = 'Access-Control-Allow-Origin: ' . $origin;

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header($originHeader);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: X-Requested-With, Keep-Alive, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
    header('Access-Control-Max-Age: 86400');

    exit;
}

header($originHeader);
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Keep-Alive, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');


use App\BasicRum\Beacon\Catcher\Storage\File;

/**
 * We use this in order to return 200 to the user as soon as possible.
 *
 * So far we will use this solution instead of doing with webserver approach
 */
fastcgi_finish_request();

include __DIR__ . "/../../src/BasicRum/Beacon/Catcher/Storage/File.php";

$storage = new File();

// Depending on the size of beacon data Boomerang may send GET or POST
$beacon = !empty($_GET) ? $_GET : $_POST;

if (!empty($beacon)) {
    $beacon['user_agent'] = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $beacon['created_at'] = date("Y-m-d H:i:s");
    $beaconJson = json_encode($beacon);

    $storage->storeBeacon($beaconJson);
    return;
}

echo "<strong>No beacon baby!</strong>";