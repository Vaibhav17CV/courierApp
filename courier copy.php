
<?php
require 'Offer.php';
require 'PackageItem.php';
require 'DeliveryCostCalculator.php';
require 'DeliveryScheduler.php';

$handle = fopen("php://stdin", "r");

[$baseCost, $count] = array_map('intval', explode(' ', trim(fgets($handle))));

$packages = [];
for ($i = 0; $i < $count; $i++) {
    [$id, $w, $d, $o] = explode(' ', trim(fgets($handle)));
    $packages[] = new PackageItem($id, (int)$w, (int)$d, $o);
}

[$vehicles, $speed, $maxWeight] = array_map('intval', explode(' ', trim(fgets($handle))));

$offers = [
    'OFR001' => new Offer('OFR001', 10, 70, 200, 0, 200),
    'OFR002' => new Offer('OFR002', 7, 100, 250, 50, 150),
    'OFR003' => new Offer('OFR003', 5, 10, 150, 50, 250)
];

$calculator = new DeliveryCostCalculator($baseCost, $offers);
foreach ($packages as $pkg) {
    $calculator->calculate($pkg);
}

$scheduler = new DeliveryScheduler($vehicles, $speed, $maxWeight);
$scheduler->schedule($packages);

foreach ($packages as $pkg) {
    echo "{$pkg->id} {$pkg->discount} {$pkg->totalCost} {$pkg->deliveryTime}
";
}
