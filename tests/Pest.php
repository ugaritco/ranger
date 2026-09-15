<?php

use Ugarit\Ranger\Collectors\Collector;
use Ugarit\Ranger\Ranger;
use Ugarit\Ranger\Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->in('Feature', 'Unit')
    ->beforeEach(function () {
        $this->app->resolving(Ranger::class, function (Ranger $ranger) {
            $ranger->setAppPaths($this->app->path());
            $ranger->setBasePaths($this->app->basePath());
        });

        $this->app->resolving(Collector::class, function (Collector $collector) {
            $collector->setAppPaths($this->app->path());
            $collector->setBasePaths($this->app->basePath());
        });
    });

// Mock Ugarit 13 Eloquent attribute classes if running in Ugarit 11/12 environment
if (! class_exists('Heritage\Database\Eloquent\Attributes\Visible')) {
    require_once __DIR__.'/Mocks/Ugarit13Attributes.php';
}
