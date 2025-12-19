
<?php
class PackageItem {
    public string $id;
    public int $weight;
    public int $distance;
    public string $offerCode;
    public float $discount = 0;
    public float $totalCost = 0;
    public float $deliveryTime = 0;

    public function __construct($id, $weight, $distance, $offerCode) {
        $this->id = $id;
        $this->weight = $weight;
        $this->distance = $distance;
        $this->offerCode = $offerCode;
    }
}
