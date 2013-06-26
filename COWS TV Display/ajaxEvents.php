<?php
/**
 * ajaxEvents.php
 * 
 * Script for passing event information to the tv app via javascript
 * 
 * @author Zachary Ennenga
 */
require_once('includes/cowsRss.php');
require_once('includes/eventSequence.php');

//Get Feed
try	{
	$cows = new cowsRss('http://cows.ucdavis.edu/ITS/event/atom?bldgRoom=');// . $_GET['bldgRoom']);
} catch (Exception $e) {
	exit(0);
}
//Generate eventSequence
$sequence = eventSequence::createSequenceFromArrayTimeBounded($cows->getData(time()), 
		    strtotime("midnight" . $_GET['date'], time()), strtotime("midnight tomorrow" . $_GET['date'], time()));
//Get the raw list
$eventList = $sequence->getList();

$index = 0;
$out = array();
if (count($eventList) >= 1)	{
	for ($i = 0; $i < count($eventList); $i++)	{
		if (!$eventList[$i]->isPast())  {
			$out[$index][$index . ' Title'] = $eventList[$i]->getTitle();
			$out[$index][$index . ' Time'] = $eventList[$i]->getStartTime() . '-' . $eventList[$i]->getEndTime();
			$out[$index][$index . ' Location'] = $eventList[$i]->getLocation();
			$index++;
		}
	}
	$out[$index]['Number of Events'] = $index;
	
	
	if (count($out) == 0)	{
		echo json_encode(array(0 => "noEvent"));
	}
	else	{ echo json_encode($out); var_dump($out); }
	
	
}
//Handle no events case
else if (count($eventList) == 0)	{
	echo json_encode(array(0 => "noEventToday"));
}

?>