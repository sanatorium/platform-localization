<?php namespace Sanatorium\Localization\Providers;

use Cartalyst\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		// Register the attributes namespace
		$this->app['platform.attributes.manager']->registerNamespace(
			$this->app['Sanatorium\Localization\Models\Language']
		);

		// Subscribe the registered event handler
		$this->app['events']->subscribe('sanatorium.localization.language.handler.event');

		// Register all the default hooks
        $this->registerHooks();

        // Register the Blade @localize widget.
        $this->registerBladeLocalizeDirective();

		$this->prepareResources();
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('sanatorium.localization.language', 'Sanatorium\Localization\Repositories\Language\LanguageRepository');

		// Register the data handler
		$this->bindIf('sanatorium.localization.language.handler.data', 'Sanatorium\Localization\Handlers\Language\LanguageDataHandler');

		// Register the event handler
		$this->bindIf('sanatorium.localization.language.handler.event', 'Sanatorium\Localization\Handlers\Language\LanguageEventHandler');

		// Register the validator
		$this->bindIf('sanatorium.localization.language.validator', 'Sanatorium\Localization\Validator\Language\LanguageValidator');
	}

	/**
     * Register all hooks.
     *
     * @return void
     */
    protected function registerHooks()
    {
        $hooks = [
            'shop.header' => 'sanatorium/localization::hooks.languages',
            'language.switch' => 'sanatorium/localization::hooks.languages',
        ];

        $manager = $this->app['sanatorium.hooks.manager'];

        foreach ($hooks as $position => $hook) {
            $manager->registerHook($position, $hook);
        }
    }

    /**
     * Register the Blade @localize directive.
     *
     * @return void
     */
    protected function registerBladeLocalizeDirective()
    {
        $this->app['blade.compiler']->directive('localize', function ($value) {
            return "<?php echo Widget::make('sanatorium/localization::language.show', array$value); ?>";
        });
    }

	/**
	 * Prepare the package resources.
	 *
	 * @return void
	 */
	protected function prepareResources()
	{
		$config = realpath(__DIR__.'/../../config/config.php');

		$this->mergeConfigFrom($config, 'sanatorium-localization');

		$this->publishes([
			$config => config_path('sanatorium-localization.php'),
		], 'config');
	}

}
