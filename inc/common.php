<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

/**
 * Settings
 * Used to get the value of the specified setting.
 * @param string $setting The setting...
 */
function settings($setting)
{
	global $CACHE, $db;
	
	// Check if the setting has already been fetched
	// and return it if it has.
	if(isset($CACHE['settings'][$setting])) return $CACHE['settings'][$setting];
	
	// Looks like the setting isn't in the cache,
	// lets fetch it now...
	$result = $db->fetcharray($db->query("SELECT setting, value FROM ".DBPF."settings WHERE setting='".$db->es($setting)."' LIMIT 1"));
	$CACHE['settings'][$setting] = $result['value'];
	
	($hook = FishHook::hook('getsetting')) ? eval($hook) : false;
	
	return $CACHE['settings'][$setting];
}

/**
 * Template
 * Used to easily fetch templates.
 * @param string $template Template Name
 * @return string
 */
function template($template)
{
	// Check if the template exists
	if(file_exists(TRAQPATH.'/templates/'.settings('theme').'/'.$template.".php")) {
		return TRAQPATH.'/templates/'.settings('theme').'/'.$template.".php";
	} else {
	// Display an error it we couldn't load it
		error("Template","Unable to load file: <code>".settings('theme')."/".$template."</code>");
	}
}

/**
 * Alt. Background
 * Used to get an alternate background class.
 * @param string $even Even class color.
 * @param string $odd Odd class color.
 */
function altbg($even='even',$odd='odd')
{
	static $bg;
	
	if($bg == $odd)
		return $bg = $even;
	else
		return $bg = $odd;
}

/**
 * Add Breadcrumb
 * Used to easily add breadcrumbs.
 * @param string $url The URL.
 * @param string $label The Label.
 */
function addcrumb($url,$label)
{
	global $breadcrumbs;
	
	$breadcrumbs[] = array('url'=>$url,'label'=>$label);
}

/**
 * Error
 * Used to display an error message.
 * @param string $title Error title.
 * @param string $message Error message.
 */
function error($title,$message)
{
	die("<blockquote style=\"border:2px solid darkred;padding:5px;background:#f9f9f9;font-family:arial; font-size: 14px;\"><h1 style=\"margin:0px;color:#000;border-bottom:1px solid #000;margin-bottom:10px;\">".$title." Error</h1><div style=\"padding: 0;\">".$message."</div><div style=\"color:#999;border-top:1px solid #000;margin-top:10px;font-size:small;padding-top:2px;\">Traq ".TRAQVER." &copy; 2009 Jack Polgar</div></blockquote>");
}

/**
 * Format Text
 * Used to format text.
 * @param string $text The text to format.
 * @return string
 */
function formattext($text)
{
	// [ticket:x] to ticked URL
	global $uri,$project;
	$text = preg_replace("/\[ticket:(.*?)\\]/is",'<a href="'.$uri->anchor($project['slug'],'ticket-$1').'">[Ticket #$1]</a>',$text);
	
	($hook = FishHook::hook('formattext')) ? eval($hook) : false;
	
	return $text;
}

/**
 * Locale String
 * Gets the specified locale string for the set language.
 * @param string $string String name/key
 * @param mixed $vars
 * @return string
 */
function l($string,$vars=array())
{
	global $lang;
	
	// Check if the string exists
	if(!isset($lang[$string])) return '['.$string.']';
	
	// Get the locale string
	$string = $lang[$string];
	
	// Check if the $vars is an array or use the function args.
	if(!is_array($vars)) $vars = array_slice(func_get_args(),1);
	
	// Loop through the vars and replace the the {x} stuff
	foreach($vars as $var)
	{
		++$v;
		$string = str_replace('{'.$v.'}',$var,$string);
	}
	
	($hook = FishHook::hook('function_locale')) ? eval($hook) : false;
	
	// Now return it...
	return $string;
}

/**
 * Is Project
 * Check if the supplied string is a project.
 * @param string $string String to check if a project exists with that slug.
 * @return integer
 */
function is_project($string)
{
	global $db;
	return $db->numrows($db->query("SELECT slug FROM ".DBPF."projects WHERE slug='".$db->escapestring($string)."' LIMIT 1"));
}

/**
 * Simple if()
 * Used to easy execute a condition.
 * @param condition $condition The condition to check.
 * @param mixed $true Returned if condition is true.
 * @param mixed $false Returned if condition is false.
 * @return mixed
 */
function iif($condition, $true, $false='')
{
	return ($condition ? $true : $false);
}

// Stupid function to easily make a simple array.
function a()
{
	return func_get_args();
}

