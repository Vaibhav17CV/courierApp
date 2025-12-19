<?php
require 'Offer.php';
require 'PackageItem.php';
require 'DeliveryCostCalculator.php';
require 'DeliveryScheduler.php';

/**
 * Print instructions to STDERR
 */
function printInstructions(): void
{
    fwrite(STDERR, PHP_EOL);
    fwrite(STDERR, "📦 Courier Service CLI\n");
    fwrite(STDERR, "---------------------------------\n");
    fwrite(STDERR, "Please provide input in this format:\n\n");

    fwrite(STDERR, "base_cost number_of_packages\n");
    fwrite(STDERR, "pkg_id weight distance offer_code\n");
    fwrite(STDERR, "pkg_id weight distance offer_code\n");
    fwrite(STDERR, "...\n");
    fwrite(STDERR, "number_of_vehicles max_speed max_weight\n\n");

    fwrite(STDERR, "Example:\n");
    fwrite(STDERR, "100 2\n");
    fwrite(STDERR, "PKG1 5 5 OFR001\n");
    fwrite(STDERR, "PKG2 15 5 OFR002\n");
    fwrite(STDERR, "1 70 200\n");
    fwrite(STDERR, "---------------------------------\n\n");
}

/**
 * Safe line reader
 */
function readLineSafe(): string
{
    $line = fgets(STDIN);
    if ($line === false) {
        throw new Exception("❌ Unexpected end of input");
    }
    return trim($line);
}

function printOutputHeader(): void
{
    fwrite(STDERR, PHP_EOL);
    fwrite(STDERR, "📊 Delivery Summary\n");
    fwrite(STDERR, "---------------------------------------------------\n");
    fwrite(
        STDERR,
        "PackageID | Discount | TotalCost | DeliveryTime(Hrs)| DeliveryTime(HH:MM:SS)\n"
    );
    fwrite(STDERR, "---------------------------------------------------\n");
}

function formatHoursToHMS(float $hours): string
{
    $totalSeconds = (int) round($hours * 3600);

    $h = intdiv($totalSeconds, 3600);
    $m = intdiv($totalSeconds % 3600, 60);
    $s = $totalSeconds % 60;

    return sprintf('%02d:%02d:%02d', $h, $m, $s);
}

// 🔹 Show instructions
printInstructions();

try {
    // 1️⃣ Base cost & package count
    [$baseCost, $count] = array_map(
        'intval',
        explode(' ', readLineSafe())
    );

    if ($count <= 0) {
        throw new Exception("❌ Number of packages must be greater than 0");
    }

    // 2️⃣ Read packages
    $packages = [];
    for ($i = 0; $i < $count; $i++) {
        $parts = explode(' ', readLineSafe());

        if (count($parts) !== 4) {
            throw new Exception(
                "❌ Invalid package input. Expected: pkg_id weight distance offer_code"
            );
        }

        [$id, $weight, $distance, $offer] = $parts;
        $packages[] = new PackageItem(
            $id,
            (int)$weight,
            (int)$distance,
            $offer
        );
    }

    // 3️⃣ Vehicle info
    $vehicleParts = explode(' ', readLineSafe());
    if (count($vehicleParts) !== 3) {
        throw new Exception(
            "❌ Vehicle input required: no_of_vehicles speed max_weight"
        );
    }

    [$vehicles, $speed, $maxWeight] = array_map('intval', $vehicleParts);

    if ($vehicles <= 0 || $speed <= 0 || $maxWeight <= 0) {
        throw new Exception("❌ Vehicle values must be positive numbers");
    }

    // 4️⃣ Offers
    $offers = [
        'OFR001' => new Offer('OFR001', 10, 70, 200, 0, 200),
        'OFR002' => new Offer('OFR002', 7, 100, 250, 50, 150),
        'OFR003' => new Offer('OFR003', 5, 10, 150, 50, 250),
    ];

    // 5️⃣ Cost calculation
    $calculator = new DeliveryCostCalculator($baseCost, $offers);
    foreach ($packages as $pkg) {
        $calculator->calculate($pkg);
    }

    // 6️⃣ Delivery scheduling
    $scheduler = new DeliveryScheduler($vehicles, $speed, $maxWeight);
    $scheduler->schedule($packages);

    printOutputHeader();

    // 7️⃣ Output (ONLY results)
    foreach ($packages as $pkg) {
        $formattedTime = formatHoursToHMS($pkg->deliveryTime);

        echo "{$pkg->id} {$pkg->discount} {$pkg->totalCost} {$pkg->deliveryTime} {$formattedTime}\n";
    }

} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
