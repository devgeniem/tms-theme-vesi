<?php

namespace TMS\Theme\Vesi\Formatters;

/**
 * Class NewPriceCalculatorTampereFormatter
 *
 * @package TMS\Theme\Vesi\Formatters
 */
class NewPriceCalculatorTampereFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'NewPriceCalculatorTampere';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/new-price-calculator-tampere/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Block strings
     *
     * @return array
     */
    public function strings() : array {
        return [
            'usage_option_1'    => __( 'Detached house', 'tms-theme-vesi' ),
            'usage_option_2'    => __( 'Row house or linked house', 'tms-theme-vesi' ),
            'usage_option_3'    => __( 'Apartment building', 'tms-theme-vesi' ),
            'usage_option_4'    => __( 'Office building or commercial building', 'tms-theme-vesi' ),
            'usage_option_5'    => __( 'Industrial building or warehouse', 'tms-theme-vesi' ),
            'usage_option_6'    => __( 'Parking facility', 'tms-theme-vesi' ),
            'usage_option_8'    => __( 'Unattended service station', 'tms-theme-vesi' ),
            'usage_option_9'    => __( 'Other building', 'tms-theme-vesi' ),
            'floor_area'        => __( 'Permitted building volume (m2)', 'tms-theme-vesi' ),
            'services'          => __( 'Use of services', 'tms-theme-vesi' ),
            'sewer'             => __( 'Sewer', 'tms-theme-vesi' ),
            'water_pipe'        => __( 'Water pipeline', 'tms-theme-vesi' ),
            'usage'             => __( 'Purpose of use', 'tms-theme-vesi' ),
            'calculate'         => __( 'Calculate', 'tms-theme-vesi' ),
            'price_without_vat' => __( 'Price excluding VAT', 'tms-theme-vesi' ),
            'vat'               => __( 'VAT 25,5%', 'tms-theme-vesi' ),
            'price_with_vat'    => __( 'Price including VAT', 'tms-theme-vesi' ),
            'prices_waste'      => __( 'Connection charge for waste water', 'tms-theme-vesi' ),
            'prices_water'      => __( 'Connection charge for water', 'tms-theme-vesi' ),
            'total'             => __( 'Total', 'tms-theme-vesi' ),
            'choose_usage_type' => __( 'Choose application', 'tms-theme-vesi' ),
        ];
    }

    /**
     * Get usage options for form
     *
     * @return array|\string[][]
     */
    public function usage_options() {
        $usage   = isset( $_GET['usage'] ) ? sanitize_text_field( wp_unslash( $_GET['usage'] ) ) : null;
        $strings = $this->strings();

        $options = [
            [
                'value' => '1,6',
                'text'  => $strings['usage_option_1'],
            ],
            [
                'value' => '2,4',
                'text'  => $strings['usage_option_2'],
            ],
            [
                'value' => '3,3',
                'text'  => $strings['usage_option_3'],
            ],
            [
                'value' => '4,3',
                'text'  => $strings['usage_option_4'],
            ],
            [
                'value' => '5,3',
                'text'  => $strings['usage_option_5'],
            ],
            [
                'value' => '6,1',
                'text'  => $strings['usage_option_6'],
            ],
            [
                'value' => '8,3',
                'text'  => $strings['usage_option_8'],
            ],
            [
                'value' => '9,3',
                'text'  => $strings['usage_option_9'],
            ],
        ];

        foreach ( $options as $key => $option ) {
            if ( $option['value'] === $usage ) {
                $options[ $key ]['selected'] = 'selected';
            }
        }

        return $options;
    }

    /**
     * Format data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        $data['form_id']     = wp_unique_id( 'lomake-' );
        $data['current_url'] = get_the_permalink() . '#' . $data['form_id'];
        $strings             = $this->strings();

        $usage              = isset( $_GET['usage'] )
            ? sanitize_text_field( wp_unslash( $_GET['usage'] ) )
            : null;
        $floor_area         = isset( $_GET['floor_area'] )
            ? floatval( sanitize_text_field( wp_unslash( $_GET['floor_area'] ) ) )
            : null;
        $floor_area_touched = $floor_area;

        if ( empty( $_GET['water'] ) && empty( $_GET['waste'] ) ) {
            $data['notice'] = $strings['choose_usage_type'];
        }

        if ( ! empty( $usage ) && ! empty( $floor_area ) ) {
            $usage        = explode( ',', $usage );
            $usage_type   = intval( $usage[0] );
            $usage_factor = intval( $usage[1] );

            if ( $usage_type === 1 && $floor_area < 250 ) {
                $floor_area_touched = 250;
            }

            if ( $usage_type === 2 && $floor_area < 500 ) {
                $floor_area_touched = 500;
            }

            $total          = $floor_area_touched * 1.84 * $usage_factor;
            $data['prices'] = [];
            $data           = $this->calculate_prices( $data, $total );
        }

        $data['floor_area']    = $floor_area;
        $data['usage_options'] = $this->usage_options();
        $data['strings']       = $strings;

        return $data;
    }

    /**
     * Calculate prices
     *
     * @param array     $data  Block data.
     * @param int|float $total Total price.
     *
     * @return array
     */
    public function calculate_prices( $data, $total ) : array {
        if ( isset( $_GET['water'] ) && intval( $_GET['water'] ) === 1 ) {
            $data['water_checked'] = 'checked';

            $data['prices']['water'] = [
                'price'          => $total,
                'vat'            => $total * 0.255,
                'price_with_vat' => $total * 1.255,
            ];
        }

        if ( isset( $_GET['waste'] ) && intval( $_GET['waste'] ) === 1 ) {
            $data['waste_checked'] = 'checked';

            $data['prices']['waste'] = [
                'price'          => $total,
                'vat'            => $total * 0.255,
                'price_with_vat' => $total * 1.255,
            ];
        }

        if ( count( $data['prices'] ) > 1 ) {
            $data['prices']['total'] = [
                'price'          => $data['prices']['waste']['price'] * 2,
                'vat'            => $data['prices']['waste']['vat'] * 2,
                'price_with_vat' => $data['prices']['waste']['price_with_vat'] * 2,
            ];
        }

        foreach ( $data['prices'] as $key => $values ) {
            foreach ( $values as $type => $val ) {
                $data['prices'][ $key ][ $type ] = number_format_i18n( $val, 2, ',', '' ) . ' €';
            }
        }

        return $data;
    }
}
