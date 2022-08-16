<?php

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // should do a check here to match $_SERVER['HTTP_ORIGIN'] to a
    // whitelist of safe domains
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
}


#create a laravel app with crud requirements, host on my personal server and also host scrapper on the personal server, and the UI on a personal servver
# get at the form input tag <input type="hidden" name="_token" value="t0l2x6ggZmd06aL8Mo4Wv6bQPQOAP0vPYZlyBjFK">
function is_base64($string)
{
    // Check if there are valid base64 characters
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) return false;

    // Decode the string in strict mode and check the results
    $decoded = base64_decode($string, true);
    if (false === $decoded) return false;

    return $decoded;
}

function getSiteOG($url, $specificTags = 0)
{
    $doc = new DOMDocument();
    @$doc->loadHTML(file_get_contents($url));
    $res = [];

    foreach ($doc->getElementsByTagName('meta') as $m) {
        $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
        if (strpos($tag, 'csrf-token') === 0) $res[str_replace('csrf-token', 'csrf-token', $tag)] = $m->getAttribute('content');
    }
    return $specificTags ? array_intersect_key($res, array_flip($specificTags)) : $res;
}

if (isset($_GET['url']) && !empty($_GET['url'])) {
    $url = is_base64($_GET['url']);

    if ($url === false) die(json_encode(['token' => null, 'message' => 'the  query parameter(url [in base64encoded format]) is missing ']));

    $data = getSiteOG($url);
    die(json_encode(['token' => $data['csrf-token'], 'message' => 'success']));
}

die(json_encode(['token' => null, 'message' => 'the  query parameter(url [in base64encoded format]) is missing ']));
