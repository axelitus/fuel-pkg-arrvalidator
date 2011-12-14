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
	 * @var Holds the currently loaded configuration.
	 */
	static protected $_config = array();

	// @formatter:off
	/**
	 * @var array Contains the default config values.
	 */
	static protected $_config_default = array( 
		'groups' => array(
		),
		'auto_load' => array(
			'validators' => array(
			),
			'groups' => array(
			)
		)
	);
	// @formatter:on

	/**
	 * @var array Holds the different ArrValidator instances.
	 */
	static protected $_instances = array();

	/**
	 * @var string Holds the name of the instance.
	 */
	protected $_name = '';

	/**
	 * @var array Holds the validator nodes for an instance.
	 */
	protected $_nodes = array();

	public static function _init()
	{
		static::$_config = \Arr::merge(static::$_config_default, \Config::load('arrvalidator'));

		// Load the groups of validators first.
		static::from_group(static::$_config['auto_load']['groups']);

		// Load the validators. The single validators take precedence, the previously loaded validators (from
		// groups) will be overwritten.
		foreach (static::$_config['auto_load']['validators'] as $validator)
		{
			static::from_file($validator);
		}
	}

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
	 * Forges a new instance of ArrValidator or gets the existing one. If the $overwrite flag is set to
	 * true, then the instance will be overwritten.
	 *
	 * @param string $name the ArrValidator instance identifier.
	 * @param bool $overwrite optional flag to force the existing instance to be overwritten if exists.
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
		// We don't use \Arr::key_exists() because we don't have a multi-dimensional array
		return array_key_exists($name, static::$_instances);
	}

	/**
	 * Gets an instance by name. If no instance is found by the given name, a new one will be forged.
	 *
	 * @param string $name optional the ArrValidator instance identifier.
	 * @return ArrValidator the existing instance or a newly forged one.
	 */
	public static function instance($name)
	{
		// If the instance we want already exists return it
		if (($return = ((static::exists($name)) ? static::$_instances[$name] : null)) !== null)
		{
			return $return;
		}

		// Create the new instance and store it in the instances array
		$return = new static($name);

		// We don't use \Arr::set() because we don't want multi-dimensional array
		static::$_instances[$name] = $return;

		return $return;
	}

	/**
	 * Removes a loaded instance.
	 *
	 * @param string $name the ArrValidator instance identifier.
	 * @return void
	 */
	public static function remove($name)
	{
		if ( ! empty(static::$_instances))
		{
			// We don't use \Arr::delete() because we don't have a multi-dimensional array
			unset(static::$_instances[$name]);
		}
	}

	/**
	 * Gets all the validator instances.
	 *
	 * @return array of ArrValidator objects.
	 */
	public static function instances()
	{
		return static::$_instances;
	}

	/**
	 * Deletes all loaded instances.
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
	 * Adds a node if it does not exist. If the node already exists it will be returned.
	 * If the $overwrite flag is set to true, then the existing node will be overwritten.
	 *
	 * @param string $name the node's identifier as a dot-separated key name
	 * @param mixed $default optional the node's default value.
	 * @param bool overwrite optional flag to force overwritting the node.
	 * @param array $rules optional an array of rules in the format:
	 * array(array('operator' => string, ['operand' => mixed]))
	 * @return ArrValidator_Node the added or previously existing node.
	 */
	public function add_node($name, $default, $overwrite = false, array $rules = array())
	{
		if ($overwrite || ! $this->has_node($name))
		{
			$node = ArrValidator_Node::forge($default);
			$node->add_rules($rules);

			return $this->add_node_object($name, $node);
		}
		else
		{
			return $this->_nodes[$name];
		}
	}

	/**
	 * Adds a node from an object if it does not exist. If the node already exists it will be returned.
	 * If the $overwrite flag is set to true, then the existing node will be overwritten.
	 *
	 * @param string $name the node's identifier as a dot-separated key name
	 * @param ArrValidator_Node $node the node object to be added.
	 * @param bool overwrite optional flag to force overwritting the node.
	 * @return ArrValidator_Node the added or previously existing node.
	 */
	public function add_node_object($name, ArrValidator_Node $node, $overwrite = false)
	{
		if ($overwrite || ! $this->has_node($name))
		{
			$this->_nodes[$name] = $node;
		}

		return $this->_nodes[$name];
	}

	/**
	 * Adds a node from an array if it does not exist. If the node already exists it will be returned.
	 * If the $overwrite flag is set to true, then the existing node will be overwritten.
	 *
	 * @param string $name the node's identifier as a dot-separated key name
	 * @param array $array the node's array structure to be added.
	 * @param bool overwrite optional flag to force overwritting the node.
	 * @return ArrValidator_Node the added or previously existing node.
	 */
	public function add_node_array($name, array $array, $overwrite = false)
	{
		if ($overwrite || ! $this->has_node($name))
		{
			$node = ArrValidator_Node::from_array($array);

			return $this->add_node_object($name, $node);
		}
		else
		{
			return $this->_nodes[$name];
		}
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
		$return = array_key_exists($name, $this->_nodes);

		return $return;
	}

	/**
	 * Gets a specific node from the node's array or if not exists $default.
	 *
	 * @return ArrValidator_Node|mixed the existing node or $default value.
	 */
	public function get_node($name, $default = null)
	{
		if ($this->has_node($name))
		{
			return $this->_nodes[$name];
		}

		return $default;
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
	 * Adds an array of nodes to the validator. The array can contain ArrValidator_Node objects or
	 * ArrValidator_Node array structures. The nodes must be identified by a key name.
	 *
	 * @param array $nodes array of node array structures indexed by node name.
	 * @param bool $overwrite optional whether the previously existing nodes should be overwritten.
	 * @return Arr_Validator this instance for chaining.
	 */
	public function add_nodes(array $nodes, $overwrite = false)
	{
		foreach ($nodes as $key => $node)
		{
			if ($node instanceof ArrValidator_Node)
			{
				$this->add_node_object($key, $node, $overwrite);
			}
			elseif (is_array($node))
			{
				$this->add_node_array($key, $node, $overwrite);
			}
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
	 * Gets the instance's array structure.
	 *
	 * @param string $ommit_name optional whether to ommit the 'name' item in the array.
	 * @return array the validator's array structure.
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
	 * Forges an instance from an array structure. The 'name' item should not be ommited using this
	 * method or else you'll get a validator with an empty string as a name (and so it is identified by
	 * it in the instances array).
	 *
	 * @param array $array the validator's array structure to forge an instance from.
	 * @param bool $overwrite optional flag to force the existing validator to be overwritten if it
	 * exist.
	 * @return ArrValidator the forged instance.
	 */
	public static function from_array(array $array, $overwrite = false)
	{
		$return = static::forge(\Arr::get($array, 'name', ''), $overwrite);

		if (($nodes = \Arr::get($array, 'nodes')) !== null)
		{
			foreach ($nodes as $key => $node)
			{
				$return->add_node_array($key, $node, true);
			}
		}

		return $return;
	}

	/**
	 * Gets an array of all loaded ArrValidator instances as their array structures.
	 *
	 * @param string $ommit_name optional whether to ommit the 'name' item in the arrays, as the
	 * individual validator array structures are named.
	 * @return array of ArrValidator object array structures
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
	 * Loads multiple instances from an array of validator array structures. If the flag $empty_first is
	 * set to true, then the instances array will be emptied prior to the load process.
	 *
	 * Note:
	 * The 'name' item inside a validator takes precedence to the validator's key name.
	 * array(
	 *     'validator_index' => array(         // <- If 'name' item is not present, this will be used
	 *         'name' => 'validator_name',     // <- Takes precedence
	 *         'nodes' => array(
	 *           ...
	 *         )
	 *     )
	 * )
	 *
	 * @param bool $overwrite optional flag to force the existing validators to be overwritten if they
	 * exist.
	 * @param bool $empty_first optional whether to empty the instances array first or not.
	 * @return array of loaded ArrValidator objects.
	 */
	public static function multiple_from_array(array $array, $overwrite = false, $empty_first = false)
	{
		$return = array();

		if ($empty_first)
		{
			static::empty_instances();
			$overwrite = true;
		}

		foreach ($array as $key => $validator)
		{
			// Get the name from the key as a fallback if no 'name' item is inside the validator array
			if ( ! \Arr::key_exists($validator, 'name'))
			{
				$validator['name'] = $key;
			}
			$validator = ArrValidator::from_array($validator, $overwrite);
			$return[$validator->get_name()] = $validator;
		}

		return $return;
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

					\Log::info('ArrValidator\ArrValidator::run - The node \''.$key.'\' does not exist in the given array and the rules failed to comply. As the force_set flag is set to true, the node was created in the given array with the default value.');
				}
			}
			elseif ($validation === false)
			{
				// A rule has failed for the node but the item exists, set the default value without further check!
				\Arr::set($array, $key, $node->get_default());

				\Log::info('ArrValidator\ArrValidator::run - The node \''.$key.'\' failed to comply all rules. The default value was set in the given array.');
			}
		}
	}

	/**
	 * Loads a single or multiple validators from a file.
	 *
	 * @param mixed $file string file | config array | Config_Interface instance
	 * @param bool $overwrite optional flag to force the existing validators to be overwritten if they
	 * exist.
	 * @return ArrValidator|array|bool the loaded validator or an array of loaded ArrValidator objects or
	 * false if $file couldn't be loaded.
	 */
	public static function from_file($file, $overwrite = false)
	{
		$array = \Config::load($file);
		if ( ! empty($array))
		{
			// Is it a single validator or multiple validators? If a 'name' item exists or a 'nodes' item exists
			// treat it as a single validator, if not there must be more validators, load multiple.
			if (\Arr::key_exists($array, 'name') || \Arr::key_exists($array, 'nodes'))
			{
				// It's a single validator
				return static::from_array($array, $overwrite);
			}
			else
			{
				// There are multiple validators
				return static::multiple_from_array($array, $overwrite);
			}
		}

		return false;
	}

	/**
	 * Loads a validators from a group (from the configuration file).
	 *
	 * @param string|array $group a group's name or an array containing group's name to be loaded.
	 * @param bool $overwrite optional flag to force the existing validators to be overwritten if they
	 * exist.
	 * @return bool true if at least one file was loaded.
	 */
	public static function from_group($groups, $overwrite = false)
	{
		$return = false;

		// One group
		if (is_string($groups))
		{
			// Get group's validators
			$validators = \Arr::get(static::$_config, 'groups.'.$groups, array());
			if (is_string($validators))
			{
				// Arrayify the validator if a comma-separated validators list was given
				$validators = array_map('trim', explode(',', $validators));
			}

			// Load the group's validators
			foreach ($validators as $validator)
			{
				if (static::from_file($validator, $overwrite))
				{
					$return = true;
				}
			}
		}
		// Multiple groups
		elseif (is_array($groups))
		{
			// Loop through each group
			foreach ($groups as $group)
			{
				if (static::from_group($group, $overwrite))
				{
					$return = true;
				}
			}
		}

		return $return;
	}

}
