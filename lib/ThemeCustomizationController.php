<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vesi;

use TMS\Theme\Base\PostType\Page;
use TMS\Theme\Base\PostType\Post;

/**
 * Class ThemeCustomizationController
 *
 * @package TMS\Theme\Vesi
 */
class ThemeCustomizationController implements \TMS\Theme\Base\Interfaces\Controller {

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_action( 'wp_head', [ $this, 'append_font_links' ] );

        add_filter( 'tms/gutenberg/blocks', [ $this, 'alter_blocks' ] );
        add_filter( 'tms/base/breadcrumbs/after_prepare', [ $this, 'alter_breadcrumbs' ] );
        add_filter( 'tms/theme/error404/search_link', [ $this, 'error404_search_link' ] );
        add_filter( 'tms/theme/error404/home_link', [ $this, 'error404_home_link' ] );
        add_filter( 'tms/theme/error404/alignment', [ $this, 'error404_alignment' ] );
        add_filter( 'tms/acf/tab/error404/fields', [ $this, 'remove_404_alignment_setting' ] );
    }

    /**
     * Alter theme blocks
     *
     * @param array $blocks Theme blocks.
     *
     * @return array
     */
    public function alter_blocks( $blocks ) {
        $blocks['acf/fault-map'] = [
            'post_types' => [
                Post::SLUG,
                Page::SLUG,
            ],
        ];

        return $blocks;
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
     * Alter breadcrumbs
     *
     * @param array $breadcrumbs Breadcrumbs.
     *
     * @return array
     */
    public function alter_breadcrumbs( $breadcrumbs ) {
        if ( empty( $breadcrumbs ) ) {
            return $breadcrumbs;
        }

        return array_map( function ( $crumb ) {
            $crumb['separator'] = $crumb['permalink'] ? 'droplet' : false;

            return $crumb;
        }, $breadcrumbs );
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
     * 404 text alignment
     *
     * @param string $alignment Alignment class.
     *
     * @return string
     */
    public function error404_alignment( string $alignment ) : string {
        $alignment = 'has-text-centered';

        return $alignment;
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
