<?php namespace Zizaco\Confide;

use App;
use Illuminate\Support\ServiceProvider;

class ConfideServiceProvider extends ServiceProvider {

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
		//echo "Confide<br>";
		$this->package('zizaco/confide');
	}

	/**
	 * Register the {{full_package}} service provider.
	 *
	 * @return void
	 */
	public function register()
	{

	    App::singleton('confide', function($app)
		{
		    return new Confide($app['view'], $app['config']);
		});
	    
	    include __DIR__.'/routes.php';
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('confide');
	}

}
