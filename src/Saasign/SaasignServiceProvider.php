<?php
namespace Dingdone2017\Saasign;

/**
 * Created by PhpStorm.
 * User: Janice
 * Date: 2018/8/2
 * Time: 10:27
 */
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;

class SaasignServiceProvider extends BaseServiceProvider
{

    public function boot()
    {
        $this->publishes([$this->configPath() => config_path('saasign.php')]);
        $source = realpath(__DIR__.'/../../database/migrations/');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => database_path('migrations')], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfig();
        $this->app->singleton(Saasign::class, function ($app) {
            return new Saasign($app['config']->get('saasign'));
        });
        $provider = $this;

        $this->app->singleton(Member::class, function () use ($provider) {
            $storage = new Member($provider->app['db']);
            $storage->setConnectionName($provider->getConnectionName());
            return $storage;
        });
    }

    /**
     * Merges user's and entrust's configs.
     *
     * @return void
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(
            $this->configPath(), 'saasign'
        );
    }

    protected function configPath()
    {
        return __DIR__ . '/../../config/saasign.php';
    }

    public function getConnectionName()
    {
        return ($this->app['config']->get('saasign.database') !== 'default') ? $this->app['config']->get('saasign.database') : null;
    }
}