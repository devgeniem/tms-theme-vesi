<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vesi;

use TMS\Theme\Vesi\Taxonomy\ArtistCategory;
use TMS\Theme\Vesi\Taxonomy\ArtworkLocation;
use TMS\Theme\Vesi\Taxonomy\ArtworkType;

/**
 * Class Localization
 *
 * @package TMS\Theme\Vesi
 */
class Localization extends \TMS\Theme\Base\Localization implements \TMS\Theme\Base\Interfaces\Controller {

    /**
     * Load theme translations.
     */
    public function load_theme_textdomains() {
        \load_theme_textdomain(
            'tms-theme-base',
            get_template_directory() . '/lang'
        );

        \load_child_theme_textdomain(
            'tms-theme-vesi',
            get_stylesheet_directory() . '/lang'
        );
    }
}
