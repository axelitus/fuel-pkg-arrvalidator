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
class Arr_Validator_Operator
{
	// Special operators
	const IS_SET = 'isset';
	const NOT_IS_SET = '!isset';

	// Basic one-operand operators
	const IS_NULL = 'is_null';
	const NOT_IS_NULL = '!isnull';

	const IS_EMPTY = 'empty';
	const NOT_IS_EMPTY = '!empty';

	const IS_ARRAY = 'is_array';
	const NOT_IS_ARRAY = '!is_array';

	const IS_NUMERIC = 'is_numeric';
	const NOT_IS_NUMERIC = '!is_numeric';

	const IS_STRING = 'is_string';
	const NOT_IS_STRING = '!is_string';

	const IS_BOOL = 'is_bool';
	const NOT_IS_BOOL = '!is_bool';

	const IS_INT = 'is_int';
	const NOT_IS_INT = '!is_int';

	const IS_FLOAT = 'is_float';
	const NOT_IS_FLOAT = '!is_float';

	const IS_DOUBLE = 'is_double';
	const NOT_IS_DOUBLE = '!is_double';

	const IS_OBJECT = 'is_object';
	const NOT_IS_OBJECT = '!is_object';

	const IS_RESOURCE = 'is_resource';
	const NOT_IS_RESOURCE = '!is_resource';

	// Basic two-operand operators
	const EQUAL = '==';
	const IDENTICAL = '===';
	const NOT_EQUAL = '!=';
	const NOT_EQUAL_ALIAS = '<>';
	const NOT_IDENTICAL = '===';

	const LESS_THAN = '<';
	const GREATER_THAN = '>';
	const LESS_THAN_OR_EQUAL = '<=';
	const GREATER_THAN_OR_EQUAL = '>=';
	
	const INSTANCE_OF = 'instanceof';
	const NOT_INSTANCE_OF = '!instanceof';

	// Extended three-operand operators
	const BETWEEN = '<==>';
	const NOT_BETWEEN = '>==<';

	/**
	 * Verifies if the operator is a supported operator.
	 *
	 * @return bool
	 */
	public static function is_operator($operator)
	{
		$return = false;

		if(is_string($operator) && $operator != '')
		{
			switch(strtolower($operator))
			{
				case static::IS_SET:
				case static::NOT_IS_SET:
				case static::IS_NULL:
				case static::NOT_IS_NULL:
				case static::IS_EMPTY:
				case static::NOT_IS_EMPTY:
				case static::IS_ARRAY:
				case static::NOT_IS_ARRAY:
				case static::IS_NUMERIC:
				case static::NOT_IS_NUMERIC:
				case static::IS_STRING:
				case static::NOT_IS_STRING:
				case static::IS_BOOL:
				case static::NOT_IS_BOOL:
				case static::IS_INT:
				case static::NOT_IS_INT:
				case static::IS_FLOAT:
				case static::NOT_IS_FLOAT:
				case static::IS_DOUBLE:
				case static::NOT_IS_DOUBLE:
				case static::IS_OBJECT:
				case static::NOT_IS_OBJECT:
				case static::IS_RESOURCE:
				case static::NOT_IS_RESOURCE:
				case static::EQUAL:
				case static::IDENTICAL:
				case static::NOT_EQUAL:
				case static::NOT_EQUAL_ALIAS:
				case static::NOT_IDENTICAL:
				case static::LESS_THAN:
				case static::GREATER_THAN:
				case static::LESS_THAN_OR_EQUAL:
				case static::GREATER_THAN_OR_EQUAL:
				case static::INSTANCE_OF:
				case static::NOT_INSTANCE_OF:
				case static::BETWEEN:
				case static::NOT_BETWEEN:
					$return = true;
					break;
			}
		}
		return $return;
	}

	/**
	 * Negates an operator to it's counterpart.
	 *
	 * @return string the negated operator
	 */
	public static function negate($operator)
	{
		$return = strtolower($operator);
		if(static::is_operator($return))
		{
			switch($operator)
			{
				case static::EQUAL:
					$return = static::NOT_EQUAL;
					break;
				case static::IDENTICAL:
					$return = static::NOT_IDENTICAL;
					break;
				case static::NOT_EQUAL:
					$return = static::EQUAL;
					break;
				case static::NOT_EQUAL_ALIAS:
					$return = static::EQUAL;
					break;
				case static::NOT_IDENTICAL:
					$return = static::IDENTICAL;
					break;
				case static::LESS_THAN:
					$return = static::GREATER_THAN;
					break;
				case static::GREATER_THAN:
					$return = static::LESS_THAN;
					break;
				case static::LESS_THAN_OR_EQUAL:
					$return = static::GREATER_THAN_OR_EQUAL;
					break;
				case static::GREATER_THAN_OR_EQUAL:
					$return = static::LESS_THAN_OR_EQUAL;
					break;
				case static::BETWEEN:
					$return = static::NOT_BETWEEN;
					break;
				case static::NOT_BETWEEN:
					$return = static::BETWEEN;
					break;
				default:
				// As all other cases only differ in the first character, test against it
					if($return[0] == '!')
					{
						$return = substr($return, 1);
					}
					else
					{
						$return = '!' . $return;
					}
					break;
			}
		}

		return $return;
	}

