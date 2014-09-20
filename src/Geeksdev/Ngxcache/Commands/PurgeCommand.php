<?php namespace Geeksdev\Ngxcache\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PurgeCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ngxcache:purge';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Nginx purge single cache. (URL argument is required.)';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{


		$uri = $this->argument('uri');

		$result = \Ngxcache::purge($uri);

		if($result->success) {
			$this->info('0. ' . $result->cache);
			if($result->config->debug){
				$this->info('(Debug Mode) Cache has been hit.');
			}else{
				$this->info('Cache has been removed.');
			}
		} else {
			$this->error('0. ' . $result->cache);
			$this->error('No such cached file.');
		}

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('uri', InputArgument::REQUIRED, 'An uri argument.'),
		);
	}

}
