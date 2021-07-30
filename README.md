# Yii 2.0 Extension for Zumba ***Swivel***

This plugin provides the necessary elements for using Swivel in a Yii 2.0+ Application.

This plugin is based on the zumba/swivel-cake plugin.

### Configuration for Yii Component:

```php
 'swivel' => [ 
 	'class'=>'dhluther\swivel\SwivelComponent' 
 ],
```

### Access from the application
```php
// If the user has the feature behavior bucket enabled, use the testFeature.New.Something behavior,
// else use the default
Yii::$app->swivel->forFeature( 'testFeature' )
	->addBehavior('New.Something', [$this,'doSomethingB'], $args)
	->defaultBehavior([$this, 'doSomethingA'], $args )
	->execute();

// If the user has the feature behavior bucket enabled, use the first callable,
// else use the second callable (default)
Yii::$app->swivel->invoke('testFeature.New.Something', [$this,'doSomethingB'],[$this, 'doSomethingA']);
```

Make sure that your bootstrap file is set to properly include the composer autoloader.

In the index.php bootstrap:
```php
require(__DIR__ . '/../vendor/autoload.php');
```

### Add the swivel migration namespace to your migration command config
```php
'migrationNamespaces'=>[
    'dhluther\\swivel\\migrations'
]
```
with the alias of:
```php
'@dhluther\swivel'=>'@vendor/dhluther/swivel/src'
```

If you've already migrated this package in the past and want to mark it to the current migratoin via namespace, the command is
```shell
./yii migrate/mark dhluther\\swivel\\migrations\\m190812_083802
```

###--OR--
### Migrate swivel once
Initialize the swivel table by running the following migration, after configuring the component in your application:
```php
./yii migrate --migrationPath=@dhluther/swivel/migrations
```

### To add the composer vendor libraries for local development:
```shell
docker run --rm -v $PWD:/app composer update
```

#### The ***Swivel*** Libraries 

https://github.com/zumba/swivel 

https://github.com/zumba/swivel-cake
