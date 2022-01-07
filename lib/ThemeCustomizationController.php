<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vesi;

use WP_post;

/**
 * Class ThemeCustomizationController
 *
 * @package TMS\Theme\Base
 */
class ThemeCustomizationController implements \TMS\Theme\Base\Interfaces\Controller {

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_action( 'wp_head', [ $this, 'append_font_links' ] );
    }

    /**
     * Append font links
     *
     * @return void
     */
    public function append_font_links() : void {
        echo '
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;600;700;800&family=Secular+One&display=swap" rel="stylesheet">
        ';
    }
}
