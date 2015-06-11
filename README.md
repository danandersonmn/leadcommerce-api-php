Lead Commerce API Client
======================

PHP client for connecting to the Lead Commerce V2 REST API.

To learn more about the Lead Commerce API, visit:
http://www.leadcommerce.com/docs/api/overview.html

Requirements
------------

- PHP 5.3 or greater
- cUURL extension enabled

To connect to the Lead Commerce API, you will need the following

- URL pointing to a Lead Commerce instance.
- An active Identifier and Key fromt that store.
- The API authorization for the library being called

To create an API key, log into the back office and go to Settings > API > Create API License

Installation
------------

Require the file in your script:

```
require 'connection.php';
```


Instantiate the connection :

```
new connection('http://[domain.com]', 'Identifier', 'Key');
```


Methods
---------------------------------

List:
```
$store->getList('API',$params);
```

Info:
```
$store->getInfo('API','INFO API',$params);

```
ID:
```
$store->getID('API',$params);
```

Create:
```
$store->create('API',$params);
```

Update:
```
$store->update('API',$params);
```

Adjustment:
```
$store->adjustment('API',$params);
```

Examples
---------------------------------
List:
```
$store = new connection('http://www.leadcommerce.com', 'LCXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
$result = $store->getList('skus',array('status' => 1,'modify_start' => 0,'modify_end' => 1997473049));
echo $result;
```

Info:
```
$store = new connection('http://www.leadcommerce.com', 'LCXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
$result = $store->getInfo('skus','stock_level',array('warehouse'=>1,'in_stock_modify_start'=>0,'in_stock_modify_end'=>1997473049,'low_inventory'=>false));
echo $result;
```

ID:
```
$store = new connection('http://www.leadcommerce.com', 'LCXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
$result = $store->getID('customers',array('display_id' => array(1002,1003)));
echo $result;
```

Create:
```
$insert = array('name'         => 'FX Commerce',
                'status'       => 1,
                'terms'        => 2,
                'send_email'   => 0,
                'addresses'    => array(array('full_name'    => 'Fx Commerce - San Diego',
                                              'email'        => 'vendorfx@leadcommerce.web',
                                              'address_1'    => '5000 Business Parkway',
                                              'city'         => 'San Diego',
                                              'region'       => 'United States',
                                              'subregion'    => 'CA',
                                              'phone'        => '619-555-1212',
                                              'postal_code'  => '92103',
                                              'default'      => 'yes'),
                                        array('full_name'    => 'Fx Commerce - Los Angeles',
                                              'email'        => 'vendorlafx@leadcommerce.web',
                                              'address_1'    => '5000 Business Ave',
                                              'city'         => 'Los Angeles',
                                              'phone'        => '310-555-1958',
                                              'region'       => 'United States',
                                              'subregion'    => 'California',
                                              'postal_code'  => '08759')),
                'warehouses' => array(1));

$store = new connection('http://www.leadcommerce.com', 'LCXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
$result = $store->create('vendors',array('inserts'=> array($insert)));
echo $result;
```

Update:
```
$update = array('id'      => 2,
                'first'   => 'Artimo',
				'last'   => 'Segimo',
                'phone'   => '858-888-9999',
			    'fax'     => '858-777-8888',
				'password' => 'abcdef22',
				'status' => '1');
				        
$store = new connection('http://www.leadcommerce.com', 'LCXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
$result = $store->update('customers',array('updates'=> array($update)));
echo $result;
```

Adjustment: (Note: For the inventory adjustment api only)
```
$insert = array('id'       => 12,
                'warehouse'  => 1,
                'amount'     => 20,
                'reason'     => 5
                );



$store = new connection('http://www.leadcommerce.com', 'LCXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
$result = $store->adjustment('skus',array('inserts'=> array($insert)));
echo $result;
```
