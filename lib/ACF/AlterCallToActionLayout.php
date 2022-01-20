<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use Geniem\ACF\Field\Select;
use TMS\Theme\Base\Logger;

/**
 * Alter Call to Action Layout
 */
class AlterCallToActionLayout {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter(
            'tms/acf/layout/_call_to_action/fields',
            [ $this, 'alter_fields' ],
            10,
            2
        );

        add_filter(
            'tms/acf/layout/call_to_action/data',
            [ $this, 'alter_format' ],
            20
        );
    }

    /**
     * Alter fields
     *
     * @param array  $fields Array of ACF fields.
     * @param string $key    Layout key.
     *
     * @return array
     */
    public function alter_fields( array $fields, string $key ) : array {
        try {
            $strings = [
                'background_color' => [
                    'label'        => 'Taustaväri',
                    'instructions' => '',
                ],
            ];

            $background_field = ( new Select( $strings['background_color']['label'] ) )
                ->set_key( "${key}_background_color" )
                ->set_name( 'background_color' )
                ->use_ui()
                ->set_choices( [
                    'light' => 'Vaalea',
                    'dark'  => 'Tumma',
                ] )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['background_color']['instructions'] );

            $fields['rows']->add_field( $background_field );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return $fields;
    }

    /**
     * Format layout data
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    public function alter_format( array $layout ) : array {
        if ( empty( $layout['rows'] ) ) {
            return $layout;
        }

        foreach ( $layout['rows'] as $key => $row ) {
            $layout['rows'][ $key ]['button_class'] = 'is-primary';

            if ( $row['background_color'] === 'light' ) {
                $layout['rows'][ $key ]['button_class'] = 'is-secondary';
            }
        }

        return $layout;
    }
}

( new AlterCallToActionLayout() );
