<?php

echo "Running CLI Test...\n";

// Create a temporary input file
$input = <<<EOT
100 5
PKG1 50 30 OFR001
PKG2 75 125 OFFR0008
PKG3 175 100 OFR003
PKG4 110 60 OFR002
PKG5 155 95 NA
2 70 200
EOT;

file_put_contents("test_input.txt", $input);

// Run courier.php using redirected input
$output = shell_exec("php courier.php < test_input.txt");

// Remove extra formatting lines
$lines = preg_grep('/^PKG/', explode("\n", $output));
$cleanedOutput = implode("\n", array_map("trim", $lines));

// Expected output (your real results)
$expected = <<<EOT
PKG1 0 750 3.99 03:59:24
PKG2 0 1475 1.78 01:46:48
PKG3 0 2350 1.42 01:25:12
PKG4 0 1500 0.85 00:51:00
PKG5 0 2125 4.2 04:12:00
EOT;

// Compare outputs
if (trim($cleanedOutput) === trim($expected)) {
    echo "\n[PASS] Output matches expected result.\n";
} else {
    echo "\n[FAIL] Output does NOT match expected result.\n";
    echo "Expected:\n$expected\n\n";
    echo "Got:\n$cleanedOutput\n";
}

echo "\nCLI Test Completed.\n";
