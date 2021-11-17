<?php
namespace App\MyClasses;

class Cartesian {

  public function cartesian($input) {
        $result = array();
        foreach ($input as $key => $values) {
            if (empty($values)) {
                continue;
            }
            if (empty($result)) {
                foreach($values as $value) {
                    $result[] = array($key => $value);
                }
            }
            else {
                $append = array();
                foreach($result as &$product) {
                    $product[$key] = array_shift($values);
                    $copy = $product;
                    foreach($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }
                    array_unshift($values, $product[$key]);
                }
                $result = array_merge($result, $append);
            }
        }
        return $result;
  }

  public function PosNClose ($position, $length_limit) {

    	$cartesian = $this->cartesian($position);
    	foreach ($cartesian as $vary) {

    		sort($vary);
    		$flag=1;
    		for ($i=0; $i<(count($vary)-1); $i++) {
    			if (abs($vary[$i]-$vary[$i+1]) > $length_limit+1) $flag=0;
    		}
    		if ($flag == 1) $coord[] = array_unique(array_values($vary));
    	}

    	if (!empty($coord)) {
    		return $coord;
    	}
    	else {
    		return FALSE;
    	}
  }

  public function Pos2Close ($position, $length_limit) {

    	if (count($position) == 1 OR (count($position) == 2 AND array_values($position)[0] == array_values($position)[1])) {
    		$result = array_values($position);
    		foreach ($result[0] as $one_word)
    		$coord[][0] = $one_word;
    		return ($coord);
    	}

    	$cartesian = $this->cartesian($position);
    	foreach ($cartesian as $vary) {

    		sort($vary);
    		$flag=0;
    		for ($i=0; $i<(count($vary)-1); $i++) {
    			if (abs($vary[$i]-$vary[$i+1]) <= $length_limit+1 AND abs($vary[$i]-$vary[$i+1]) !=0) $flag=1;
    		}
    		if ($flag == 1) {
    			$coord[] = array_unique(array_values($vary));
    //			if (is_array($coord)) array_unique($coord);
    		}
    	}

    	if (!empty($coord)) {
    		return $coord;
    	}
    	else {
    		return FALSE;
    	}
  }

}
?>