/**
 * Ticket Status List
 * Fetches the requred type of ticket status options in an array.
 * @param integer $getstatus Status type to fetch (1 for open, 0 for closed)
 * @return array
 */
function ticket_status_list($getstatus=1)
{
	global $db;
	
	$status = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."ticket_status ".(is_numeric($getstatus) ? "WHERE status='".$getstatus."'" :'')." ORDER BY name ASC");
	while($info = $db->fetcharray($fetch))
		$status[] = $info;
	
	($hook = FishHook::hook('function_ticket_statuses')) ? eval($hook) : false;
	return $status;
}

/**
 * Ticket Types
 * Fetches the Ticket Types specified in the AdminCP.
 * @return array
 */
function ticket_types()
{
	global $db;
	
	$types = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."ticket_types ORDER BY id ASC");
	while($info = $db->fetcharray($fetch))
		$types[] = $info;
	
	($hook = FishHook::hook('function_ticket_types')) ? eval($hook) : false;
	return $types;
}

/**
 * Ticket Priorities
 * Fetches the Ticket Priorities specified in the AdminCP.
 * @return array
 */
function ticket_priorities()
{
	global $db;
	
	$priorities = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."priorities ORDER BY id DESC");
	while($info = $db->fetcharray($fetch))
		$priorities[] = $info;
	
	($hook = FishHook::hook('function_ticket_priorities')) ? eval($hook) : false;
	return $priorities;
}

/**
 * Ticket Severities
 * Fetches the Ticket Severities specified in the AdminCP.
 * @return array
 */
function ticket_severities()
{
	global $db;
	
	$severities = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."severities ORDER BY id ASC");
	while($info = $db->fetcharray($fetch))
		$severities[] = $info;
	
	($hook = FishHook::hook('function_ticket_severities')) ? eval($hook) : false;
	return $severities;
}

/**
 * Ticket Status
 * Gets the ticket status.
 * @return array
 */
function ticket_status($status_id)
{
	global $db;
	$status = $db->queryfirst("SELECT * FROM ".DBPF."ticket_status WHERE id='".$db->res($status_id)."' LIMIT 1");
	return $status['name'];
}

/**
 * Ticket Type
 * Gets the ticket type.
 * @return array
 */
function ticket_type($type_id)
{
	global $db;
	$status = $db->queryfirst("SELECT * FROM ".DBPF."ticket_types WHERE id='".$db->res($type_id)."' LIMIT 1");
	return $status['name'];
}

/**
 * Ticket Priority
 * Gets the ticket priority.
 * @return array
 */
function ticket_priority($priority_id)
{
	global $db;
	$priority = $db->queryfirst("SELECT * FROM ".DBPF."priorities WHERE id='".$db->res($priority_id)."' LIMIT 1");
	return $priority['name'];
}

/**
 * Ticket Severity
 * Gets the ticket severity.
 * @return array
 */
function ticket_severity($severity_id)
{
	global $db;
	$severity = $db->queryfirst("SELECT * FROM ".DBPF."severities WHERE id='".$db->res($severity_id)."' LIMIT 1");
	return $severity['name'];
}

/**
 * Ticket Columns
 * Returns an array of the ticket columns that can be displayed on the view tickets page.
 * @return array
 */
function ticket_columns()
{
	$columns = array(
		'ticket',
		'summary',
		'status',
		'owner',
		'type',
		'severity',
		'component',
		'milestone',
		'version',
		'assigned_to'
	);
	($hook = FishHook::hook('function_ticket_columns')) ? eval($hook) : false;
	return $columns;
}

/**
 * Ticket Filters
 * Returns an array of ticket filters
 * @return array
 */
function ticket_filters()
{
	$filters = array(
		'component',
		'milestone',
		'version',
		'status',
		'type'
	);
	($hook = FishHook::hook('function_ticket_filters')) ? eval($hook) : false;
	return $filters;
}

/**
 * Project Milestones
 * Fetches the project milestones.
 * @return array
 */
function project_milestones($project_id=NULL)
{
	global $project, $db;
	$project_id = ($project_id == NULL ? $project['id'] : $project_id);
	
	$milestones = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."milestones WHERE project_id='".$db->es($project_id)."' ORDER BY displayorder ASC");
	while($info = $db->fetcharray($fetch))
		$milestones[] = $info;
	
	($hook = FishHook::hook('function_project_milestones')) ? eval($hook) : false;
	return $milestones;
}

/**
 * Project Versions
 * Fetches the project verions.
 * @return array
 */
