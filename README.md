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
$store = new connection('http://[domain.com]', 'Identifier', 'Key');
```


Methods (PUT)
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
Create:

```
$store->update('API',$params);
```
