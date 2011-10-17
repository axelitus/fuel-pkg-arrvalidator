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
 * Arr_Validator_Rule_Group
 *
 * @package     Fuel
 * @subpackage  Arr Validator
 */
class Arr_Validator_Rule_Group
{
	/**
	 * The group's glue method.
	 *
	 * @var string Arr_Validator_Glue:: constant that specifies the glue method to be used for the group.
	 */
	protected $_glue = Arr_Validator_Glue::_AND;

	/**
	 * The group's rules.
	 *
	 * @var array Holds the rules or group of rules contained in this group.
	 */
	protected $_rules = array();

	/**
	 * Prevent direct instantiation
	 *
	 * @param string $glue optional Arr_Validator_Glue:: constant that specifies the glue method.
	 * @return void
	 */
	protected function __construct($glue = Arr_Validator_Glue::_AND)
	{
		if(!Arr_Validator_Glue::is_glue($glue))
		{
			throw new \InvalidArgumentException("The given glue [{$glue}] is not a supported glue. Please refer to the documentation for supported glues.");
		}

		$this->_glue = $glue;
	}

	/**
	 * Forges a new instance of Arr_Validator_Rule_Group.
	 *
	 * @param string $glue optional Arr_Validator_Glue:: constant that specifies the glue method.
	 * @param array $rules optional Array of rules or groups (as objects or it's array representation) to
	 * include in the new instance.
	 * @return Arr_Validator_Rule_Group the new instance.
	 */
	public static function forge($glue = Arr_Validator_Glue::_AND, $rules = array())
	{
		$return = new static($glue);

		// Add the rules or groups
		$return->add($rules);

		return $return;
	}

	/**
	 * Gets the group's glue.
	 *
	 * @return string the group's Arr_Validator_Glue glue.
	 */
	public function get_glue()
	{
		return $this->_glue;
	}

	/**
	 * Gets the group's rules
	 *
	 * @return array of Arr_Validator_Rule and Arr_Validator_Rule_Group objects.
	 */
	public function get_rules()
	{
		return $this->_rules;
	}

	/**
	 * Adds a new rule to the group. The rules are added in order of method execution. If a rule is added
	 * twice, the position that the rule will be in the end is that which the latest execution
	 * determined.
	 *
	 * @param string|array|Arr_Validator_Rule $operator The operator to be used in the rule or an
	 * instance of Arr_Validator_Rule itself or it's array representation.
	 * @param mixed $operand optional the comparison operand(s) to be used by the rule.
	 * @param string $glue optional Arr_Validator_Glue:: constant that specifies the glue method.
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function add_rule($operator, $operand = null, $glue = Arr_Validator_Glue::_AND)
	{
		// Insert the rule at the end
		return $this->add_rule_at(count($this->_rules), $operator, $operand, $glue);
	}

	/**
	 * Adds a new rule to the group at the specified index. The rules are added in order of method
	 * execution. If a rule is added twice, the position that the rule will be in the end is that which
	 * the latest execution determined.
	 *
	 * @param int $index the index to add the rule at.
	 * @param string|array|Arr_Validator_Rule $operator The operator to be used in the rule or an
	 * instance of Arr_Validator_Rule itself or it's array representation.
	 * @param mixed $operand optional the comparison operand(s) to be used by the rule.
	 * @param string $glue optional Arr_Validator_Glue:: constant that specifies the glue method.
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function add_rule_at($index, $operator, $operand = null, $glue = Arr_Validator_Glue::_AND)
	{
		$rule = $operator;

		if(is_object($rule))
		{
			if(!($rule instanceof Arr_Validator_Rule))
			{
				throw new \InvalidArgumentException("The object must be an instance of Arr\\Arr_Validator_Rule.");
			}
		}
		elseif(is_array($rule))
		{
			$rule = Arr_Validator_Rule::from_array($rule);
		}
		elseif(Arr_Validator_Operator::is_operator($rule))
		{
			$rule = Arr_Validator_Rule::forge($operator, $operand, $glue);
		}
		else
		{
			throw new \InvalidArgumentException("The passed parameters are incorrect. A rule cannot be forged from them.");
		}

		// Insert the rule
		$this->_insert_rule($rule, $index);

		return $this;
	}

	/**
	 * Inserts a rule into the group's rules array at position pos. The negated rule (negated
	 * Arr_Validator_Operator) will be removed from the group as only one of them can exist at the same
	 * time. The new rule will be inserted at the end or at the specified position.
	 *
	 * @param array|Arr_Validator_Rule $rule the rule object or it's array representation to insert into
	 * the group.
	 * @param int $pos the position in the rules array.
	 * @return void
	 */
	protected function _insert_rule($rule, $pos = -1)
	{
		$new_pos = $pos;

		if(is_array($rule))
		{
			$rule = Arr_Validator_Rule::from_array($rule);
		}
		elseif(!($rule instanceof Arr_Validator_Rule))
		{
			throw new \InvalidArgumentException("The given \$rule param is incorrect. Please use an instance of Arr\\Arr_Validator_Rule or it's array representation.");
		}

		// Unset the rule if exists, if deleted decrement pos by 1.
		$this->remove($this->_search_rule($rule->operator), 'Arr\Arr_Validator_Rule') and $new_pos--;

		// Get the negated operator.
		$neg_operator = Arr_Validator_Operator::negate($rule->operator);

		// Unset the negated operator if exists. Search only if the non-negated rule was not found, if
		// deleted decrement pos by 1.
		($new_pos == $pos) and $this->remove($this->_search_rule($neg_operator), 'Arr\Arr_Validator_Rule') and $new_pos--;

		// Insert the new rule at the specified position (with modified pos because of deletions).
		if($new_pos < 0 || $new_pos >= count($this->_rules))
		{
			// Insert it at the end
			$this->_rules[] = $rule;
		}
		else
		{
			// Insert at the specified position. Use array($rule) because of how array_splice works.
			// Re-indexation is automatically handled by the insert function.
			\Arr::insert($this->_rules, array($rule), $new_pos);
		}
	}

