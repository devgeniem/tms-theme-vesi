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

        add_filter( 'tms/theme/error404/search_link', [ $this, 'error404_search_link' ] );
        add_filter( 'tms/theme/error404/home_link', [ $this, 'error404_home_link' ] );
        add_filter( 'tms/acf/tab/error404/fields', [ $this, 'remove_404_alignment_setting' ] );
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
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;600;700;800&family=Secular+One&display=swap" rel="stylesheet">'; // phpcs:ignore
    }

    /**
     * 404 home link
     *
     * @param array $link Home link.
     *
     * @return array
     */
    public function error404_home_link( array $link ) : array {
        $link['classes'] = 'is-primary';
        $link['icon']    = 'arrow-right';
        $link['class']   = 'icon--medium';

        return $link;
    }

    /**
     * 404 search link
     *
     * @param array $link Search link.
     *
     * @return array
     */
    public function error404_search_link( array $link ) : array {
        $link['classes'] = 'is-primary';
        $link['class']   = 'icon--medium';

        return $link;
    }

    /**
     * Remove 404 alignment field
     *
     * @param array $fields Tab fields.
     *
     * @return array
     */
    public function remove_404_alignment_setting( array $fields ) : array {
        return array_filter( $fields, fn( $f ) => $f->get_name() !== '404_alignment' );
    }
}
