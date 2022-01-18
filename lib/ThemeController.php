<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vesi;

use TMS\Theme\Base\Interfaces;
use TMS\Theme\Vesi\ThemeCustomizationController;

/**
 * ThemeController
 */
class ThemeController extends \TMS\Theme\Base\ThemeController {

    /**
     * Init classes
     */
    protected function init_classes() : void {
        $classes = [
            ACFController::class,
            Assets::class,
            BlocksController::class,
            FormatterController::class,
            Localization::class,
            PostTypeController::class,
            TaxonomyController::class,
            ThemeCustomizationController::class,
        ];

        array_walk( $classes, function ( $class ) {
            $instance = new $class();

            if ( $instance instanceof Interfaces\Controller ) {
                $instance->hooks();
            }
        } );
    }
}
