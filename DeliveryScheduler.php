<?php

class DeliveryScheduler
{
    private int $vehicleCount;
    private int $speed;
    private int $maxWeight;

    private array $vehicleAvailableAt = [];

    public function __construct(int $vehicleCount, int $speed, int $maxWeight)
    {
        $this->vehicleCount = $vehicleCount;
        $this->speed = $speed;
        $this->maxWeight = $maxWeight;

        $this->vehicleAvailableAt = array_fill(0, $vehicleCount, 0.0);
    }

    public function schedule(array $packages): void
    {
        $pending = array_values($packages); // ensure indexed

        while (!empty($pending)) {

            // 1️⃣ earliest available vehicle
            $vehicleIndex = array_search(
                min($this->vehicleAvailableAt),
                $this->vehicleAvailableAt,
                true
            );

            $currentTime = $this->vehicleAvailableAt[$vehicleIndex];

            // 2️⃣ build best shipment
            $shipment = $this->buildBestShipment($pending);

            // 3️⃣ deliver packages
            $farthestDistance = 0;

            foreach ($shipment as $pkg) {
                $travelTime = $pkg->distance / $this->speed;
                // $pkg->deliveryTime = round($currentTime + $travelTime, 2);

                $pkg->deliveryTime = $this->truncate($currentTime + $travelTime, 2);

                if ($pkg->distance > $farthestDistance) {
                    $farthestDistance = $pkg->distance;
                }
            }

            // 4️⃣ vehicle return
            $returnTime = (2 * $farthestDistance) / $this->speed;
            // $this->vehicleAvailableAt[$vehicleIndex] =
            //     round($currentTime + $returnTime, 2);
            $this->vehicleAvailableAt[$vehicleIndex] =
                $this->truncate($currentTime + $returnTime, 2);

            // 5️⃣ remove delivered + REINDEX
            $pending = array_values(
                array_udiff(
                    $pending,
                    $shipment,
                    fn($a, $b) => $a === $b ? 0 : -1
                )
            );
        }
    }

    private function buildBestShipment(array $packages): array
    {
        $bestShipment = [];
        $bestCount = 0;
        $bestWeight = 0;

        $n = count($packages);

        for ($mask = 1; $mask < (1 << $n); $mask++) {
            $shipment = [];
            $totalWeight = 0;

            for ($i = 0; $i < $n; $i++) {
                if ($mask & (1 << $i)) {
                    $totalWeight += $packages[$i]->weight;
                    $shipment[] = $packages[$i];
                }
            }

            if ($totalWeight > $this->maxWeight) {
                continue;
            }

            $count = count($shipment);

            if (
                $count > $bestCount ||
                ($count === $bestCount && $totalWeight > $bestWeight)
            ) {
                $bestShipment = $shipment;
                $bestCount = $count;
                $bestWeight = $totalWeight;
            }
        }

        return $bestShipment;
    }

    private function truncate(float $value, int $decimals = 2): float
    {
        $factor = pow(10, $decimals);
        return floor($value * $factor) / $factor;
    }
}
