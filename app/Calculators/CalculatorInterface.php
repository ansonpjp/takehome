<?php
namespace App\Calculators;

interface CalculatorInterface
{
    /**
     * method to calculate the SUM
     */
    public function calculate(array $product): float;
}


?>
