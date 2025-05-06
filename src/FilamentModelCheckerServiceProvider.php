<?php

namespace Vvb13a\LaravelModelChecker;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentModelCheckerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-model-checker')
            ->hasViews();
    }
}