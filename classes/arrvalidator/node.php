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
 * ArrValidator_Node
 *
 * @package     Fuel
 * @subpackage  ArrValidator
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-arrvalidator
 */
class ArrValidator_Node
{
	/**
	 * @var mixed Holds the default value to be set if a rule fails.
	 */
	protected $_default;

	/**
	 * @var array Holds the rules to be applied to the node.
	 */
	protected $_rules = array();

	/**
	 * Prevent direct instantiation
	 */
	protected function __construct($default)
	{
		$this->_default = $default;
	}

	/**
	 * Forges a new instance of ArrValidator_Node.
	 *
	 * @param mixed $default the default value to be set if a rule fails.
	 * @return ArrValidator_Node the forged instance.
	 */
	public static function forge($default)
	{
		$return = new static($default);

		return $return;
	}

	/**
	 * Gets the node's default value.
	 *
	 * @return mixed the node's default value.
	 */
	public function get_default()
	{
		return $this->_default;
	}

	/**
	 * Sets the node's default value.
	 *
	 * @param mixed the new node's default value.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function set_default($value)
	{
		$this->_default = $value;

		return $this;
	}

	/**
	 * Gets the node's rules array.
	 *
	 * @return array of rules.
	 */
	public function get_rules()
	{
		return $this->_rules;
	}

	/**
	 * Gets a rule from the array if exists or $default if not. The rule's indexes are zero-based.
	 *
	 * @param int $index of the rule to retrieve.
	 * @param mixed $default optional a default value if the rule is not found.
	 * @return array a rule in the form: array('operator' => string, ['operand' => mixed]).
	 */
	public function get_rule($index, $default = null)
	{
		return \Arr::get($this->_rules, $index, $default);
	}

	/**
	 * Gets a properly formatted array to be inserted into the node's rules array.
	 *
	 * @param string|array $operator a supported operator for the rule or the rule's array
	 * representation.
	 * @param mixed $operand optional the secondary operand(s) needed by the operator.
	 * @return array the rules array representation.
	 */
	protected static function _prepare_rule($operator, $operand = null)
	{
		$return = null;

		// Did we receive the rule as an array? If so then break it to get only the values that matter
		if (is_array($operator))
		{
			$operand = \Arr::get($operator, 'operand');
			$operator = \Arr::get($operator, 'operator');
		}

		// Is the operator supported?
		if ( ! ArrValidator_Operator::is_supported($operator))
		{
			throw new \InvalidArgumentException('The given operator \''.$operator.'\' is not a supported operator.');
			return $return;
		}

		// @formatter:off
		$return = array(
			'operator' => $operator,
		);
		// @formatter:on

		// If the operator needs additional operands then include them in the rule
		if ($operand !== null && ArrValidator_Operator::operand_count($operator) > 1)
		{
			$return['operand'] = $operand;
		}

		return $return;
	}

