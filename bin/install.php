#!/usr/bin/env php
<?php

echo "Welcome to Trunk framework!\n";
echo "Let's configure your new project.\n\n";

$db = 0;
while (!in_array($db, [1, 2, 3])) {
    echo "Which database do you prefer?\n";
    echo "  [1] MySQL\n";
    echo "  [2] PostgreSQL\n";
    echo "  [3] None\n";
    echo "Select an option [1-3]: ";
    $handle = fopen("php://stdin", "r");
    $db = (int)trim(fgets($handle));
}

$api = 0;
while (!in_array($api, [1, 2])) {
    echo "\nWhich API style do you prefer?\n";
    echo "  [1] REST\n";
    echo "  [2] GraphQL\n";
    echo "Select an option [1-2]: ";
    $handle = fopen("php://stdin", "r");
    $api = (int)trim(fgets($handle));
}

$packages = [];
if ($db === 1) {
    $packages[] = 'react/mysql';
} elseif ($db === 2) {
    $packages[] = 'voryx/pgasync';
}

if ($api === 2) {
    $packages[] = 'webonyx/graphql-php';
}

if (!empty($packages)) {
    echo "\nInstalling selected packages: " . implode(', ', $packages) . "\n";
    system('composer require ' . implode(' ', $packages));
} else {
    echo "\nNo extra packages to install.\n";
}
echo "\nSetup complete!\n";
