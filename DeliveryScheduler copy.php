
<?php
class DeliveryScheduler {
    private int $vehicleCount;
    private int $speed;
    private int $maxWeight;
    private array $availableAt;

    public function __construct($vehicleCount, $speed, $maxWeight) {
        $this->vehicleCount = $vehicleCount;
        $this->speed = $speed;
        $this->maxWeight = $maxWeight;
        $this->availableAt = array_fill(0, $vehicleCount, 0);
    }

    public function schedule(array $packages): void {
        usort($packages, fn($a, $b) => $b->weight <=> $a->weight);

        foreach ($packages as $pkg) {
            $vehicle = array_search(min($this->availableAt), $this->availableAt);
            $travel = $pkg->distance / $this->speed;
            $pkg->deliveryTime = round($this->availableAt[$vehicle] + $travel, 2);
            $this->availableAt[$vehicle] += 2 * $travel;
        }
    }
}
