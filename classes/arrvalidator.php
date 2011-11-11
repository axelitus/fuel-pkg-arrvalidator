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

namespace ArrValidator;

/**
 * ArrValidator
 *
 * @package     Fuel
 * @subpackage  ArrValidator
 * @version     1.0
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-arrvalidator
 */
class ArrValidator
{
	/**
	 * @var string The version of the ArrValidator package.
	 */
	const VERSION = '1.0';

	/**
	 * @var array Holds the different ArrValidator instances.
	 */
	static public $_instances = array();

	/**
	 * @var ArrValidator the default validator (the first that is loaded or the one that is manually set
	 * as default).
	 */
	static protected $_default = null;

	/**
	 * @var string Holds the name of the instance.
	 */
	protected $_name = '';

	/**
	 * @var array Holds the validator nodes for an instance.
	 */
	protected $_nodes = array();

	/**
	 * Prevent direct instantiation.
	 *
	 * @return void
	 */
	protected function __construct($name)
	{
		$this->_name = $name;
	}

	/**
	 * Forges a new instance of ArrValidator, gets the existing. If the $overwrite flag is set to true,
	 * then the instance will be overwritten.
	 *
	 * @param string $name the ArrValidator instance identifier.
	 * @param bool $overwrite optional flag to force the existing instance to be overwriting if exists.
	 * @return ArrValidator the forged validator.
	 */
	public static function forge($name, $overwrite = false)
	{
		// If we set the overwrite flag get rid of the instance if it already exists
		if ($overwrite)
		{
			// We don't use \Arr::remove() because we don't have multi-dimensional array
			unset(static::$_instances[$name]);
		}

		return static::instance($name);
	}

	/**
	 * Verifies if the named ArrValidator instance already exists.
	 *
	 * @param string $name the name of the ArrValidator to check for.
	 * @return bool whether the named ArrValidator instance exists or not.
	 */
	public static function exists($name)
	{
		return \Arr::key_exists(static::$_instances, $name);
	}

	/**
	 * Gets an instance by name. Returns the default instance if the given name is an empty string. If no
	 * instance is found by the given name, a new one will be forged.
	 *
	 * @param string $name optional the ArrValidator instance identifier.
	 * @return ArrValidator the existing instance or a newly forged one.
	 */
	public static function instance($name = '')
	{
		// Do we want the default instance?
		if ($name == '')
		{
			// Verify if a default instance exists and return it
			if (static::$_default !== null)
			{
				return static::$_default;
			}
		}
		// If the instance we want already exists return it
		elseif (($return = \Arr::get(static::$_instances, $name, null)) !== null)
		{
			return $return;
		}

		// Create the new instance and store it in the instances array
		$return = new static($name);

		// We don't use \Arr::set() because we don't want multi-dimensional array
		static::$_instances[$name] = $return;

		// Set the default instance as needed
		if (static::$_default == null)
		{
			static::$_default = $return;
		}

		return $return;
	}

	/**
	 * Removes a loaded instance. If the removed instance was the default then we fetch another one (the
	 * first instance of the array).
	 *
	 * @param string $name the ArrValidator instance identifier.
	 * @return void
	 */
	public static function remove($name)
	{
		if ( ! empty(static::$_instances))
		{
			$was_default = ($name == static::$_default->get_name());
			\Arr::delete(static::$_instances, $name);

			// If the deleted instance was the default get us a new one
			if ($was_default)
			{
				static::$_default = reset(static::$_instances);
			}
		}
	}

	/**
	 * Gets all the validator instances.
	 *
	 * @return array of ArrValidator instances.
	 */
	public static function instances()
	{
		return static::$_instances;
	}

	/**
	 * Deletes all loaded instances
	 *
	 * @return void
	 */
	public static function empty_instances()
	{
		static::$_instances = array();
	}

	/**
	 * Gets the validator's name.
	 *
	 * @return string this instance name.
	 */
	public function get_name()
	{
		return $this->_name;
	}

	/**
	 * Gets the validator's nodes.
	 *
	 * @return array of ArrValidator_Node objects.
	 */
	public function get_nodes()
	{
		return $this->_nodes;
	}

	/**
	 * Adds a node only if it does not exist. If the node already exists it will be returned.
	 * If the $overwrite flag is set to true, then the existing node will be overwritten.
	 *
	 * @param string $name the node's identifier as a dot-separated key name, an ArrValidator_Node object
	 * or an ArrValidator_Node array representation.
	 * @param mixed $default the node's default value.
	 * @param bool overwrite optional flag to force overwritting the node.
	 * @param array $rules optional an array of rules in the format:
	 * array(array('operator' => string, ['operand' => mixed]))
	 * @return ArrValidator_Node the added or previously existing node.
	 */
	public function add_node($name, $default, $overwrite = false, array $rules = array())
	{
		// TODO: Accept objects and array reps
		if ($overwrite || ! $this->has_node($name))
		{
			if (is_object($default) && $default instanceof ArrValidator_Node)
			{
				$this->_nodes[$name] = $default;
			}
			else
			{
				$this->_nodes[$name] = ArrValidator_Node::forge($default);
				$this->_nodes[$name]->add_rules($rules);
			}
		}

		return $this->_nodes[$name];
	}

