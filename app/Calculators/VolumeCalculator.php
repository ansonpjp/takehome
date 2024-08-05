<?php

namespace App\Calculators;

class VolumeCalculator  implements CalculatorInterface
{
    /**
     * Calculates the sum for a product by VOLUME
     *
     * This method multiplies the initial unit quantity of a product by its volume.
     * @param array $product The product data
     * @return float The calculated total weight of the product.
     */
    public function calculate(array $product): float
    {
        return $product['unit_quantity_initial'] * $product['Product']['volume'];
    }
}
