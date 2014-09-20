<?php namespace Geeksdev\Ngxcache\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class RebuildCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ngxcache:rebuild';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Nginx cache rebuild. (URL argument is required.)';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire($overwrite=false)
	{
		$uri = $this->argument('uri');

		$result = \Ngxcache::rebuild($uri,$overwrite);
		if($result->success){
			$this->info('Rebuilding of the cache has been completed.');
			$this->info('0. '.$result->cache);
		}else{
			if($result->status == 'exist'){
				$this->error('Cache has been exist.');
				$this->error('0. '.$result->cache);
				echo PHP_EOL;
				if ($this->confirm('Do you want to rebuild by deleting the cache ? [y/N]',false)){
					\Ngxcache::purge($uri);
					$this->fire(true);
				}
			}else{
				$this->error('Writing error.');
			}
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
