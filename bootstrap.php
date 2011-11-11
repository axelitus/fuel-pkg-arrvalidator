<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.1
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * ArrValidator
 *
 * @package     Fuel
 * @subpackage  ArrValidator
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-arrvalidator
 */
Autoloader::add_core_namespace('ArrValidator');
 
Autoloader::add_classes(array(
	'ArrValidator\\ArrValidator'					=> __DIR__.'/classes/arrvalidator.php',
	'ArrValidator\\ArrValidator_Node'				=> __DIR__.'/classes/arrvalidator/node.php',
	'ArrValidator\\ArrValidator_Operator'			=> __DIR__.'/classes/arrvalidator/operator.php',
	'ArrValidator\\OperatorNotSupportedException'	=> __DIR__.'/classes/arrvalidator/operator.php'
));