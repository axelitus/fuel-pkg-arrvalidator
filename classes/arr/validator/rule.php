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
 * Arr_Validator_Rule
 *
 * @package     Fuel
 * @subpackage  Arr Validator
 */
class Arr_Validator_Rule
{
	/**
	 * The rule's operator value.
	 *
	 * @var string Arr_Validator_Operator:: constant that specifies the operator that describes the rule.
	 */
	protected $_operator = '';

	/**
	 * The rule's additional operand(s) to use by the operator.
	 *
	 * @var mixed
	 */
	protected $_operand = null;

	/**
	 * The rule's glue method.
	 *
	 * @var string Arr_Validator_Glue:: constant that specifies the glue method to be used for the rule.
	 */
	protected $_glue = Arr_Validator_Glue::_AND;

	/**
	 * Prevent direct instantiation.
	 *
	 * @param string $operator Arr_Validator_Operator:: constant that specifies the operator.
	 * @param mixed $operand optional the comparison operand(s) to be used by the rule.
	 * @param string $glue optional Arr_Validator_Glue:: constant that specifies the glue method.
	 * @return void
	 */
	protected function __construct($operator, $operand = null, $glue = Arr_Validator_Glue::_AND)
	{
		if(!Arr_Validator_Operator::is_operator($operator))
		{
			throw new \InvalidArgumentException("The given operator [{$operator}] is not a supported operator. Please refer to the documentation for supported operators.");
		}

		if(!Arr_Validator_Glue::is_glue($glue))
		{
			throw new \InvalidArgumentException("The given glue [{$glue}] is not a supported glue. Please refer to the documentation for supported glues.");
		}

		$this->_operator = $operator;
		$this->_operand = $operand;
		$this->_glue = $glue;
	}

	/**
	 * Forges a new instance of Arr_Validator_Rule.
	 *
	 * @param string $operator Arr_Validator_Operator:: constant that specifies the operator.
	 * @param mixed $operand optional the comparison operand(s) to be used by the rule.
	 * @param string $glue optional Arr_Validator_Glue:: constant that specifies the glue method.
	 * @return Arr_Validator_Rule the new instance.
	 */
	public static function forge($operator, $operand = null, $glue = Arr_Validator_Glue::_AND)
	{
		$return = new static($operator, $operand, $glue);

		return $return;
	}

	/**
	 * Magic Method to retrieve the instance property.
	 *
	 * @param string $name the name of the property.
	 * @return mixed
	 */
	public function __get($name)
	{
		if(!array_key_exists('_' . $name, get_object_vars($this)))
		{
			throw new \InvalidArgumentException("The property '{$name}' does not exist.");
		}

		return $this->{'_' . $name};
	}

	/**
	 * Magic Method to set the instance property.
	 *
	 * @param string $name the name of the property.
	 * @param mixed $value the value for the property.
	 * @return void
	 */
	public function __set($name, $value)
	{
		if(!array_key_exists('_' . $name, get_object_vars($this)))
		{
			throw new \InvalidArgumentException("The property '{$name}' does not exist.");
		}

		switch ($name)
		{
			case 'operator':
				if(!Arr_Validator_Operator::is_operator($value))
				{
					throw new \InvalidArgumentException("The given operator [{$operator}] is not a supported operator. Please refer to the documentation for supported operators.");
				}
				break;
			case 'glue':
				if(!Arr_Validator_Glue::is_glue($value))
				{
					throw new \InvalidArgumentException("The given glue [{$glue}] is not a supported glue. Please refer to the documentation for supported glues.");
				}
				break;
		}

		$this->{'_' . $name} = $value;
	}

	/**
	 * Gets the rule's array representation.
	 *
	 * @return array the rule's array representation.
	 */
	public function as_array()
	{
		// @formatter:off
		$return = array(
			'operator' => $this->_operator,
			'operand' => $this->_operand,
			'glue' => $this->_glue,
		);
		// @formatter:on

		return $return;
	}

	/**
	 * Forges a new instance of Arr_Validator_Rule from it's array representation.
	 *
	 * @param array $rule an Arr_Validator_Rule's array representation.
	 * @return Arr_Validator_Rule the new instance.
	 */
	public static function from_array(array $rule)
	{
		if(!isset($rule['operator']) || !Arr_Validator_Operator::is_operator($rule['operator']))
		{
			throw new \InvalidArgumentException("The key 'operator' in the given array must exist and be a supported operator.");
		}

		return static::forge($rule['operator'], \Arr::get($rule, 'operand', null), \Arr::get($rule, 'glue', Arr_Validator_Glue::_AND));
	}

	/**
	 * Changes this instance's members only if the parameters are set in the $rule param.
	 *
	 * @param array $rule a partial Arr_Validator_Rule's array representation.
	 * @return Arr_Validator_Rule this instance for chaining.
	 */
	public function change(array $rule)
	{
		isset($rule['operator']) and $this->operator = $rule['operator'];
		isset($rule['operand']) and $this->operand = $rule['operand'];
		isset($rule['glue']) and $this->glue = $rule['glue'];

		return $this;
	}

	/**
	 * Evaluates the rule against a main operand. If a $glue_to_value param is given (other than null)
	 * then the returned value will be automatically glued to the operation's result.
	 *
	 * @param mixed $main_operand the operand to apply the rule to.
	 * @param mixed $glue_to_value optional the value to glue the result to automatically.
	 * @return bool result of applying the rule's operator to $main_operand and glueing to $glue_to_value
	 */
	public function evaluate($main_operand, $glue_to_value = null)
	{
		// Get the result of the operation
		$return = Arr_Validator_Operator::apply($main_operand, $this->_operator, $this->_operand);
		
		// Do we need to glue it to some value?
		if($glue_to_value !== null)
		{
			switch($this->_glue)
			{
				case Arr_Validator_Glue::_AND:
					$return = ($glue_to_value and $return);
					break;
				case Arr_Validator_Glue::_OR:
					$return = ($glue_to_value or $return);
					break;
			}
		}

		return $return;
	}

}
