<?php

declare(strict_types=1);

namespace Src\Services;

class MathService
{
    public function calculateAverage(array $objects): float|int
    {
        $totalValue = 0;
        $totalAmount = count($objects);

        if (!$totalAmount) {
            return 0;
        }

        for ($i = 0; $i < $totalAmount; $i++) {
            $totalValue += $objects[$i];
        }

        return $totalValue / $totalAmount;
    }
}
