<?php namespace Smartsoftware\Modelworkflow;

use Illuminate\Support\ServiceProvider;

/**
 * @author Martin Alejandro Santangelo <msantangelo@smartsoftware.com.ar>
 * @copyright SmartSoftware Argentina
 */
class ModelworkflowServiceProvider extends ServiceProvider {

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
		$this->package('smartsoftware/modelworkflow');
		$this->loadTranslationsFrom(__DIR__.'../../lang', 'modelworkflow');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