	/**
	 * Searches the rule specified by the operator and returns the index.
	 *
	 * @param string $operator Arr_Validator_Operator:: constant to search for.
	 * @return bool|int false if not found, otherwise the index position.
	 */
	protected function _search_rule($operator)
	{
		$return = false;

		// Verify if the given operator is a supported one
		if(!Arr_Validator_Operator::is_operator($operator))
		{
			throw new \InvalidArgumentException("The given operator [{$operator}] is not a supported operator. Please refer to the documentation for supported operators.");
		}

		// Remember a group can contain rules AND group of rules so we must check for that
		foreach($this->_rules as $key => $rule)
		{
			if($rule instanceof Arr_Validator_Rule)
			{
				if($rule->operator == $operator)
				{
					$return = $key;
					break;
				}
			}
		}

		return $return;
	}

	/**
	 * Adds multiple rules to the group.
	 *
	 * @param array $rules as Arr_Validator_Rule objects or it's array representations.
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function add_rules(array $rules)
	{
		foreach($rules as $rule)
		{
			$this->add_rule($rule);
		}

		return $this;
	}

	/**
	 * Removes a rule from the group indicated by index. This is an alias to
	 * Arr_Validator_Rule_Group::remove() which enforces removal by type Arr\Arr_Validator_Rule.
	 * If the rule cannot be removed it can be because index is out of bounds or that the type of the
	 * item of index is not an Arr_Validator_Rule instance.
	 *
	 * @param int $index the index of the rule to be removed.
	 * @return bool true if the rule was deleted, false otherwise.
	 */
	public function remove_rule($index)
	{
		return $this->remove($index, 'Arr\Arr_Validator_Rule');
	}

