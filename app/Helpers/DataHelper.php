<?php
namespace App\Helpers;

use Validator;
use DB;
use Illuminate\Support\Arr;

class DataHelper
{
	public static function createFirstLastName($data)
	{
		$data = trim($data);
		if(empty($data)){
			return [];
		} else {
			return explode(" ", $data);
		}
	}
}
