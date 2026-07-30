#!/usr/bin/env php
<?php

/**
 * Prompts only work with a real TTY attached to stdin. Composer's post-create-project-cmd
 * scripts, CI runners, and piped/scripted invocations don't have one - fgets(STDIN) would
 * return false immediately in that case, and looping on that never satisfies the "valid
 * choice" condition, so this falls back to sane defaults instead of spinning forever.
 */
function promptChoice(string $question, array $options, int $default): int
{
    if (!stream_isatty(STDIN)) {
        echo "{$question}\n(non-interactive shell detected, using default: {$options[$default]})\n";
        return $default;
    }

    $min = min(array_keys($options));
    $max = max(array_keys($options));

    while (true) {
        echo "{$question}\n";
        foreach ($options as $key => $label) {
            echo "  [{$key}] {$label}\n";
        }
        echo "Select an option [{$min}-{$max}]: ";

        $line = fgets(STDIN);
        if ($line === false) {
            echo "\n(no input received, using default: {$options[$default]})\n";
            return $default;
        }

        $choice = (int) trim($line);
        if (isset($options[$choice])) {
            return $choice;
        }
    }
}

echo "Welcome to Trunk framework!\n";
echo "Let's configure your new project.\n\n";

$db = promptChoice('Which database do you prefer?', [1 => 'MySQL', 2 => 'PostgreSQL', 3 => 'None'], 1);

$packages = [];
if ($db === 1) {
    // friends-of-reactphp/mysql has no stable release with the MysqlClient class Trunk's
    // MysqlDriver depends on - it only exists on the 0.7.x-dev branch, so this constraint
    // must be explicit or a bare "composer require react/mysql" installs an incompatible
    // stable v0.6.x instead.
    $packages[] = 'react/mysql:^0.7 || ^0.8';
} elseif ($db === 2) {
    $packages[] = 'voryx/pgasync';
}

if (!empty($packages)) {
    echo "\nInstalling selected packages: " . implode(', ', $packages) . "\n";
    system('composer require ' . implode(' ', array_map('escapeshellarg', $packages)));
} else {
    echo "\nNo extra packages to install.\n";
}
echo "\nSetup complete!\n";