function project_versions($project_id=NULL)
{
	global $project, $db;
	$project_id = ($project_id == NULL ? $project['id'] : $project_id);
	
	$versions = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."versions WHERE project_id='".$db->es($project_id)."' ORDER BY version ASC");
	while($info = $db->fetcharray($fetch))
		$versions[] = $info;
	
	($hook = FishHook::hook('function_project_verions')) ? eval($hook) : false;
	return $versions;
}

/**
 * Project Components
 * Fetches the project components.
 * @return array
 */
function project_components($project_id=NULL)
{
	global $project, $db;
	$project_id = ($project_id == NULL ? $project['id'] : $project_id);
	
	$components = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."components WHERE project_id='".$db->es($project_id)."' ORDER BY name ASC");
	while($info = $db->fetcharray($fetch))
		$components[] = $info;
	
	($hook = FishHook::hook('function_project_components')) ? eval($hook) : false;
	return $components;
}

/**
 * Project Managers
 * Fetches the project managers.
 * @return array
 */
function project_managers($project_id=NULL)
{
	global $project, $db;
	$project_id = ($project_id == NULL ? $project['id'] : $project_id);
	
	if(!isset($project))
	{
		$info = $db->queryfirst("SELECT managers FROM ".DBPF."projects WHERE id='".$db->es($project_id)."' LIMIT 1");
		$managers = array();
		$manager_ids = explode(',',$info['managers']);
	}
	else
	{
		$manager_ids = $project['managers'];
	}
	
	
	foreach($manager_ids as $id)
		$managers[] = $db->queryfirst("SELECT id,username,name FROM ".DBPF."users WHERE id='".$db->es($id)."' LIMIT 1");
	
	($hook = FishHook::hook('function_project_managers')) ? eval($hook) : false;
	
	return $managers;
}

/**
 * Calcuate Percent
 * Used to calculate the percent of two numbers,
 * if both numbers are the same, 100(%) is returned.
 * @param integer $min Lowest number
 * @param integer $max Highest number
 * @return integer
 */
function getpercent($min,$max)
{
	if($min == $max) return 100;
	
	$calculate = ($min/$max*100);
	$split = explode('.',$calculate);
	return $split[0];
}

/**
 * Time Since
 * @param integer $original Original Timestamp
 * @param integer $detailed Detailed format or not
 * @return string
 */
function timesince($original, $detailed = false)
{
	$now = time(); // Get the time right now...
	
	// Time chunks...
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year', 'years'),
		array(60 * 60 * 24 * 30, 'month', 'months'),
		array(60 * 60 * 24 * 7, 'week', 'weeks'),
		array(60 * 60 * 24, 'day', 'days'),
		array(60 * 60, 'hour', 'hours'),
		array(60, 'minute', 'minutes'),
		array(1, 'second', 'seconds'),
	);
	
	// Get the difference
	$difference = ($now - $original);
	
	// Loop around, get the time since
	for($i = 0, $c = count($chunks); $i < $c; $i++)
	{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	// Format the time since
	//$since = $count." ".((1 == $count) ? $name : $names);
	$since = l('x_'.((1 == $count) ? $name : $names),$count);
	
	// Get the detailed time since if the detaile variable is true
	if($detailed && $i + 1 < $c)
	{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2))
			$since = l('x_and_x',$since,l('x_'.((1 == $count2) ? $name2 : $names2),$count2));
	}
	
	// Return the time since
	return $since;
}

/**
 * Time From
 * @param integer $original Original Timestamp
 * @param integer $detailed Detailed format or not
 * @return string
 */
function timefrom($original, $detailed = false)
{
	$now = time(); // Get the time right now...
	
	// Time chunks...
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year', 'years'),
		array(60 * 60 * 24 * 30, 'month', 'months'),
		array(60 * 60 * 24 * 7, 'week', 'weeks'),
		array(60 * 60 * 24, 'day', 'days'),
		array(60 * 60, 'hour', 'hours'),
		array(60, 'minute', 'minutes'),
		array(1, 'second', 'seconds'),
	);
	
	// Get the difference
	$difference = ($original - $now);
	
	// Loop around, get the time from
	for($i = 0, $c = count($chunks); $i < $c; $i++)
	{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	// Format the time from
	$from = l('x_'.((1 == $count) ? $name : $names),$count);
	
	// Get the detailed time from if the detaile variable is true
	if($detailed && $i + 1 < $c)
	{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2))
			$from = l('x_and_x',$from,l('x_'.((1 == $count2) ? $name2 : $names2),$count2));
	}
	
	// Return the time from
	return $from;
}
?>