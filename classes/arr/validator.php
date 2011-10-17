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
 * Arr_Validator
 *
 * @package     Fuel
 * @subpackage  Arr Validator
 */
class Arr_Validator
{
	/**
	 * @var string The version of the Arr\Validator package.
	 */
	const VERSION = '1.0';

	/**
	 * @var array Holds the different Arr_Validator instances.
	 */
	protected static $_instances = array();

	/**
	 * @var Arr_Validator the default validator (the first that is loaded).
	 */
	protected static $_default = null;

	/**
	 * @var array Holds the validator nodes for an instance.
	 */
	public $_nodes = array();

	/**
	 * Prevent direct instatiation
	 */
	protected function __construct()
	{
	}

	/**
	 * Forges a new instance of Arr_Validator. If an instance with $name is already loaded it will
	 * replace it.
	 *
	 * @param string $name optional the Arr_Validator instance identifier. If non given a numeric index
	 * will be given as name.
	 * @return Arr_Validator the forged validator.
	 */
	public static function forge($name = '')
	{
		$instance = new static();

		if($name == '')
		{
			static::$_instances[] = $instance;
		}
		else
		{
			static::$_instances[$name] = $instance;
		}

		// Set default instance
		if(static::$_default === null)
		{
			static::$_default = $instance;
		}

		return $instance;
	}

	/**
	 * Verifies if the named Arr_Validator instance already exists.
	 *
	 * @param string $name the name of the Arr_Validator to check for.
	 * @return bool whether the named Arr_Validator instance exists or not.
	 */
	public static function exists($name)
	{
		$name = (($name != '') ? $name : 0);
		return \Arr::key_exists(static::$_instances, $name);
	}

	/**
	 * Gets the instance by name. Returns the default if no name is given. If no instance is found a new
	 * one will be forged using the $name param.
	 *
	 * @param string $name optional the Arr_Validator instance identifier.
	 * @return Arr_Validator the existing isntance or a newly forged one.
	 * TODO: verify if this works
	 */
	public static function instance($name = '')
	{
		if(empty(static::$_instances))
		{
			return static::forge($name);
		}

		if($name == '')
		{
			if(static::$_default == null)
			{
				return static::forge($name);
			}
			else
			{
				return static::$_default;
			}
		}

		if(!static::exists($name))
		{
			return static::forge($name);
		}

		return \Arr::get(static::$_instances, $name);
	}

	/**
	 * Gets all the Arr_Validator instances.
	 *
	 * @return array of Arr_Validator objects
	 */
	public static function instances()
	{
		return static::$_instances;
	}

	/**
	 * Adds a new node to the validator.
	 * TODO: finish this!
	 *
	 * @return Arr_Validator_Node the newly created node.
	 */
	public function add_node($key, $default = null, $default_rule_set = false)
	{
		$return = $key;

		if(is_string($return))
		{
			$return = Arr_Validator_Node::forge($key, $default, $default_rule_set);
		}
		elseif(!is_object($return) || !($return instanceof Arr_Validator_Node))
		{
			// TODO: throw Exception: Invalid arguments
			//throw new InvalidArgumentException("");
		}

		// We are not using \Arr::set() here because we want the whole key string to be the node key
		$this->_nodes[$key] = $return;

		return $return;
	}

	/**
	 * Adds an array of nodes to the Arr_Validator instance.
	 *
	 * TODO: finish this!
	 */
	public function add_nodes(array $nodes)
	{
		foreach($nodes as $node)
		{
			// TODO: add each node
			// $this->add_node();
		}
	}

	/**
	 * Remove a node from the Arr_Validator instance.
	 * TODO: finish this!
	 */
	public function remove_node()
	{

	}

	/**
	 * Loads an Arr_Validator isntance from file.
	 */
	public function load_from_file($file)
	{

	}

}