	/**
	 * Applies the given operator to the given left and right operands and returns the evaluation.
	 *
	 * @return bool depends on the given operator and operand(s).
	 */
	public static function apply($main_operand, $operator, $comparison_operand = null)
	{
		$return = false;

		if(static::is_operator($operator))
		{
			// Although this could be shortened using the eval() method, we will not do so for security reasons.
			switch(strtolower($operator))
			{
				case Arr_Validator_Operator::IS_NULL:
					$return = is_null($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_NULL:
					$return = !is_null($main_operand);
					break;
				case Arr_Validator_Operator::IS_EMPTY:
					$return = empty($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_EMPTY:
					$return = !empty($main_operand);
					break;
				case Arr_Validator_Operator::IS_ARRAY:
					$return = is_array($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_ARRAY:
					$return = !is_array($main_operand);
					break;
				case Arr_Validator_Operator::IS_NUMERIC:
					$return = is_numeric($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_NUMERIC:
					$return = !is_numeric($main_operand);
					break;
				case Arr_Validator_Operator::IS_STRING:
					$return = is_string($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_STRING:
					$return = !is_string($main_operand);
					break;
				case Arr_Validator_Operator::IS_BOOL:
					$return = is_bool($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_BOOL:
					$return = !is_bool($main_operand);
					break;
				case Arr_Validator_Operator::IS_INT:
					$return = is_int($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_INT:
					$return = !is_int($main_operand);
					break;
				case Arr_Validator_Operator::IS_FLOAT:
					$return = is_float($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_FLOAT:
					$return = !is_float($main_operand);
					break;
				case Arr_Validator_Operator::IS_DOUBLE:
					$return = is_double($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_DOUBLE:
					$return = !is_double($main_operand);
					break;
				case Arr_Validator_Operator::IS_OBJECT:
					$return = is_object($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_OBJECT:
					$return = !is_object($main_operand);
					break;
				case Arr_Validator_Operator::IS_RESOURCE:
					$return = is_resource($main_operand);
					break;
				case Arr_Validator_Operator::NOT_IS_RESOURCE:
					$return = !is_resource($main_operand);
					break;
				case Arr_Validator_Operator::EQUAL:
					$return = ($main_operand == $comparison_operand);
					break;
				case Arr_Validator_Operator::IDENTICAL:
					$return = ($main_operand === $comparison_operand);
					break;
				case Arr_Validator_Operator::NOT_EQUAL:
					$return = ($main_operand != $comparison_operand);
					break;
				case Arr_Validator_Operator::NOT_EQUAL_ALIAS:
					$return = ($main_operand <> $comparison_operand);
					break;
				case Arr_Validator_Operator::NOT_IDENTICAL:
					$return = ($main_operand !== $comparison_operand);
					break;
				case Arr_Validator_Operator::LESS_THAN:
					$return = ($main_operand < $comparison_operand);
					break;
				case Arr_Validator_Operator::GREATER_THAN:
					$return = ($main_operand > $comparison_operand);
					break;
				case Arr_Validator_Operator::LESS_THAN_OR_EQUAL:
					$return = ($main_operand <= $comparison_operand);
					break;
				case Arr_Validator_Operator::GREATER_THAN_OR_EQUAL:
					$return = ($main_operand >= $comparison_operand);
					break;
				case Arr_Validator_Operator::INSTANCE_OF:
					$return = ($main_operand instanceof $comparison_operand);
					break;
				case Arr_Validator_Operator::NOT_INSTANCE_OF:
					$return = (!($main_operand instanceof $comparison_operand));
					break;
				case Arr_Validator_Operator::BETWEEN:
					if(is_array($comparison_operand) && count($comparison_operand) > 1)
					{
						$min = min($comparison_operand[0], $comparison_operand[1]);
						$max = max($comparison_operand[0], $comparison_operand[1]);

						$return = ($min <= $main_operand && $main_operand <= $max);
					}
					else
					{
						\Log::warning("Arr\\Arr_Validator_Operator::apply() - The operator BETWEEN requires two comparison operands which were not given. Please use format: array(a, b).");
					}
					break;
				case Arr_Validator_Operator::NOT_BETWEEN:
					if(is_array($comparison_operand) && count($comparison_operand) > 1)
					{
						$min = min($comparison_operand[0], $comparison_operand[1]);
						$max = max($comparison_operand[0], $comparison_operand[1]);

						$return = ($min >= $main_operand || $main_operand >= $max);
					}
					else
					{
						\Log::warning("Arr\\Arr_Validator_Operator::apply() - The operator NOT_BETWEEN requires two comparison operands which were not given. Please use format: array(a, b).");
					}
					break;
			}
		}

		return $return;
	}

}
