<?php
/**
 * Copyright (c) 2022. Geniem Oy
 */

namespace TMS\Theme\Vesi\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class HeroFields
 *
 * @package TMS\Theme\Vesi\ACF\Fields
 */
class HeroFields extends \Geniem\ACF\Field\Group {

    /**
     * The constructor for field.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        parent::__construct( $label, $key, $name );

        try {
            $this->add_fields( $this->sub_fields() );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws \Geniem\ACF\Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'image'       => [
                'label'        => 'Kuva',
                'instructions' => '',
            ],
            'video'       => [
                'label'        => 'Videon URL',
                'instructions' => 'Dreambroker-linkki',
            ],
            'title'       => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description' => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'link'        => [
                'label'        => 'Painike',
                'instructions' => '',
            ],
            'align'       => [
                'label'        => 'Tekstin tasaus',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $image_field = ( new Field\Image( $strings['image']['label'] ) )
            ->set_key( "${key}_image" )
            ->set_name( 'image' )
            ->set_return_format( 'id' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['image']['instructions'] );

        $video_field = ( new Field\URL( $strings['video']['label'] ) )
            ->set_key( "${key}_video" )
            ->set_name( 'video' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['video']['instructions'] );

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_rows( 4 )
            ->set_new_lines( 'wpautop' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['description']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "${key}_link" )
            ->set_name( 'link' )
            ->set_wrapper_width( 40 )
            ->set_instructions( $strings['link']['instructions'] );

        $align_field = ( new Field\Select( $strings['align']['label'] ) )
            ->set_key( "${key}_align" )
            ->set_name( 'align' )
            ->set_choices( [
                'left'  => 'Vasen',
                'right' => 'Oikea',
            ] )
            ->set_default_value( 'right' )
            ->set_wrapper_width( 30 )
            ->set_instructions( $strings['align']['instructions'] );

        return [
            $image_field,
            $video_field,
            $title_field,
            $description_field,
            $link_field,
            $align_field,
        ];
    }
}
