<?php
declare(strict_types=1);

namespace Jhdxr\LaravelPruneDbCache;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->commands([
            Command\PruneDbCache::class,
        ]);
    }
}