	/**
	 * Verifies if the node already exists in the validator. This only checks if the key exists in the
	 * node's array.
	 *
	 * @param string $name the node's identifier as a dot-separated key name.
	 * @return bool whether the validator has the node.
	 */
	public function has_node($name)
	{
		$return = \Arr::key_exists($this->_nodes, $name);

		return $return;
	}

	/**
	 * Removes a node from the validator.
	 *
	 * @param string $name the node's identifier as a dot-separated key name.
	 * @return ArrValidator this instance for chaining.
	 */
	public function remove_node($name)
	{
		// We don't use \Arr::remove() because we don't want multi-dimensional array
		unset($this->_nodes[$name]);

		return $this;
	}

	/**
	 * Adds an array of nodes to the ArrValidator instance.
	 * The array can contain ArrValidator_Node objects or ArrValidator_Node array representations.
	 *
	 * @return Arr_Validator this instance for chaining.
	 */
	public function add_nodes(array $nodes)
	{
		// TODO: FIX
		foreach ($nodes as $node)
		{
			$this->add_node($node);
		}

		return $this;
	}

	/**
	 * Empties the validator node's array.
	 *
	 * @return ArrValidator this instance for chaining.
	 */
	public function empty_nodes()
	{
		unset($this->_nodes);
		$this->_nodes = array();

		return $this;
	}

	/**
	 * Gets the instance's array representation.
	 *
	 * @param string $ommit_name optional whether to ommit the 'name' item in the array.
	 * @return array the validator's array representation.
	 */
	public function as_array($ommit_name = false)
	{
		if ( ! $ommit_name)
		{
			$return['name'] = $this->_name;
		}

		$return['nodes'] = array();
		foreach ($this->_nodes as $key => $node)
		{
			$return['nodes'][$key] = $node->as_array();
		}

		return $return;
	}

	/**
	 * Forges an instance from an array representation. The 'name' item cannot be ommited using this
	 * method or else you'll get only one validator with an empty string as a name.
	 *
	 * @param array $array the validator's array representation to forge an instance from.
	 * @return ArrValidator the forged instance.
	 */
	public static function from_array(array $array)
	{
		$return = static::forge(\Arr::get($array, 'name', ''));

		if (($nodes = \Arr::get($array, 'nodes')) !== null)
		{
			foreach ($nodes as $key => $node)
			{
				$return->add_node($key, ArrValidator_Node::from_array($node));
			}
		}

		return $return;
	}

	/**
	 * Gets an array of all loaded ArrValidator instances as their array representations.
	 *
	 * @param string $ommit_name optional whether to ommit the 'name' item in the arrays.
	 * @return array of ArrValidator instances array representations
	 */
	public static function all_as_array($ommit_name = false)
	{
		$return = array();

		foreach (static::$_instances as $key => $validator)
		{
			$return[$key] = $validator->as_array($ommit_name);
		}

		return $return;
	}

	/**
	 * Loads multiple instances from an array of validators. If the flag $empty_first is set to true,
	 * then the instances array will be emptied prior to the load process.
	 *
	 * Note:
	 * The 'name' item inside a validator takes precedence to the validator's index.
	 * array(
	 *     'validator_index' => array(         // <- If 'name' item is not present, this will be used
	 *         'name' => 'validator_name',     // <- Takes precedence
	 *         'nodes' => array(
	 *           ...
	 *         )
	 *     )
	 * )
	 *
	 * @param bool $empty_first optional whether to empty the instances array first or not.
	 * @return void
	 */
	public static function multiple_from_array(array $array, $empty_first = false)
	{
		if ($empty_first)
		{
			static::empty_instances();
		}

		foreach ($array as $key => $validator)
		{
			// Get the name from the key as a fallback if no 'name' item is in validator array
			if ( ! \Arr::key_exists($validator, 'name'))
			{
				$validator['name'] = $key;
			}
			$validator = ArrValidator::from_array($validator);

			// We don't use \Arr::set() because we don't want multi-dimensional array
			static::$_instances[$validator->get_name()] = $validator;
		}
	}

	/**
	 * Runs the validator. Every node will be checked against the given array and if the node's
	 * validation fails the node's default value will be set into the array. If the item is not found and
	 * the $force_item_set flag is set to true then the item will be created with the default value.
	 *
	 * @param array $array the array to run the validator through.
	 * @param bool $force_item_set optional flag to force the creation of the item in the given array.
	 * @return void
	 */
	public function run(array &$array, $force_item_set = true)
	{
		foreach ($this->_nodes as $key => $node)
		{
			$validation = $node->validate($array, $key);
			if ($validation === null)
			{
				// The item does not exist and the rules didn't comply
				if ($force_item_set)
				{
					// A rule has failed for the node, set the default value!
					\Arr::set($array, $key, $node->get_default());

					\Log::info('The node \''.$key.'\' does not exist in the given array and the rules failed to comply. As the force_set flag is set to true, the node was created in the given array with the default value.');
				}
			}
			elseif ($validation === false)
			{
				// A rule has failed for the node, set the default value!
				\Arr::set($array, $key, $node->get_default());

				\Log::info('The node \''.$key.'\' failed to comply all rules. The default value was set in the given array.');
			}
		}
	}

}
