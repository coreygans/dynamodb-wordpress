dynamodb-wordpress
==================

This project is not maintained

Plugin for Wordpress which interacts with DynamoDB

This Plugin is a first attempt at surfacing results from DynamoDB within Wordpress leveraging the SimpleAmazon SDK. This has a lot of hard coded components to it, but is functional.

All of the configuration is in amazonDbQuery-public.php, specifically:

	var $AmazonKeyPub = 'ENTER PUBLIC KEY';
	var $AmazonKeySec = 'ENTER SECRET';

Also the output is hardcoded at the bottom which I've made generic. But you can set the table name here:
	'TableName' => '{Insert Table Name}',

   and then modify the output to meet your requirements.

There are definitely better ways to approach this, but wanted to make it available for others to leverage and benefit from.
