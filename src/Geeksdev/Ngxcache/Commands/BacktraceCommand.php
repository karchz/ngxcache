<?php namespace Geeksdev\Ngxcache\Commands;

use Illuminate\Console\Command;

class BacktraceCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ngxcache:backtrace';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Nginx display URL by tracing back all caches.';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$result = \Ngxcache::items();

		if($result->count == 0){
			$this->comment('Cache directory is empty.');
		}else{
			foreach ($result->files as $key => $value) {
				$this->info($key.'. '.\Ngxcache::backtrace($value));
			}
			$this->info('Caches has been hit.');
		}

	}

}
