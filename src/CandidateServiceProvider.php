<?php

namespace Jeylabs\Candidate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container as Application;

class CandidateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = __DIR__ . '/config/candidate.php';
        $this->publishes([$source => config_path('candidate.php')]);
        $this->mergeConfigFrom($source, 'candidate');
    }

    public function register()
    {
        $this->registerBindings($this->app);
    }

    protected function registerBindings(Application $app)
    {
        $app->singleton('candidate', function ($app) {
            $config = $app['config'];
            return new Candidate(
                $config->get('candidate.secret_key', null),
                $config->get('candidate.candidate_api_babe_uri', null),
                $config->get('candidate.async_requests', false)
            );
        });
        $app->alias('candidate', Candidate::class);

    }
}
