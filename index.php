<?php
include __DIR__ . '/loader.php';

use Quark\Quark;
use Quark\QuarkConfig;

use Quark\DataProviders\QuarkDNA;

const HELIX_DB = 'db';

$config = new QuarkConfig(__DIR__ . '/runtime/application.ini');
$config->AllowINIFallback(true);

$config->Localization(__DIR__ . '/localization.ini');

$config->DataProvider(HELIX_DB, new QuarkDNA());

Quark::Run($config);