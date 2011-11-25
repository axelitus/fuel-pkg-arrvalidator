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
				'arrvalidator/ws/google',
				'arrvalidator/ws/amazon'
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

The operator for this rule that will be applied to the value of the item in the array that is being validated. If this entry does not exist, an `InvalidArgumentException` will be thrown. The following are the only valid operators (all of them are defined as constants in the class `ArrValidator_Operator`, this list is to be read as `[const_name] -> actual_value`):

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
	- [REGEX] -> 'regex'
* three-operand operators
	- [BETWEEN] -> '<==>'
	- [BETWEEN\_ALIAS] -> 'between'
	- [NOT\_BETWEEN] -> '>==<'
	- [NOT\_BETWEEN\_ALIAS] -> '!between'

The operand count needed for the operator to work includes the main operand (which is the actual value from the array that is being validated).

###### operand (type: mixed, default: null)

The additional operand(s) needed for the rule to be applied. From the list above, for the two-operand and three-operand operators this entry is needed. When just one operand is needed the operand value is to be given, if more than one operand is needed an array of operand values should be given.

#### Multiple Validators Description File

A multiple validators description file follows the same rules as the single validator description file but instead of having only one description, it has an array of validator descriptions.

A multiple validator description file looks like this:

	return array(
		array(
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
		),
		array(
			'name' => 'db',
			'nodes' => array(
				'connection.server' => array(
					'default' => 'dbserver.mydomain.com',
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
					'default' => 3366,
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
		)
	);

### Methods

The most common methods are described here. Please refer to the doc-blocks in the actual code.

#### ArrValidator class

This is the main ArrValidator package class.

##### ArrValidator::VERSION

Contains a string for the current version of the package.

##### ArrValidator::forge($name, $overwrite = false)

**Description:** Forges a new instance of `ArrValidator` or gets the existing one. If the `$overwrite` flag is set to `true`, then the instance will be overwritten.  
**Static:** Yes  
**Return:** `ArrValidator` the validator instance

	$validator = ArrValidator::forge('FirstValidator');

	// $validator2 will get the previously created $validator
	$validator2 = ArrValidator::forge('FirstValidator');

	// $validator3 will get a new validator named FirstValidator.
	$validator3 = ArrValidator::forge('FirstValidator', true);

This is the status of variables after all three calls:

	($validator = $validator2) != $validator3

The validator named _FirstValidator_ is no longer the instance that `$variable` and `$variable2` hold, it was overwritten inside the `ArrValidator` instances array with the last method call.

##### ArrValidator::exists($name)

**Description:** Verifies if the named `ArrValidator` instance already exists.  
**Static:** Yes  
**Return:** `bool`

	if(ArrValidator::exists('FirstValidator'))
	{
		// do something if the validator already exists
	}

##### ArrValidator::instance($name)

**Description:** Gets an instance by name. If no instance is found by the given name, a new one will be forged.  
**Static:** Yes  
**Return:** `ArrValidator` the validator instance

	// Gets the previously created validator
	$validator = ArrValidator::instance('FirstValidator');

	// Gets a newly created validator
	$validator2 = ArrValidator::instance('SecondValidator');

##### ArrValidator::remove($name)

**Description:** Removes a loaded instance.  
**Static:** Yes  
**Return:** `void`

	ArrValidator::remove('SecondValidator');

##### ArrValidator::instances()

**Description:** Gets all the validator instances.  
**Static:** Yes  
**Return:** `array` of `ArrValidator` objects

	$validators = ArrValidator::instances();
	foreach($validators as $validator)
	{
		// do something with each validator
	}

##### ArrValidator::empty\_instances()

**Description:** Deletes all loaded instances.  
**Static:** Yes  
**Return:** `void`

	ArrValidator::empty_instances();

##### ArrValidator::from\_array(array $array, $overwrite = false)

**Description:** Forges an instance from an array representation. The `name` item should not be ommited using this method or else you'll get a validator with an empty string as a name (and so it is identified by it in the instances array).

Please refer to the section _Single Validator Description File_ where the validator array structure is described.  
**Static:** Yes  
**Return:** `ArrValidator` the validator instance

	$array = array(
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
	$validator = ArrValidator::from_array($array);

##### ArrValidator::all\_as\_array($ommit_name = false)

**Description:** Gets an array of all loaded `ArrValidator` instances as their array structures.  
**Static:** Yes  
**Return:** `array` of `ArrValidator` object array structures

	ArrValidator::forge('FirstValidator');
	ArrValidator::forge('SecondValidator');
	$array = ArrValidator::all_as_array();
	$array_ommited = ArrValidator::all_as_array(true);

The variable `$array` will contain:

	array(
		'FirstValidator' => array(
			'name' => 'FirstValidator',
			'nodes' => array()
		),
		'SecondValidator' => array(
			'name' => 'SecondValidator',
			'nodes' => array()
		)
	);

wheras the variable `$array_ommited` will contain:

	array(
		'FirstValidator' => array(
			'nodes' => array()
		),
		'SecondValidator' => array(
			'nodes' => array()
		)
	);

##### ArrValidator::multiple\_from\_array(array $array, $overwrite = false, $empty\_first = false)

**Description:** Loads multiple instances from an array of validator array structures. If the flag `$empty_first` is set to `true`, then the instances array will be emptied prior to the load process.

Note: The `name` item inside a validator takes precedence to the validator's key name.  
**Static:** Yes  
**Return:** `void`

	$mult_validators = array(
		'FirstValidator' => array(
			'name' => 'FirstValidatorPrecedence',
			'nodes' => array()
		),
		'SecondValidator' => array(
			'nodes' => array()
		)
	);
	ArrValidator::multiple_from_array($mult_validators);
	echo '<pre>';
	var_dump(ArrValidator::instances());
	echo '</pre>';

will output something like this:

	array(2) {
	  ["FirstValidatorPrecedence"]=>
	  object(ArrValidator\ArrValidator)#14 (2) {
	    ["_name":protected]=>
	    string(24) "FirstValidatorPrecedence"
	    ["_nodes":protected]=>
	    array(0) {
	    }
	  }
	  ["SecondValidator"]=>
	  object(ArrValidator\ArrValidator)#15 (2) {
	    ["_name":protected]=>
	    string(15) "SecondValidator"
	    ["_nodes":protected]=>
	    array(0) {
	    }
	  }
	}

##### ArrValidator::load\_from\_file($file, $overwrite = false)

**Description:** Loads a single or multiple validators from a file.

Please refer to the section _Single Validator Description File_ where the validator array structure in files usage is described.  
**Static:** Yes  
**Return:** `bool`

	ArrValidator::load_from_file('arrvalidator/ws/google');
	ArrValidator::load_from_file('arrvalidator/ws/amazon', true);

##### ArrValidator::load\_from\_group($groups, $overwrite = false)

**Description:** Loads a validators from a group (from the configuration file).  
**Static:** Yes  
**Return:** `bool`

	ArrValidator::load_from_group('connections');
	ArrValidator::load_from_group('webservices', true);

##### ArrValidator::get\_name()

**Description:** Gets the validator's name.  
**Static:** No  
**Return:** `string`

	$validator = ArrValidator::forge('FirstValidator');
	echo $validator->get_name();

##### ArrValidator::get\_nodes()

**Description:** Gets the validator's nodes.  
**Static:** No  
**Return:** `array` of `ArrValidator_Node` objects

	$validator = ArrValidator::instance('GoogleWebService');
	foreach($validator->get_nodes() as $node)
	{
		// do something with each validator node
	}

##### ArrValidator::add\_node($name, $default, $overwrite = false, array $rules = array())

**Description:** Adds a node if it does not exist. If the node already exists it will be returned. If the `$overwrite` flag is set to `true`, then the existing node will be overwritten.  
**Static:** No  
**Return:** `ArrValidator_Node` the added or previously existing node

	$validator = ArrValidator::forge('FirstValidator');
	$node = $validator->add_node('connection.port', 389);

	$rules = array(
		array(
			'operator' => 'is_string'
		),
		array(
			'operator' => '!empty'
		)
	);
	$node2 = $validator->add_node('connection.server', 'defaultserver.mydomain.com', true, $rules);

##### ArrValidator::add\_node\_object($name, ArrValidator\_Node $node, $overwrite = false)

**Description:** Adds a node from an object if it does not exist. If the node already exists it will be returned. If the `$overwrite` flag is set to `true`, then the existing node will be overwritten.  
**Static:** No  
**Return:** `ArrValidator_Node` the added or previously existing node

	$validator = ArrValidator::forge('FirstValidator');
	$node = $validator->add_node_object('connection.port', ArrValidator_Node::forge(389));

	$node2_obj = ArrValidator_Node::forge('defaultserver.mydomain.com');
	$rules = array(
		array(
			'operator' => 'is_string'
		),
		array(
			'operator' => '!empty'
		)
	);
	$node2_obj->add_rules($rules);
	$node2 = $validator->add_node_object('connection.server', $node2_obj, true);

##### ArrValidator::add\_node\_array($name, array $array, $overwrite = false)

**Description:** Adds a node from an array if it does not exist. If the node already exists it will be returned. If the `$overwrite` flag is set to `true`, then the existing node will be overwritten.  
**Static:** No  
**Return:** `ArrValidator_Node` the added or previously existing node

	$validator = ArrValidator::forge('FirstValidator');
	$node_arr = array(
  		'default' => 389,
  		'rules' => array(
		)
	);
	$node = $validator->add_node_array('connection.port', $node_arr);

	$node2_arr = array(
		'default' => 'defaultserver.mydomain.com',
		'rules' => array(
			array(
				'operator' => 'is_string'
			),
			array(
				'operator' => '!empty'
			),
		),
	);
	$node2 = $validator->add_node_array('connection.server', $node2_arr, true);

##### ArrValidator::has\_node($name)

**Description:** Verifies if the node already exists in the validator. This only checks if the key exists in the node's array.  
**Static:** No  
**Return:** `bool`

	if(!$validator->has_node('connection.server'))
	{
		$rules = array(
			array(
				'operator' => 'is_string'
			),
			array(
				'operator' => '!empty'
			)
		);
		$validator->add_node('connection.server', 'defaultserver.mydomain.com')->add_rules($rules);
	}

##### ArrValidator::remove\_node($name)

**Description:** Removes a node from the validator.  
**Static:** No  
**Return:** `ArrValidator` this instance for chaining

	$validator->remove_node('connection.server')->remove_node('connection.port');

##### ArrValidator::add\_nodes(array $nodes, $overwrite = false)

**Description:** Adds an array of nodes to the validator. The array can contain `ArrValidator_Node` objects or `ArrValidator_Node` array structures. The nodes must be identified by a key name.  
**Static:** No  
**Return:** `Arr_Validator` this instance for chaining

	$validator = ArrValidator::forge('FirstValidator');
	$nodes = array(
		'connection.port' => array(
			'default' => 389,
			'rules' => array (
			)
		),
		'connection.server' => array(
			'default' => 'defaultserver.mydomain.com',
			'rules' => array(
				array(
					'operator' => 'is_string',
				), 
				array (
					'operator' => '!empty',
				)
			)
		)
	);
	$validator->add_nodes($nodes);

##### ArrValidator::empty\_nodes()

**Description:** Empties the validator node's array.  
**Static:** No  
**Return:** `Arr_Validator` this instance for chaining

	$validator->empty_nodes();

##### ArrValidator::as\_array($ommit_name = false)

**Description:** Gets the instance's array structure.  
**Static:** No  
**Return:** `array` the validator's array structure

	$array = $validator->as_array();
	$array2 = $validator->as_array(true);

The variable `$array` will have something like this:

	array(2) {
	  ["name"]=>
	  string(14) "FirstValidator"
	  ["nodes"]=>
	  array(2) {
	    ["connection.port"]=>
	    array(2) {
	      ["default"]=>
	      int(389)
	      ["rules"]=>
	      array(0) {
	      }
	    }
	    ["connection.server"]=>
	    array(2) {
	      ["default"]=>
	      string(26) "defaultserver.mydomain.com"
	      ["rules"]=>
	      array(2) {
	        [0]=>
	        array(1) {
	          ["operator"]=>
	          string(9) "is_string"
	        }
	        [1]=>
	        array(1) {
	          ["operator"]=>
	          string(6) "!empty"
	        }
	      }
	    }
	  }
	}

The variable `$array2` will have something like this:

	array(2) {
	  ["nodes"]=>
	  array(2) {
	    ["connection.port"]=>
	    array(2) {
	      ["default"]=>
	      int(389)
	      ["rules"]=>
	      array(0) {
	      }
	    }
	    ["connection.server"]=>
	    array(2) {
	      ["default"]=>
	      string(26) "defaultserver.mydomain.com"
	      ["rules"]=>
	      array(2) {
	        [0]=>
	        array(1) {
	          ["operator"]=>
	          string(9) "is_string"
	        }
	        [1]=>
	        array(1) {
	          ["operator"]=>
	          string(6) "!empty"
	        }
	      }
	    }
	  }
	}

##### ArrValidator::run(array &$array, $force\_item\_set = true)

**Description:** Runs the validator. Every node will be checked against the given array and if the node's validation fails the node's default value will be set into the array. If the item is not found and the `$force_item_set` flag is set to `true` then the item will be created with the default value.  
**Static:** No  
**Return:** `void`

	// Loads a validator with two nodes
	// Node: connection.server; Rules: is string and not empty
	// Node: connection.port; Rules: is_numeric between 1 and 5000
	$validator = ArrValidator::load_from_file('arrvalidator/connections/db')

	// Test case 1
	$array = array(
		'connection' => array(
			'server' => '',
			'port' => 0
		)
	);
	$validator->run($array);

	// Test case 2
	$array2 = array(
		'connection' => array(
			'server' => 'configured_server.mydomain.com'
		)
	);
	$validator->run($array2);

	// Test case 3
	$array3 = array(
		'connection' => array(
			'server' => 'default_server.mydomain.com',
			'port' => 0
		)
	);
	$validator->run($array3);

	// Test case 4
	$array4 = array(
		'connection' => array(
			'server' => 'default_server.mydomain.com',
			'port' => '2000'
		)
	);
	$validator->run($array4);

	// Test case 5
	$array5 = array(
		'connection' => array(
			'server' => 'default_server.mydomain.com',
			'port' => 2000
		)
	);
	$validator->run($array5);

For test case 1 the variable `$array` will contain this:

	array(1) {
	  ["connection"]=>
	  array(2) {
	    ["server"]=>
	    string(26) "defaultserver.mydomain.com"
	    ["port"]=>
	    int(389)
	  }
	}

For test case 2 the variable `$array2` will contain this:

	array(1) {
	  ["connection"]=>
	  array(2) {
	    ["server"]=>
	    string(30) "configured_server.mydomain.com"
	    ["port"]=>
	    int(389)
	  }
	}

For test case 3 the variable `$array3` will contain this:

	array(1) {
	  ["connection"]=>
	  array(2) {
	    ["server"]=>
	    string(27) "default_server.mydomain.com"
	    ["port"]=>
	    int(389)
	  }
	}

For test case 4 the variable `$array4` will contain this:

	array(1) {
	  ["connection"]=>
	  array(2) {
	    ["server"]=>
	    string(27) "default_server.mydomain.com"
	    ["port"]=>
	    string(4) "2000"
	  }
	}

For test case 5 the variable `$array5` will contain this:

	array(1) {
	  ["connection"]=>
	  array(2) {
	    ["server"]=>
	    string(27) "default_server.mydomain.com"
	    ["port"]=>
	    int(2000)
	  }
	}

#### ArrValidator\_Node class

To be written...

## Future development

The first version has the basic functionality one would expect. New features will be evaluated and added as soon as possible.
Please feel free to send feature requests through the Github repository.

## Special Thanks

Firstly I would like to thank the [Fuel Development Team](http://fuelphp.com/about) for their magnificent framework and spent time for making our lives easier. Great work, keep it up!

Special thanks for he ones that helped by commenting, discussing, suggesting, testing, brainstorming (if I missed someone please let me know, if you don't want to appear in this list also let me know):