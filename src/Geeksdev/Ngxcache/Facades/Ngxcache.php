<?php namespace Geeksdev\Ngxcache\Facades;

/*
 * This file is part of the Ngxcache package.
 *
 * (c) Geeksdev <vendor@geeks-dev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Facades\Facade;

class Ngxcache extends Facade{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor(){ return 'ngxcache';}

}
