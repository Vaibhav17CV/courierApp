
<?php
class Offer {
    public string $code;
    public int $discount;
    public int $minWeight;
    public int $maxWeight;
    public int $minDistance;
    public int $maxDistance;

    public function __construct($code, $discount, $minWeight, $maxWeight, $minDistance, $maxDistance) {
        $this->code = $code;
        $this->discount = $discount;
        $this->minWeight = $minWeight;
        $this->maxWeight = $maxWeight;
        $this->minDistance = $minDistance;
        $this->maxDistance = $maxDistance;
    }

    public function isApplicable($weight, $distance): bool {
        return $weight >= $this->minWeight &&
               $weight <= $this->maxWeight &&
               $distance >= $this->minDistance &&
               $distance <= $this->maxDistance;
    }
}
