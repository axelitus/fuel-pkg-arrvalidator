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

// @formatter:off
/**
 * OperatorNotSupportedException
 *
 * @package     Fuel
 * @subpackage  ArrValidator
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-arrvalidator
 */
class OperatorNotSupportedException extends \FuelException {}
// @formatter:on

/**
 * ArrValidator_Operator
 *
 * @package     Fuel
 * @subpackage  ArrValidator
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-arrvalidator
 */
class ArrValidator_Operator
{
	// one-operand operators
	const IS_SET = 'isset';
	const NOT_IS_SET = '!isset';

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

	// two-operand operators
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

	const REGEXP = 'regexp';

	// three-operand operators
	const BETWEEN = '<==>';
	const BETWEEN_ALIAS = 'between';
	const NOT_BETWEEN = '>==<';
	const NOT_BETWEEN_ALIAS = '!between';

	/**
	 * Verifies if the operator is a supported operator.
	 *
	 * @return bool
	 */
	public static function is_supported($operator)
	{
		$return = false;

		if (is_string($operator) && $operator != '')
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
				case static::REGEXP:
				case static::BETWEEN:
				case static::BETWEEN_ALIAS:
				case static::NOT_BETWEEN:
				case static::NOT_BETWEEN_ALIAS:
					$return = true;
				break;
			}
		}

		return $return;
	}

	/**
	 * Retrieves the number of operands needed by the operator. This count includes the main operand.
	 *
	 * @return int number of operands that needs the operator or false if it's not supported.
	 */
	public static function operand_count($operator)
	{
		$return = false;

		if (static::is_supported($operator))
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
					$return = 1;
				break;
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
				case static::REGEXP:
					$return = 2;
				break;
				case static::BETWEEN:
				case static::BETWEEN_ALIAS:
				case static::NOT_BETWEEN:
				case static::NOT_BETWEEN_ALIAS:
					$return = 3;
				break;
			}
		}

		return $return;
	}

	/**
	 * Applies the given operator to the given main operand using the optional comparison operand(s) as
	 * required by the operator and returns the evaluation. If no comparison operand is given and the
	 * operator needs it, null will be used as the comparison value.
	 *
	 * @return bool depends on the given operator and operand(s).
	 */
	public static function apply($main_operand, $operator, $secondary_operand = null)
	{
		$return = false;

		if (static::is_supported($operator))
		{
			// Although this could be shortened using the eval() method, we will not do so for security reasons.
			switch(strtolower($operator))
			{
				case static::IS_SET:
					$return = isset($main_operand);
				break;
				case static::IS_NULL:
					$return = is_null($main_operand);
				break;
				case static::NOT_IS_NULL:
					$return = ! is_null($main_operand);
				break;
				case static::IS_EMPTY:
					$return = empty($main_operand);
				break;
				case static::NOT_IS_EMPTY:
					$return = ! empty($main_operand);
				break;
				case static::IS_ARRAY:
					$return = is_array($main_operand);
				break;
				case static::NOT_IS_ARRAY:
					$return = ! is_array($main_operand);
				break;
				case static::IS_NUMERIC:
					$return = is_numeric($main_operand);
				break;
				case static::NOT_IS_NUMERIC:
					$return = ! is_numeric($main_operand);
				break;
				case static::IS_STRING:
					$return = is_string($main_operand);
				break;
				case static::NOT_IS_STRING:
					$return = ! is_string($main_operand);
				break;
				case static::IS_BOOL:
					$return = is_bool($main_operand);
				break;
				case static::NOT_IS_BOOL:
					$return = ! is_bool($main_operand);
				break;
				case static::IS_INT:
					$return = is_int($main_operand);
				break;
				case static::NOT_IS_INT:
					$return = ! is_int($main_operand);
				break;
				case static::IS_FLOAT:
					$return = is_float($main_operand);
				break;
				case static::NOT_IS_FLOAT:
					$return = ! is_float($main_operand);
				break;
				case static::IS_DOUBLE:
					$return = is_double($main_operand);
				break;
				case static::NOT_IS_DOUBLE:
					$return = ! is_double($main_operand);
				break;
				case static::IS_OBJECT:
					$return = is_object($main_operand);
				break;
				case static::NOT_IS_OBJECT:
					$return = ! is_object($main_operand);
				break;
				case static::IS_RESOURCE:
					$return = is_resource($main_operand);
				break;
				case static::NOT_IS_RESOURCE:
					$return = ! is_resource($main_operand);
				break;
				case static::EQUAL:
					$return = ($main_operand == $secondary_operand);
				break;
				case static::IDENTICAL:
					$return = ($main_operand === $secondary_operand);
				break;
				case static::NOT_EQUAL:
					$return = ($main_operand != $secondary_operand);
				break;
				case static::NOT_EQUAL_ALIAS:
					$return = ($main_operand <> $secondary_operand);
				break;
				case static::NOT_IDENTICAL:
					$return = ($main_operand !== $secondary_operand);
				break;
				case static::LESS_THAN:
					$return = ($main_operand < $secondary_operand);
				break;
				case static::GREATER_THAN:
					$return = ($main_operand > $secondary_operand);
				break;
				case static::LESS_THAN_OR_EQUAL:
					$return = ($main_operand <= $secondary_operand);
				break;
				case static::GREATER_THAN_OR_EQUAL:
					$return = ($main_operand >= $secondary_operand);
				break;
				case static::INSTANCE_OF:
					$return = ($main_operand instanceof $secondary_operand);
				break;
				case static::NOT_INSTANCE_OF:
					$return = ( ! ($main_operand instanceof $secondary_operand));
				break;
				case static::REGEXP:
					// TODO: eval regexp
					$return = false;
				break;
				case static::BETWEEN:
				case static::BETWEEN_ALIAS:
					if (is_array($secondary_operand) && count($secondary_operand) > 1)
					{
						$min = min($secondary_operand[0], $secondary_operand[1]);
						$max = max($secondary_operand[0], $secondary_operand[1]);

						$return = ($min <= $main_operand && $main_operand <= $max);
					}
					else
					{
						throw new InvalidArgumentException('The operator BETWEEN requires two secondary operands which were not given. Please use format: array(a, b).');
					}
				break;
				case static::NOT_BETWEEN:
				case static::NOT_BETWEEN_ALIAS:
					if (is_array($secondary_operand) && count($secondary_operand) > 1)
					{
						$min = min($secondary_operand[0], $secondary_operand[1]);
						$max = max($secondary_operand[0], $secondary_operand[1]);

						$return = ($min >= $main_operand || $main_operand >= $max);
					}
					else
					{
						throw new InvalidArgumentException('The operator NOT_BETWEEN requires two secondary operands which were not given. Please use format: array(a, b).');
					}
				break;
			}
		}
		else
		{
			throw new OperatorNotSupportedException('The given operator \''.$operator.'\' is not a supported operator.');
		}

		return $return;
	}

}
