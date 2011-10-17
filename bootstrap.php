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

/**
 * Arr\Validator Bootstrap
 *
 * @package     Fuel
 * @subpackage  Arr Validator
 */
Autoloader::add_core_namespace('Arr');
 
Autoloader::add_classes(array(
	'Arr\\Arr_Validator'					=> __DIR__.'/classes/arr/validator.php',
	'Arr\\Arr_Validator_Rule'				=> __DIR__.'/classes/arr/validator/rule.php',
	'Arr\\Arr_Validator_Operator'			=> __DIR__.'/classes/arr/validator/operator.php',
	'Arr\\Arr_Validator_Glue'				=> __DIR__.'/classes/arr/validator/glue.php',
	'Arr\\Arr_Validator_Rule_Group'			=> __DIR__.'/classes/arr/validator/rule/group.php',
	'Arr\\Arr_Validator_Node'				=> __DIR__.'/classes/arr/validator/node.php',
));