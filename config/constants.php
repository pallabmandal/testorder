<?php
return [
	'roles' => [
		'admin' => 1,
		'customer' => 2
	],
	'status' => [
		'inactive' => 0,
		'active' => 1,
		'payment_completed' => 2,
		'payment_rejected' => 3,

		0 => 'inactive',
		1 => 'active',
		2 => 'payment_completed',
		3 => 'payment_rejected'
	],
	'payment_methods' => [
		'cod',
		'superpay',
		'razorpay'
	],
	'payment_helpers' => [
		'cod' => '\App\Helpers\CodHelper',
		'superpay' => '\App\Helpers\SuperPayHelper',
		'razorpay' => '\App\Helpers\RazorPayHelper'
	]
];