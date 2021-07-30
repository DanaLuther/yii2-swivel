<?php
/**
 * swivel.php
 *
 * @created 7/27/21
 * @version 1.0
 * @author Dana Luther <dana.luther@gmail.com>
 */

use League\FactoryMuffin\Faker\Facade as Faker;

/** @var \League\FactoryMuffin\FactoryMuffin $fm */
try
{
	$fm->define(\dhluther\swivel\SwivelFeature::class)->setDefinitions([
		'slug'=>Faker::word(),
		'buckets'=>Faker::numberBetween(1, 11),
	]);
}
catch (\League\FactoryMuffin\Exceptions\DefinitionAlreadyDefinedException $e)
{

}