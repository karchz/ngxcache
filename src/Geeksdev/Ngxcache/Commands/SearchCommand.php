<?php namespace Geeksdev\Ngxcache\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class SearchCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ngxcache:search';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Nginx search single cache. (URL argument is required.)';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		$uri = $this->argument('uri');

		$result = \Ngxcache::purge($uri,true);

		if($result->success) {
			$this->info('0. ' . $result->cache);
			$this->info('Cache has been hit.');
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