	/**
	 * Inserts a rule at the specified position in the node's rules array. The operand(s) is(are) not
	 * validated.
	 *
	 * @param int $pos the numeric position at which to insert, negative to count from the end backwards.
	 * @param string|array $operator a supported operator for the rule or the rule's array
	 * representation.
	 * @param mixed $operand optional the secondary operand(s) needed by the operator.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function insert_rule($pos, $operator, $operand = null)
	{
		$rule = static::_prepare_rule($operator, $operand);

		// Add the rule
		\Arr::insert($this->_rules, array($rule), $pos);

		return $this;
	}

	/**
	 * Prepends the node's rules array with a rule. The operand(s) is(are) not validated.
	 *
	 * @param string|array $operator a supported operator for the rule or the rule's array
	 * representation.
	 * @param mixed $operand optional the secondary operand(s) needed by the operator.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function prepend_rule($operator, $operand = null)
	{
		return $this->insert_rule(0, $operator, $operand);
	}

	/**
	 * Adds a rule to to the end of the node's rules array. The operand(s) is(are) not validated.
	 *
	 * @param string|array $operator a supported operator for the rule or the rule's array
	 * representation.
	 * @param mixed $operand optional the secondary operand(s) needed by the operator.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function add_rule($operator, $operand = null)
	{
		return $this->insert_rule(count($this->_rules), $operator, $operand);
	}

	/**
	 * Removes a rule from the node. The rule's indexes are zero-based.
	 *
	 * @param int $index the index of the rule to remove.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function remove_rule($index)
	{
		if (isset($this->_rules[$index]))
		{
			// Unset the rule form the array
			unset($this->_rules[$index]);

			if ($index < count($this->_rules))
			{
				// re-index array only if we removed an item and it was not the last
				$this->_rules = array_values($this->_rules);
			}
		}

		return $this;
	}

	/**
	 * Inserts a rule at the specified position in the node's rules array. The operand(s) is(are) not
	 * validated.
	 *
	 * @param int $pos the numeric position at which to insert, negative to count from the end backwards.
	 * @param string|array $operator a supported operator for the rule or the rule's array
	 * representation.
	 * @param mixed $operand optional the secondary operand(s) needed by the operator.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function insert_rules($pos, array $rules)
	{
		// Prepare the rules
		$prep_rules = array();
		foreach ($rules as $rule)
		{
			$prep_rules[] = static::_prepare_rule($rule);
		}

		// Add the rules
		\Arr::insert($this->_rules, $prep_rules, $pos);

		return $this;
	}

	/**
	 * Prepends the node's rules array with an array of rules in the format:
	 * array(array('operator' => string, ['operand' => mixed]), array('operator' => string...))
	 *
	 * @param array $rules an array of rules in their array representations.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function prepend_rules(array $rules)
	{
		return $this->insert_rules(0, $rules);
	}

	/**
	 * Adds to the end of the node's rules array an array of rules in the format:
	 * array(array('operator' => string, ['operand' => mixed]), array('operator' => string...))
	 *
	 * @param array $rules an array of rules in their array representations.
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function add_rules(array $rules)
	{
		return $this->insert_rules(count($this->_rules), $rules);
	}

	/**
	 * Empties the node's rules array.
	 *
	 * @return ArrValidator_Node this instance for chaining.
	 */
	public function empty_rules()
	{
		$this->_rules = array();

		return $this;
	}

	/**
	 * Gets the instance's array representation.
	 *
	 * @return array the node's array representation.
	 */
	public function as_array()
	{
		// @formatter:off
		$return = array(
			'default' => $this->_default,
			'rules' => $this->_rules
		);
		// @formatter:on

		return $return;
	}

	/**
	 * Forges an instance from an array representation.
	 *
	 * @param array $array the node's array representation to forge an instance from.
	 * @return ArrValidator_Node the forged instance.
	 */
	public static function from_array(array $array)
	{
		$return = static::forge(\Arr::get($array, 'default'));

		// Add the rules
		$return->add_rules(\Arr::get($array, 'rules', array()));

		return $return;
	}

	protected static function _operand_to_str($operand)
	{
		ob_start();
		var_dump($operand);
		$return = trim(ob_get_contents());
		ob_end_clean();

		return $return;
	}

	/**
	 * Validates the node. The rules are verified in the order there were added. When the first rule
	 * fails to comply, the validation is interrupted, the subsequent rules will not be verified and the
	 * given array's item will be set to the node's default.
	 *
	 * Special cases:
	 *
	 *
	 * @return bool|null true only when the given array complies with all node's rules, null is returned
	 * when the item does not exist in the array.
	 */
	public function validate(array &$array, $key)
	{
		if ( ! empty($this->_rules))
		{
			// Get the item (or null) and if it exists or not
			$item = \Arr::get($array, $key);
			$item_exists = \Arr::key_exists($array, $key);

			// Loop through each rule
			foreach ($this->_rules as $rule)
			{
				if ($item_exists)
				{
					// Get the operand(s) if it(they) exist(s)
					$operand = \Arr::get($rule, 'operand');

					// Apply operator. If the result is false then set the return value accordingly and break the loop.
					if (ArrValidator_Operator::apply($item, $rule['operator'], $operand) === false)
					{
						\Log::info('The operator \''.$rule['operator'].'\' failed for key \''.$key.'\' with secondary operand '.static::_operand_to_str($operand).'.');
						return false;
					}
				}
				else
				{
					// Only if the operator is different than NOT_IS_SET set the return to false and break the loop.
					if ($rule['operator'] != ArrValidator_Operator::NOT_IS_SET)
					{
						\Log::info('The operator \''.$rule['operator'].'\' failed for key \''.$key.'\' with secondary operand '.static::_operand_to_str($operand).'.');
						return null;
					}
				}
			}
		}

		return $true;
	}

}
