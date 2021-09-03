<?php
namespace App\MyClasses;

class num {
// склонение числительных (array('товар', 'товара', 'товаров'))
  public static function declension($number, $titles) {
  	$abs = abs($number);
  	$cases = array (2, 0, 1, 1, 1, 2);
  	return number_format($number, 0, '.', ' ')." ".$titles[ ($abs%100 > 4 && $abs %100 < 20) ? 2 : $cases[min($abs%10, 5)] ];
  }
}
?>
