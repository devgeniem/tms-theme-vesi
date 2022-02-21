<?php

namespace TMS\Theme\Vesi\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class PriceCalculatorTampereFields
 *
 * @package TMS\Theme\Vesi\ACF\Fields
 */
class PriceCalculatorTampereFields extends \Geniem\ACF\Field\Group {

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
            'title'       => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description' => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\ExtendedWysiwyg( $strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_tabs( 'visual' )
            ->set_toolbar( 'tms-minimal' )
            ->disable_media_upload()
            ->set_height( 100 )
            ->set_instructions( $strings['description']['instructions'] );

        return [
            $title_field,
            $description_field,
        ];
    }
}
