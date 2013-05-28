<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
if (isset($_REQUEST['url'])) {
    $url = parse_url($_REQUEST['url']);
    if ($url === false) {
        exit;
    }
    $url = http_build_url($url);
    if ($url === false) {
        exit;
    }
    $short = $redis->get($url);
    if (!$short) {
        do {
            $short = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);
            if (!$redis->exists($short)) {
               break; 
            }
        } while (1);
        $redis->set($short, $url);
        $redis->set($url, $short);
    }
    die($short);
}
$short = substr($_SERVER['REQUEST_URI'], 1);
$url = $redis->get($short);
if (!$url) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
header('Location: ' . $url);
exit;

// not my code got it from php.net to replace http_build_url that is in PECL
function http_build_url ($parsed_url) {
  $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
  $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
  $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
  $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
  $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
  $pass     = ($user || $pass) ? "$pass@" : '';
  $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
  $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
  return "$scheme$user$pass$host$port$path$query$fragment";
} 
