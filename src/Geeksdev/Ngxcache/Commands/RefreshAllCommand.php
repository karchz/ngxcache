<?php namespace Geeksdev\Ngxcache\Commands;

use Illuminate\Console\Command;

class RefreshAllCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ngxcache:refresh-all';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Nginx refresh and build cache of all.';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		echo PHP_EOL;
		$this->error('Warning!');
		if ($this->confirm('Do you want to rebuild by deleting the cache of all ? [y/N]',false)){
			echo PHP_EOL;
			$this->comment('Display port number depends on the setting of the nginx.');
			$info = \Ngxcache::items();
			foreach ($info->files as $key => $file) {
				$current = \Ngxcache::backtrace($file);
				if($current){
					$this->info('cached. => '.$current); 
					$rebuild = \Ngxcache::rebuild($current,true);
					if($rebuild->success){
						$this->info($key.'. '.$rebuild->cache);
					}else{
						$this->error('Writing error.');
					}
				}else{
					$this->error("Sourse uri is unknown.");
				}
			}
		}
	}

}
