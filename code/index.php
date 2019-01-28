<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once 'vendor/autoload.php';

$start = microtime(true);

$allowFormats = ['json', 'html'];

$url = $_GET['url'] ?: null;
$format = $_GET['format'] ?: 'html';

if ($url === null) {
    echo 'Url must be set';
    exit();
}

if (!in_array($format, $allowFormats, true)) {
    echo 'Wrong format. Only "json" or "html"';
    exit();
}

$host = 'http://172.17.0.1:4444/wd/hub';
$capabilities = DesiredCapabilities::chrome();
$driver = RemoteWebDriver::create($host, $capabilities, 5000);

$driver->get($url);

$buttonSelector = WebDriverBy::cssSelector('.contact-button span.spoiler');

$driver->findElement($buttonSelector)->click();

$driver->wait(10, 500)->until(
        WebDriverExpectedCondition::invisibilityOfElementLocated($buttonSelector)
);

$adSelector = WebDriverBy::cssSelector('.clr.offerbody');
$adHtml = $driver->findElement($adSelector)->getAttribute('innerHTML');

$driver->quit();

if ($format === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'html' => $adHtml,
        'execution_time' => microtime(true) - $start,
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

echo $adHtml, PHP_EOL;
echo microtime(true) - $start;