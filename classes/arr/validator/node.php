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
 * Arr_Validator_Operator
 *
 * @package     Fuel
 * @subpackage  Arr Validator
 */
class Arr_Validator_Node
{
	/**
	 * @var string Contains the key of the validator node.
	 */
	protected $_key = '';

	/**
	 * @var mixed Contains the default value for the validator node (in case a rule fails).
	 */
	protected $_default = null;

	/**
	 * @var Arr_Validator_Rule_Group Contains the rules for the validator node.
	 */
	protected $_rules = null;

	/**
	 * Prevent direct instantiation.
	 *
	 * @param string $key the key for the validator node.
	 * @param mixed $default optional the default value for the validator node.
	 */
	protected function __construct($key, $default = null)
	{
		if(!is_string($key))
		{
			throw new InvalidArgumentException("The param '\$key' must be a dot-notated array key string.");
		}

		$this->_key = $key;
		$this->_default = $default;
		$this->_rules = Arr_Validator_Rule_Group::forge();
	}

	/**
	 * Forges a new instance of a validator node. The default rule set includes only an IS_SET rule.
	 *
	 * @param string $key the key for the validator node.
	 * @param mixed $default optional the default value for the validator node.
	 * @param bool $default_rule_set optional whether or not to create a default rule set for the node.
	 * @return Arr_Validator_Node
	 */
	public static function forge($key, $default = null, $default_rule_set = true)
	{
		$return = new static($key, $default);

		if($default_rule_set)
		{
			$return->_rules->add_rule(Arr_Validator_Operator::IS_SET);
		}

		return $return;
	}

	/**
	 * Gets the key for the validator node.
	 *
	 * @return string the node's key.
	 */
	public function get_key()
	{
		return $this->_key;
	}

	/**
	 * Gets the default value for the validator node.
	 *
	 * @return mixed the node's default value.
	 */
	public function get_default()
	{
		return $this->_default;
	}

	/**
	 * Gets the root Arr_Validator_Rule_Group as reference to add/remove the node rules as needed.
	 *
	 * @return Arr_Validator_Rule_Group the rules for the node.
	 */
	public function & get_rules()
	{
		return $this->_rules;
	}

	/**
	 * Sets the root Arr_Validator_Rule_Group for the node.
	 *
	 * @param Arr_Validator_Rule_Group $rules an instance of Arr_Validator_Rule_Group.
	 * @return void
	 */
	public function set_rules(Arr_Validator_Rule_Group $rules)
	{
		$this->_rules = $rules;
	}

	/**
	 * Returns the node as it's array representation.
	 *
	 * @return array validator node array representation.
	 */
	public function as_array()
	{
		// @formatter:off
		$return = array(
			'key' => $this->_key,
			'default' => $this->_default,
			'rules' => $this->_rules->as_array()
		);
		// @formatter:on

		return $return;
	}

	/**
	 * Forges a validator node instance from it's array representation.
	 *
	 * @param array $node the node's array representation.
	 * @return Arr_Validator_Node
	 */
	public static function from_array(array $node)
	{
		if(!isset($node['key']))
		{
			throw new InvalidArgumentException("The node array 'key' item must be set.");
		}

		$default = (isset($node['default']) ? $node['default'] : null);
		$rules = (isset($node['rules']) ? $node['rules'] : array());

		$return = static::forge($node['key'], $default, false);
		$return->set_rules(Arr_Validator_Rule_Group::from_array($rules));

		return $return;
	}

	/**
	 * Evaluates the array against the validator node rules. If a rule fails for the array item specified
	 * by the node's key then the array's item will be set to the default value and the evaluation of
	 * subsequent rules will be stopped. (Please refer to the Arr_Validator_Node::add_rule() method to
	 * understand how the IS_SET and NOT_IS_SET operator rules are handled as this are special).
	 *
	 * Note: All rules are evaluated array->value centric.
	 *
	 * The rule that fails will be logged as info.
	 *
	 * @param array $array the array reference to where the rules are to be be evaluated to.
	 * @see Arr_Validator_Node::add_rule()
	 * @see Arr_Validator_Node::_evaluate_rule()
	 * TODO: make this function return an Arr_Validator_Result object with info about the node's
	 * evaluation.
	 */
	public function evaluate(array &$array)
	{
		// TODO: Fix method to not differentiate if is_set is first or not. This is code from first version,
		// get rid of it!
		$rules = $this->_rules->get_rules();
		if(!empty($rules))
		{
			// Verify if the key exists
			$key_exists = \Arr::key_exists($array, $this->_key);

			// Handle the first rule (for the is_set, not_is_set rules)
			if(($first_rule = reset($rules)) !== false)
			{
				if($first_rule->operator == Arr_Validator_Operator::IS_SET)
				{
					// Get rid of the first rule as we are already evaluating it
					$rules = array_slice($rules, 1, null, true);

					// Evaluate the IS_SET rule
					if(!$key_exists)
					{
						// Set item's value to the default value
						\Arr::set($array, $this->_key, $this->_default);

						// Log the failed rule
						\Log::info("Arr\\Arr_Validator_Node::evaluate() - The following rule failed: [operator: {$first_rule->operator}, operand: {$first_rule->operand}] in node [{$this->_key}] for the given array. The default value [{$this->_default}] has been set. No further rules will be evaluated for this node.");

						// Stop further rule evaluation
						return;
					}
				}
				elseif($first_rule->operator == Arr_Validator_Operator::NOT_IS_SET)
				{
					// Get rid of the first rule as we are already evaluating it
					$rules = array_slice($rules, 1, null, true);

					// Evaluate the IS_SET rule
					if($key_exists)
					{
						// Set item's value to the default value
						\Arr::set($array, $this->_key, $this->_default);

						// Log the failed rule
						\Log::info("Arr\\Arr_Validator_Node::evaluate() - The following rule failed: [operator: {$first_rule->operator}, operand: {$first_rule->operand}] in node [{$this->_key}] for the given array. The default value [{$this->_default}] has been set. No further rules will be evaluated for this node.");

						// Stop further rule evaluation for this node
						return;
					}
				}
			}

			if($key_exists)
			{
				// Get the array's value to evaluate
				$value = \Arr::get($array, $this->_key);

				// Generate a new rules group with the remaining rules
				$rules = Arr_Validator_Rule_Group::forge()->add($rules);

				// If the value does not pass the evaluation then set it's default value
				if(!$rules->evaluate($value))
				{
					// Set item's value to the default value
					\Arr::set($array, $this->_key, $this->_default);

					// Log the failed eval result.
					\Log::info("Arr\\Arr_Validator_Node::evaluate() - The rule evaluation failed in node [{$this->_key}] for the given array. The default value [{$this->_default}] has been set instead of the original value [{$value}].");

					return;
				}
			}
		}
	}

}
