<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Arr;

/**
 * Arr_Validator_Glue
 *
 * @package     Fuel
 * @subpackage  Arr Validator
 */
class Arr_Validator_Glue
{
	/**
	 * The AND glue type.
	 * 
	 * @var string AND glue value.
	 */
	const _AND = 'and';
	
	/**
	 * The OR glue type.
	 * 
	 * @var string OR glue value.
	 */
	const _OR = 'or';
	
	/**
	 * Verifies if the given param is a supported glue.
	 * 
	 * @return bool true if $glue is a supported glue, false otherwise.
	 */
	public static function is_glue($glue)
	{
		$return = false;

		if(is_string($glue) && $glue != '')
		{
			switch(strtolower($glue))
			{
				case static::_AND:
				case static::_OR:
					$return = true;
					break;
			}
		}
		
		return $return;
	}
}