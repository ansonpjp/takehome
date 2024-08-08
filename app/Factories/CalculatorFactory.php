<?php

namespace App\Factories;

use App\Calculators\CalculatorInterface;
use Illuminate\Support\Facades\Log;

class CalculatorFactory
{
    public static function getCalculator(int $productTypeId): ?CalculatorInterface
    {
       $mappings = config('calculators.mappings');

       if(!isset($mappings[$productTypeId])) {
           Log::channel('slack')->error('No calculator is defined for this product with product_type_id: ' . $productTypeId);
           return null;
       }

       return app($mappings[$productTypeId]);
    }
}
