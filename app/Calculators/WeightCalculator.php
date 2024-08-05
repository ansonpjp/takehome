<?php

namespace App\Calculators;

Class WeightCalculator implements CalculatorInterface
{
    /**
     * Calculates the sum for a product by WEIGHT.
     *
     * This method multiplies the initial unit quantity of a product by its weight.
     * @param array $product The product data
     * @return float The calculated total weight of the product.
     */
    public function calculate(array $product): float
    {
        return $product['unit_quantity_initial'] * $product['Product']['weight'];
    }
}
