<?php namespace Geeksdev\Ngxcache\Commands;

use Illuminate\Console\Command;

class PurgeAllCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ngxcache:purge-all';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Nginx purge cache of all.';


	/**
	 * Execute the console command.
	 *
	 */
	public function fire()
	{
		$result = \Ngxcache::purgeall();

		if($result->count == 0){
			$this->comment('Cache directory is empty.');
		}else{
			if($result->success){
				foreach ($result->files as $key => $value) {
					$this->info($key.'. '.$value);
				}
				if($result->config->debug){
					$this->info('(Debug Mode) Caches has been hit.');
				}else{
					$this->info('Caches has been removed.');
				}
			}else{
				$this->error('Was not possible to empty the cache directory.');
			}

		}

	}

}
