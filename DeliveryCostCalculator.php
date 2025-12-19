
<?php
class DeliveryCostCalculator {
    private int $baseCost;
    private array $offers;

    public function __construct($baseCost, $offers) {
        $this->baseCost = $baseCost;
        $this->offers = $offers;
    }

    public function calculate(PackageItem $pkg): void {
        $cost = $this->baseCost + ($pkg->weight * 10) + ($pkg->distance * 5);
        $discount = 0;

        if (isset($this->offers[$pkg->offerCode])) {
            $offer = $this->offers[$pkg->offerCode];
            if ($offer->isApplicable($pkg->weight, $pkg->distance)) {
                $discount = ($cost * $offer->discount) / 100;
            }
        }

        $pkg->discount = round($discount, 2);
        $pkg->totalCost = round($cost - $discount, 2);
    }
}
