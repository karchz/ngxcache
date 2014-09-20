<?php namespace Geeksdev\Ngxcache;

/*
 * This file is part of the Ngxcache package.
 *
 * (c) Geeksdev <vendor@geeks-dev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Config;

class Ngxcache
{

	/**
	 * Load configure.
	 *
	 * @return config
	 */
	private function getconfig()
	{

		$config = new \stdClass();

		$config->debug = !empty(Config::get('ngxcache::debug',false));
		$config->cache_dir = Config::get('ngxcache::nginx_cache_path','/var/run/nginx-cache');
		$config->level_keys = explode(':', Config::get('ngxcache::nginx_levels','1:2'));

		if(substr($config->cache_dir,-1,1)==DIRECTORY_SEPARATOR){
			$config->cache_dir = substr($config->cache_dir,0,-1);
		}

		return $config;
	}

	/**
	 * Set response X-Accel-Expires to expires value.
	 *
	 */
	public function enable()
	{
		header("X-Accel-Expires: ".Config::get('ngxcache::expires','86400'));
		\Config::set('session.driver', 'array');
	}


	/**
	 * Set response header X-Accel-Expires to 0.
	 *
	 */
	public function disable()
	{
		header("X-Accel-Expires: 0");
	}


	/**
	 * Search nginx cache of all.
	 *
	 * @return result
	 */
	public function items()
	{

		$result = new \stdClass();
		$config = $this->getconfig();

		$iterator = new \RecursiveDirectoryIterator($config->cache_dir);
		$iterator = new \RecursiveIteratorIterator($iterator);

		$result->files = array();
		$result->count = 0;

		foreach ($iterator as $fileinfo) {
			if ($fileinfo->isFile()) {
				$result->count++;
				$result->files[] = $fileinfo->getPathname();
			}
		}

		return $result;
	}

	/**
	 * Purge nginx cache of all.
	 *
	 * @return result
	 */
	public function purgeall()
	{

		$result = new \stdClass();
		$config = $this->getconfig();

		$result->config = $config;

		$iterator = new \RecursiveDirectoryIterator($config->cache_dir);
		$iterator = new \RecursiveIteratorIterator($iterator);
		$result->count = 0;

		$result->files = array();
		$result->dirs = array();

		foreach ($iterator as $fileinfo) {
			if ($fileinfo->isFile()) {
				$result->count++;
				$result->files[] = $fileinfo->getPathname();
			}else{
				if($fileinfo->getPath() != $config->cache_dir){
					$result->count++;
					$result->dirs[] = $fileinfo->getPath();
				}
			}
		}

		if(count($result->files) && !$config->debug){
			foreach ($result->files as $value) {
				unlink($value);
			}
		}

		if(count($result->dirs) && !$config->debug){
			$result->dirs = array_merge(array_unique($result->dirs));
			foreach (array_reverse($result->dirs) as $value) {
				rmdir($value);
			}
		}

		$result->success = true;

		if(!$config->debug){
			foreach ($iterator as $fileinfo) {
				if($fileinfo->getPath() != $config->cache_dir){
					$result->success = false;
				}
			}
		}


		return $result;
	}

	/**
	 * Purge or search Nginx cache.
	 *
	 * @param  string  $uri
	 * @param  bool    $searchmode
	 * @return result
	 */
	public function purge($uri,$searchmode=false)
	{

		$result = new \stdClass();
		$config = $this->getconfig();

		$result->config = $config;

		if($searchmode){
			$result->config->debug = true;
		}
		//Remove port number.
		$uri = preg_replace('/:[0-9]+$/', '/', $uri);
		$uri = preg_replace('/:[0-9]+\//', '/', $uri);

		$file_key  = md5($uri);
		$cache_file = $config->cache_dir;

		$offset = 0;
		foreach($config->level_keys as $level){
			$ilevel = intval($level);
			$offset -= $ilevel;
			$cache_file .=  DIRECTORY_SEPARATOR.substr($file_key,$offset,$ilevel);
		}

		$cache_file .=  DIRECTORY_SEPARATOR.$file_key;
		$cache_file_exist =  is_file($cache_file);

		$result->success = false;

		if($cache_file_exist) {
			if($this->backtrace($cache_file)){
				if($config->debug) {
					$result->success = true;
				} else {
					$result->success = unlink($cache_file);
				}
			}
		}

		$result->cache = $cache_file;

		return $result;

	}

	/**
	 * Rebuild Nginx cache.
	 *
	 * @param  string  $uri
	 * @return result
	 */
	public function rebuild($uri,$overwrite=false)
	{
		$result = new \stdClass();
		
		$config = $this->getconfig();
		$info = $this->purge($uri,$overwrite);

		if((!$info->success || ($overwrite && $info->success)) && !$config->debug){

			$content = file_get_contents($uri);

			$result = $this->purge($uri,true);
			if($result->success){
				$result->status = 'cached';
			}else{
				$result->status = 'notfound';
			}
		}else{
			$result = $info;
			$result->success = false;
			$result->status = 'exist';
		}

		return $result;
	}

	/**
	 * Backtrace uri from Nginx cache.
	 *
	 * @param  string  $cachePath
	 * @return string  $uri
	 */
	public function backtrace($cachePath)
	{

		$uri = new \stdClass();
		
		$file_content = file_get_contents($cachePath);
		$output = array();
		if(preg_match('/KEY\s*:\s*([^\n]*)/i', $file_content,$output)){
			$uri = strtolower($output[1]);
		}else{
			$uri = false;
		}

		return $uri;
	}
}