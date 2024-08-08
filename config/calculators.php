<?php

/*
|--------------------------------------------------------------------------
| Mappings for the calculator
|--------------------------------------------------------------------------
|
| maps to the Calculators by product type ID
|
*/

return [
  'mappings' => [
      1 => \App\Calculators\WeightCalculator::class,
      2 =>  \App\Calculators\VolumeCalculator::class,
      3 =>  \App\Calculators\WeightCalculator::class
  ],
];
