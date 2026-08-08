<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    // `ext-intl` is an optional integration: `FallbackNameCreator` only uses it when the extension is loaded,
    // falling back to a transliteration map otherwise.
    ->ignoreErrorsOnExtensions(['ext-intl'], [ErrorType::SHADOW_DEPENDENCY]);
