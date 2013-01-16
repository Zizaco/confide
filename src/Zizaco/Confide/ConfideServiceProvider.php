<?php namespace Zizaco\Confide;

use App;
use Artisan;
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
		    return new Confide($app);
		});

	    $this->commands(
	    	'Zizaco\Confide\ControllerCommand',
	    	'Zizaco\Confide\RoutesCommand',
	    	'Zizaco\Confide\MigrationCommand'
	    );
	    
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
