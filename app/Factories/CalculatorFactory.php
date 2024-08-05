<?php

namespace App\Factories;

use App\Calculators\CalculatorInterface;
use InvalidArgumentException;

class CalculatorFactory
{
    public static function getCalculator(int $productTypeId): CalculatorInterface
    {
       $mappings = config('calculators.mappings');

       if(!isset($mappings[$productTypeId])) {
           throw new InvalidArgumentException("No calculator is defined for this product type");
       }

       return app($mappings[$productTypeId]);
    }
}
