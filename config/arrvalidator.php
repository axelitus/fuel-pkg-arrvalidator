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

// @formatter:off
/**
 * ArrValidator Config example
 *
 * @package     Fuel
 * @subpackage  ArrValidator
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-arrvalidator
 */
return array(
	// Array that defines groups of validators as named arrays or comma-separated string of validators. 
	'groups' => array(
	),
	// Determines which validators and groups will be auto-loaded once the class is initiated.
	'auto_load' => array(
		// The validators that will be auto-loaded
		'validators' => array(
		),
		// The groups of validators that will be auto-loaded (the names of the groups defined previously).
		'groups' => array(
		)
	)
);
// @formatter:on
