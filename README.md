# FuelPHP ArrValidator package

The FuelPHP ArrValidator package simplifies and automatizes the validation of arrays against simple rules. If an array's node doesn't comply with the rules, then a default value is set to that item.

## About

* Latest Version: v1.0
* Released: 
* Author: Axel Pardemann ([http://axelitus.mx](http://axelitus.mx))

## Requirements

* FuelPHP Framework version v1.1

## Development Team

* Axel Pardemann - Lead Developer ([http://dev.blogs.axelitus.mx](http://dev.blogs.axelitus.mx))

## Repository

You can find the GitHub Repository on [https://github.com/axelitus/fuel-pkg-arrvalidator](https://github.com/axelitus/fuel-pkg-arrvalidator)

## Suggestions / Issues / Fixes

* For Issues you can use [GitHub's Issue Tracker](https://github.com/axelitus/fuel-pkg-arrvalidator/issues)
* If you have suggestions you can send an email to dev [at] axelitus [dot] mx
* If you have any fixes or new features you'd like to share please send them as Pull Requests on [GitHub](https://github.com/axelitus/fuel-pkg-arrvalidator/pulls)

## Installation

The package installation is very easy. You can choose one of two methods described here.

### Manual

Just download the source code located at [axelitus' FuelPHP ArrValidator package at GitHub](https://github.com/axelitus/fuel-pkg-arrvalidator) and place it in a folder named `arrvalidator` inside the packages folder in FuelPHP.

Alternatively you can use git to clone the repository directly (this will make your life easier when updating the package):

	git clone git@github.com:axelitus/fuel-pkg-arrvalidator arrvalidator

### Using Oil

Waiting for release v1.1 to complete this...

## Usage

The first thing you should do is load the package. This can be achieved manually by calling:

	\Package::load('arrvalidator');

To load it automatically you should edit your app's config file (`config.php`). Look for the `always_load` key and under `packages` and set an entry with the 'arrvalidator' string. It should look similar to this:

	...
	'always_load'	=> array(
		'packages'	=> array(
			'arrvalidator'
		),
	...

### Configuration

The configuration is done using the file `arrvalidator.php` in the config directory (you are encouraged to copy this file to your `app/config` folder and edit that file instead).

An example of the contents of a `arrvalidator.php` config file:

	return array(
		'groups' => array(
			'connections' => 'arrvalidator/connections/db, arrvalidator/connections/ldap',
			'webservices' => array(
				'arrvalidator/ws/amazon',
				'arrvalidator/ws/google'
			)
		),
		'auto_load' => array(
			'validators' => array(
				'arrvalidator/api/twitter',
				'arrvalidator/api/facebook'
			),
			'groups' => array(
				'connections',
				'webservices'
			)
		)
	);

The options in the config array are explained as follows:

#### groups (type: array, default: array())

You can define groups of validator description files. The group should be named and it can accept an array of strings or a comma-separated string of array validator description files.

Note: the validator description files are relative to the main config folder.

#### auto_load (type: array)

This sections holds what validators and groups of validators will be automatically loaded upon class initialization.

##### validators (type: array, default: array())

The validators that will be auto-loaded should be defined here. Every item (string) in the array is a validator description file to be loaded.

Note: the validator description files are relative to the main config folder.

##### groups (type: array, default: array())

The groups of validators that will be auto-loaded should be defined here. Every item (string) in the array is the name of a group of validators which has been defined in the `groups` config entry.

### Validator Description Files

The validator description files are config files that describe a validator that can be loaded from it. This files can contain a single validator description or multiple validators description. There are some rules/structure that must be followed:

#### Single Validator Description File

This is the most common form of validator description, inside such a file there's only one description and it looks like this:

	return array(
		'name' => 'ldap',
		'nodes' => array(
			'connection.server' => array(
				'default' => 'defaultserver.mydomain.com',
				'rules' => array(
					array(
						'operator' => 'is_string'
					),
					array(
						'operator' => '!empty'
					)
				)
			),
			'connection.port' => array(
				'default' => 389,
				'rules' => array(
					array(
						'operator' => 'is_numeric',
					),
					array(
						'operator' => 'between',
						'operand' => array(1, 65535)
					)
				)
			)
		)
	);

For an array to be identified as a validator description (and to attempt to load it) there must exist one of the two sub-items. If one is found and the other not, then the non-existent one will be set to it's own default value.

##### name (type: string, default: '')

This is the name of the validator. If you want a named validator (other than an empty string which will overwrite previously loaded non-named validator) then this must exist.

##### nodes (type: array, default: array())

This are the nodes that belong to the validator to which the rules will be applied to. This are named arrays in which the key is corresponds to the key to be verified in the array that is validated.

Please refer to _Node array structure_.

##### Node array structure

The structure of the array that represents a node is as follows:

	array(
		'default' => mixed,
		'rules' => array(
		)
	);

###### default (type: mixed, default: null)

This is the default value that the item will take in the array that is being validated if the rules do not comply.

###### rules (type: array, default: array())

This specifies the rules to be applied to the item in the array that is being validated.

Please refer to _Rule array structure_.

##### Rule array structure

The structure of the array that represents a rule is as follows:

	array(
		'operator' => string,
		'operand' => mixed
	);

###### operator (type: string)

The operator for this rule that will be applied to the value of the item in the array that is being validated. If this entry does not exist, an `InvalidArgumentException` will be thrown. The following are the only valid operators (all of them are defined as constants in the class `ArrValidator_Operator`, this list is to be read as `[const\_name] -> actual\_value`):

* one-operand operators
	- [IS\_SET] -> 'isset'
	- [NOT\_IS\_SET] -> '!isset'
	- [IS\_NULL] -> 'is\_null'
	- [NOT\_IS\_NULL] -> '!isnull'
	- [IS\_EMPTY] -> 'empty'
	- [NOT\_IS\_EMPTY] -> '!empty'
	- [IS\_ARRAY] -> 'is\_array'
	- [NOT\_IS\_ARRAY] -> '!is\_array'
	- [IS\_NUMERIC] -> 'is\_numeric'
	- [NOT\_IS\_NUMERIC] -> '!is\_numeric'
	- [IS\_STRING] -> 'is\_string'
	- [NOT\_IS\_STRING] -> '!is\_string'
	- [IS\_BOOL] -> 'is\_bool'
	- [NOT\_IS\_BOOL] -> '!is\_bool'
	- [IS\_INT] -> 'is\_int'
	- [NOT\_IS\_INT] -> '!is\_int'
	- [IS\_FLOAT] -> 'is\_float'
	- [NOT\_IS\_FLOAT] -> '!is\_float'
	- [IS\_DOUBLE] -> 'is\_double'
	- [NOT\_IS\_DOUBLE] -> '!is\_double'
	- [IS\_OBJECT] -> 'is\_object'
	- [NOT\_IS\_OBJECT] -> '!is\_object'
	- [IS\_RESOURCE] -> 'is\_resource'
	- [NOT\_IS\_RESOURCE] -> '!is\_resource'
* two-operand operators
	- [EQUAL] -> '=='
	- [IDENTICAL] -> '==='
	- [NOT\_EQUAL] -> '!='
	- [NOT\_EQUAL\_ALIAS] -> '<>'
	- [NOT\_IDENTICAL] -> '==='
	- [LESS\_THAN] -> '<'
	- [GREATER\_THAN] -> '>'
	- [LESS\_THAN\_OR\_EQUAL] -> '<='
	- [GREATER\_THAN\_OR\_EQUAL] -> '>='
	- [INSTANCE\_OF] -> 'instanceof'
	- [NOT\_INSTANCE\_OF] -> '!instanceof'
	- [REGEXP] -> 'regexp'
* three-operand operators
	- [BETWEEN] -> '<==>'
	- [BETWEEN\_ALIAS] -> 'between'
	- [NOT\_BETWEEN] -> '>==<'
	- [NOT\_BETWEEN\_ALIAS] -> '!between'

The operand count needed for the operator to work includes the main operand (which is the actual value from the array that is being validated).

###### operand (type: mixed, default: null)

The additional operands needed for the rule to be applied. From the list above one can see that from the two-operand operators on this entry is needed. When just one operand is needed the valu must be given, if more than one operand is needed an array of operands should be given.

#### Multiple Validators Description File



### Methods



## Future development

The first version has the basic functionality one would expect. New features will be evaluated and added as soon as possible.
Please feel free to send feature erquests through the Github repository.

## Special Thanks

Firstly I would like to thank the [Fuel Development Team](http://fuelphp.com/about) for their magnificent framework and spent time for making our lives easier. Great work, keep it up!

Special thanks for he ones that helped by commenting, discussing, suggesting, testing, brainstorming (if I missed someone please let me know, if you don't want to appear in this list also let me know):