	/**
	 * Adds a new group to the group. The groups are added in order of method execution.
	 *
	 * @param string|array|Arr_Validator_Rule_Group $glue the glue for the group or an instance of
	 * Arr_Validator_Rule_Group or it's array representation.
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function add_group($glue = Arr_Validator_Glue::_AND)
	{
		return $this->add_group_at(count($this->_rules), $glue);
	}

	/**
	 * Adds a new group to the group at the specified index. The groups are added in order of method
	 * execution.
	 *
	 * @param int $index the index to add the group at.
	 * @param string|array|Arr_Validator_Rule_Group $glue the glue for the group or an instance of
	 * Arr_Validator_Rule_Group or it's array representation.
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function add_group_at($index, $glue = Arr_Validator_Glue::_AND)
	{
		$group = $glue;

		if(is_object($group))
		{
			if(!($group instanceof Arr_Validator_Rule_Group))
			{
				throw new \InvalidArgumentException("The object must be an instance of Arr\\Arr_Validator_Rule_Group.");
			}
		}
		elseif(is_array($group))
		{
			$group = Arr_Validator_Rule_Group::from_array($group);
		}
		elseif(Arr_Validator_Glue::is_glue($group))
		{
			$group = Arr_Validator_Rule_Group::forge($glue);
		}
		else
		{
			throw new \InvalidArgumentException("The passed parameters are incorrect. A group cannot be forged from them.");
		}

		// Insert the rule
		$this->_insert_group($group, $index);

		return $this;
	}

	/**
	 * Inserts a group into the group's rules array at position pos.
	 *
	 * @param array|Arr_Validator_Rule $rule the rule object or it's array representation to insert into
	 * the group.
	 * @param int $pos the position in the rules array.
	 * @return void
	 */
	protected function _insert_group($group, $pos = -1)
	{
		$new_pos = $pos;

		if(is_array($group))
		{
			$group = Arr_Validator_Rule_Group::from_array($group);
		}
		elseif(!($group instanceof Arr_Validator_Rule_Group))
		{
			throw new \InvalidArgumentException("The given \$group param is incorrect. Please use an instance of Arr\\Arr_Validator_Rule_Group or it's array representation.");
		}

		// Insert the new group at the specified position.
		if($new_pos < 0 || $new_pos >= count($this->_rules))
		{
			// Insert it at the end
			$this->_rules[] = $group;
		}
		else
		{
			// Insert at the specified position. Use array($group) because of how array_splice works.
			\Arr::insert($this->_rules, array($group), $new_pos);
		}
	}

	/**
	 * Adds multiple groups to the group.
	 *
	 * @param array $groups as Arr_Validator_Rule_Group objects or it's array representations.
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function add_groups(array $groups)
	{
		foreach($groups as $group)
		{
			$this->add_group($group);
		}

		return $this;
	}

	/**
	 * Removes a group from the group indicated by index. This is an alias to
	 * Arr_Validator_Rule_Group::remove() which enforces removal by type Arr\Arr_Validator_Rule_Group.
	 * If the group cannot be removed it can be because index is out of bonuds or that the type of the
	 * item of index is not an Arr_Validator_Rule_Group instance.
	 *
	 * @param int $index the index of the group to be removed.
	 * @return bool true if the group was deleted, false otherwise.
	 */
	public function remove_group($index)
	{
		return $this->remove($index, 'Arr\Arr_Validator_Rule_Group');
	}

	/**
	 * Adds an array of mixed rules and groups in their array representations to the group.
	 *
	 * @param array $rules array of rules or groups instances or it's array representations.
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function add(array $rules)
	{
		foreach($rules as $rule)
		{
			if(is_object($rule))
			{
				if($rule instanceof Arr_Validator_Rule)
				{
					$this->add_rule($rule);
				}
				elseif($rule instanceof Arr_Validator_Rule_Group)
				{
					$this->add_group($rule);
				}
			}
			elseif(is_array($rule))
			{
				if(isset($rule['operator']))
				{
					$this->add_rule(Arr_Validator_Rule::from_array($rule));
				}
				else
				{
					$this->add_group(Arr_Validator_Rule_Group::from_array($rule));
				}
			}
		}

		return $this;
	}

	/**
	 * Gets a rule/group for the group specified by index.
	 *
	 * @param int $index the index of the rule/group to get.
	 * @param mixed $default optional value to return if element not found.
	 * @return mixed the element if found or default otherwise.
	 */
	public function get($index, $default = null)
	{
		return \Arr::get($this->_rules, $index, $default);
	}

