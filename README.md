# shaper-framework
###  Introduction
This is a simple super fast PHP famework with a Node.JS approach based on MVC for REST API.
clone the repository to a direcory and start working. 
Create a router page in app/routes/ to control and assign routes

```php
<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));
global $app;

$app->post('/home', function($req, $res) {
  $res->success('You successfully landed on the home route via POST!');
}

$app->get('/about', function($req, $res) {
  $res->success('You successfully landed on the about route via get!');
}
```
