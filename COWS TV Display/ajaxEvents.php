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
	if (isset($_GET['bldgRoom'])) $cows = new cowsRss('http://cows.ucdavis.edu/ITS/event/atom?bldgRoom=' . $_GET['bldgRoom']);
	else $cows = new cowsRss('http://cows.ucdavis.edu/ITS/event/atom');
} catch (Exception $e) {
	exit(0);
}
//Generate eventSequence
$sequence = eventSequence::createSequenceFromArrayTimeBounded($cows->getData(time()), 
		    strtotime("midnight " . $_GET['date']), strtotime("midnight tomorrow " . $_GET['date']));
//Get the raw list
$eventList = $sequence->getList();

$index = 0;
$out = array();
if (count($eventList) >= 1)	{
	for ($i = 0; $i < count($eventList); $i++)	{
		if (!$eventList[$i]->isPast())  {
			$out[$index]['Title'] = $eventList[$i]->getTitle();
			$out[$index]['Time'] = $eventList[$i]->getStartTime() . '-' . $eventList[$i]->getEndTime();
			$out[$index]['Location'] = $eventList[$i]->getLocation();
			$index++;
		}
	}
	
	
	if (count($out) == 0)	{
		echo json_encode(array(0 => "noEvent"));
	}
	else	{ echo json_encode($out); }
	
	
}
//Handle no events case
else if (count($eventList) == 0)	{
	echo json_encode(array(0 => "noEventToday"));
}

?>