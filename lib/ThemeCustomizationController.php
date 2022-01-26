<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vesi;

use Geniem\ACF\Field\TrueFalse;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType\Page;
use TMS\Theme\Base\PostType\Post;
use TMS\Theme\Vesi\ACF\Layouts\FaultMapLayout;

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

        add_filter(
            'tms/acf/field/fg_front_page_components_components/layouts',
            [ $this, 'alter_front_page_layouts' ]
        );

        add_filter(
            'tms/acf/field/fg_onepager_components_components/layouts',
            [ $this, 'alter_onepager_layouts' ]
        );

        add_filter(
            'tms/acf/field/fg_page_components_components/layouts',
            [ $this, 'alter_page_layouts' ]
        );

        add_filter( 'tms/gutenberg/blocks', [ $this, 'alter_blocks' ] );
        add_filter( 'tms/base/breadcrumbs/after_prepare', [ $this, 'alter_breadcrumbs' ] );
        add_filter( 'tms/theme/error404/search_link', [ $this, 'error404_search_link' ] );
        add_filter( 'tms/theme/error404/home_link', [ $this, 'error404_home_link' ] );
        add_filter( 'tms/theme/error404/alignment', [ $this, 'error404_alignment' ] );
        add_filter( 'tms/acf/tab/error404/fields', [ $this, 'remove_404_alignment_setting' ] );

        add_filter( 'tms/block/subpages/fields', [ $this, 'alter_subpages_fields' ] );
        add_filter( 'tms/block/key_figures/fields', [ $this, 'alter_key_figures_fields' ] );
        add_filter( 'tms/block/notice_banner/fields', [ $this, 'alter_notice_banner_fields' ], 10, 2 );
        add_filter( 'tms/acf/layout/_notice_banner/fields', [ $this, 'alter_notice_banner_fields' ], 10, 2 );
        add_filter( 'tms/theme/search/search_item', [ $this, 'search_classes' ] );
        add_filter( 'tms/theme/base/search_result_item', [ $this, 'alter_search_item' ] );

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
     * Alter front page layouts
     *
     * @param array $layouts ACF layouts.
     *
     * @return array
     */
    public function alter_front_page_layouts( $layouts ) {
        $layouts[] = FaultMapLayout::class;

        return $layouts;
    }

    /**
     * Alter one pager layouts
     *
     * @param array $layouts ACF layouts.
     *
     * @return array
     */
    public function alter_onepager_layouts( $layouts ) {
        $layouts[] = FaultMapLayout::class;

        return $layouts;
    }

    /**
     * Alter page layouts
     *
     * @param array $layouts ACF layouts.
     *
     * @return array
     */
    public function alter_page_layouts( $layouts ) {
        $layouts[] = FaultMapLayout::class;

        return $layouts;
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

    /**
     * Alter subpages block fields
     *
     * @param array $fields Array of ACF fields.
     *
     * @return array
     */
    public function alter_subpages_fields( array $fields ) : array {
        unset( $fields['background_color'] );

        return $fields;
    }

    /**
     * Alter key figures block fields
     *
     * @param array $fields Array of ACF fields.
     *
     * @return array
     */
    public function alter_key_figures_fields( array $fields ) : array {
        $fields['rows']->get_field( 'numbers' )->get_field( 'background_color' )->set_choices( [
            'primary' => 'Sininen',
            'light'   => 'Vaalea',
            'red'     => 'Punainen',
            'white'   => 'Valkoinen',
        ] );

        return $fields;
    }

    /**
     * Alter notice banner block fields
     *
     * @param array  $fields Array of ACF fields.
     * @param string $key    Parent key.
     *
     * @return array
     */
    public function alter_notice_banner_fields( array $fields, string $key ) : array {
        unset( $fields['background_color'] );

        try {
            $strings = [
                'is_alarm' => [
                    'label'        => 'Vakava huomio',
                    'instructions' => '',
                ],
            ];

            $is_alarm_field = ( new TrueFalse( $strings['is_alarm']['label'] ) )
                ->set_key( "${key}_is_alarm" )
                ->set_name( 'is_alarm' )
                ->use_ui()
                ->set_default_value( false )
                ->set_instructions( $strings['is_alarm']['instructions'] );

            $fields[] = $is_alarm_field;
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTraceAsString() );
        }

        return $fields;
    }

    /**
     * Search classes.
     *
     * @param array $classes Search view classes.
     *
     * @return array
     */
    public function search_classes( $classes ) : array {
//        $classes['search_item']          = 'has-border-1 has-border-divider-invert';
//        $classes['search_item_excerpt']  = 'has-text-small';
//        $classes['search_form']          = 'has-colors-accent-secondary';
        $classes['search_filter_button'] = 'is-primary';

//        $classes['event_search_section'] = 'has-border-bottom-1 has-border-divider-invert';

        return $classes;
    }

    public function alter_search_item( $search_item ) {
        $search_item->content_type     = false;
        $search_item->meta['category'] = false;

        $search_item->post_excerpt = wp_trim_words( $search_item->post_content, 30 );
    }
}
