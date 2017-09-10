<?php
/**
 * Basic install script for XHGui2.
 *
 * Does the following things.
 *
 * - Downloads composer.
 * - Installs dependencies.
 */
function out($out) {
    if (is_string($out)) {
        echo $out . "\n";
    }
    if (is_array($out)) {
        foreach ($out as $line) {
            out($line);
        }
    }
}

function runProcess($cmd, $input = null) {
    $descriptorSpec = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w')
    );
    $process = proc_open(
        $cmd,
        $descriptorSpec,
        $pipes
    );
    if (!is_resource($process)) {
        return 'ERROR - Could not start subprocess.';
    }
    $output = $error = '';
    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    proc_close($process);
    if (strlen($error)) {
        return 'ERROR - ' . $error;
    }
    return $output;
}

/**
 * Composer setup.
 */
if (!file_exists(__DIR__ . '/composer.phar')) {
    out("Downloading composer.");
    $cmd = "php -r \"eval('?>'.file_get_contents('https://getcomposer.org/installer'));\"";
    $output = runProcess($cmd);
    out($output);
} else {
    out("Composer already installed.");
}

if (!file_exists(__DIR__ . '/composer.phar')) {
    out('ERROR - No composer found');
    out('download failed, possible reasons:');
    out(' - you\'re behind a proxy.');
    out(' - composer servers is not available at the moment.');
    out(' - something wrong with network configuration.');
    out('please try download it manually from https://getcomposer.org/installer and follow manual.');
    out('');
    exit(9);
}

out("Installing dependencies.");
$cmd = 'php ' . __DIR__ . '/composer.phar install';
$output = runProcess($cmd);
out($output);

