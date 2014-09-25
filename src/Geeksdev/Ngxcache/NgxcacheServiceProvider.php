<?php namespace Geeksdev\Ngxcache;

use Illuminate\Support\ServiceProvider;

class NgxcacheServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('geeks-dev/ngxcache');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		$this->app->bind('ngxcache',function(){
			return new Ngxcache;
		});
		
		$this->app['ngxcache.search'] = $this->app->share(function($app)
		{
			return new Commands\SearchCommand();
		});

		$this->app['ngxcache.show'] = $this->app->share(function($app)
		{
			return new Commands\ShowCommand();
		});

		$this->app['ngxcache.purge'] = $this->app->share(function($app)
		{
			return new Commands\PurgeCommand();
		});

		$this->app['ngxcache.purge-all'] = $this->app->share(function($app)
		{
			return new Commands\PurgeAllCommand();
		});

		$this->app['ngxcache.rebuild'] = $this->app->share(function($app)
		{
			return new Commands\RebuildCommand();
		});

		$this->app['ngxcache.refresh-all'] = $this->app->share(function($app)
		{
			return new Commands\RefreshAllCommand();
		});

		$this->app['ngxcache.backtrace'] = $this->app->share(function($app)
		{
			return new Commands\BacktraceCommand();
		});

		$this->commands(
			'ngxcache.search',
			'ngxcache.show',
			'ngxcache.purge',
			'ngxcache.purge-all',
			'ngxcache.rebuild',
			'ngxcache.refresh-all',
			'ngxcache.backtrace'
		);

	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('ngxcache');
	}

}