	/**
	 * Removes an element from the rules array.
	 *
	 * @return bool true if it could be removed, false otherwise.
	 */
	public function remove($index, $enforced_type = '')
	{
		$return = false;

		// Validate enforced type
		!is_string($enforced_type) and $enforced_type = '';

		// Enforce type to remove
		if($enforced_type != '')
		{
			if(!($this->get($index, null) instanceof $enforced_type))
			{
				return $return;
			}
		}

		$return = \Arr::delete($this->_rules, $index);

		// Re-index array only if we deleted something
		if($return)
		{
			$this->_rules = array_values($this->_rules);
		}

		return $return;
	}

	/**
	 * Gets the group's array representation.
	 *
	 * @return array the group's array representation.
	 */
	public function as_array()
	{
		// @formatter:off
		$return = array(
			'glue' => $this->_glue,
		);
		// @formatter:on

		// Get the array's representation for every rule or group
		foreach($this->_rules as $rule)
		{
			$return['rules'][] = $rule->as_array();
		}

		return $return;
	}

	/**
	 * Forges a new instance of Arr_Validator_Rule_Group from it's array representation.
	 *
	 * @param array $group an Arr_Validator_Rule_Group's array representation.
	 * @return Arr_Validator_Rule_Group the new instance.
	 */
	public static function from_array(array $group)
	{
		if(isset($group['operator']))
		{
			// This is a rule! Cannot proceed.
			throw new \InvalidArgumentException("The given array does not represent an Arr\Arr_Validator_Rule_Group but may represent an Arr\Arr_Validator_Rule instead. Cannot proceed.");
		}

		$glue = (isset($group['glue']) ? $group['glue'] : Arr_Validator_Glue::_AND);
		$rules = (isset($group['rules']) ? $group['rules'] : array());

		$return = Arr_Validator_Rule_Group::forge($glue);

		// Add rules or groups
		$return->add($rules);

		return $return;
	}

	/**
	 * Clears the rules and groups from the group.
	 *
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function clear()
	{
		unset($this->_rules);

		$this->_rules = array();

		return $this;
	}

	/**
	 * Clears the rules from the group.
	 *
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function clear_rules()
	{
		$count = count($this->_rules);

		for($i = 0; $i < $count; $i++)
		{
			if($this->_rules[$i] instanceof Arr_Validator_Rule)
			{
				unset($this->_rules[$i]);
			}
		}

		// Re-index if needed
		$new_count = count($this->_rules);
		if($new_count > 0 && $count > $new_count)
		{
			$this->_rules = array_values($this->_rules);
		}

		return $this;
	}

	/**
	 * Clears the groups from the group.
	 *
	 * @return Arr_Validator_Rule_Group this instance for chaining.
	 */
	public function clear_groups()
	{
		$count = count($this->_rules);

		for($i = 0; $i < $count; $i++)
		{
			if($this->_rules[$i] instanceof Arr_Validator_Rule_Group)
			{
				unset($this->_rules[$i]);
			}
		}

		// Re-index if needed
		$new_count = count($this->_rules);
		if($new_count > 0 && $count > $new_count)
		{
			$this->_rules = array_values($this->_rules);
		}

		return $this;
	}

	/**
	 * Evaluates the group against a main operand. If a $glue_to_value param is given (other than null)
	 * then the returned value will be automatically glued to the operation's result.
	 *
	 * @param mixed $main_operand the operand to apply the group to.
	 * @param mixed $glue_to_value optional the value to glue the result to automatically.
	 * @return bool result of applying the group's rules to $main_operand and glueing to $glue_to_value
	 */
	public function evaluate($main_operand, $glue_to_value = null)
	{
		$return = false;

		$rules = $this->_rules;
		if(($rule = reset($this->_rules)) !== false)
		{
			$return = $rule->evaluate($main_operand);

			// Get rid of the first rule as we have already evaluated it to have a base return value to glue to
			$rules = array_slice($rules, 1, null, true);
			foreach($rules as $rule)
			{
				$return = $rule->evaluate($main_operand, $return);
			}

			// Do we need to glue it to some value?
			if($glue_to_value !== null)
			{
				switch($this->_glue)
				{
					case Arr_Validator_Glue::_AND:
						$return = $glue_to_value and $return;
						break;
					case Arr_Validator_Glue::_OR:
						$return = $glue_to_value or $return;
						break;
				}
			}
		}

		return $return;
	}

}
