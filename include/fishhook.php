<?php
/**
 * FishHook
 * @author Jack Polgar
 * @package FishHook
 * @copyright Jack Polgar
 * @version 1.0
 */

class FishHook {
	// Used to store the functions and hooks.
	private static $hooks = array();
	
	/**
	 * Hook Function
	 * Used to execute the functions for the specified hook.
	 * @param $hook Hook
	 */
	public function hook($hook)
	{
		if(count(self::$hooks[$hook]) > 0)
		{
			foreach(self::$hooks[$hook] as $id => $function)
			{
				$function = self::$hooks[$hook][$id];
				$function();
			}
		}
	}
	
	/**
	 * Add Function
	 * Used to add functions and hooks.
	 * @param $function Function to run
	 * @param $hook Hook
	 */
	public function add($function,$hook)
	{
		self::$hooks[$hook][] = $function;
	}
	
	// Used to get the FishHook version.
	public function version()
	{
		return '1.0 r'.str_replace(' $','',substr('$Rev$',6));
	}
}

/* $Id$ */
?>