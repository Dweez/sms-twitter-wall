<?php
/* Works out the time since the entry post, takes a an argument in unix time (seconds)
*/
function Timesince($original,$delay) {
    // array of time period chunks
    $chunks = array(
	array(60 * 60 * 24 * 365 , 'an'),
	array(60 * 60 * 24 * 30 , 'mois'),
	array(60 * 60 * 24 * 7, 'semaine'),
	array(60 * 60 * 24 , 'jour'),
	array(60 * 60 , 'heure'),
	array(60 , 'min'),
	array(1 , 'sec'),
    );

    $today = time(); /* Current unix time  */
    if(!empty($delay)){
    	$today = $today + $delay;
    }
    $since = $today - $original;

    // $j saves performing the count function each time around the loop
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
	
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
	
		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0) {
		    break;
		}
    }
	
    //$pluriel = ($name != "mois") ? 's' : '';
    $pluriel = ($name == "min" || $name == "sec" || $name == "mois") ? '' : 's';
    $print = ($count == 1) ? '1 '.$name : "$count {$name}$pluriel";

	
    if ($i + 1 < $j) {
		// now getting the second item
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
	
		// add second item if its greater than 0
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
			//$pluriel2 = ($name2 != "mois") ? 's' : '';
			$pluriel2 = ($name2 == "min" || $name2 == "sec" || $name2 == "mois") ? '' : 's';
		    $print .= ($count2 == 1) ? ', 1 '.$name2 : " $count2 {$name2}$pluriel2";
		}
    }
    return $print;
}
?>