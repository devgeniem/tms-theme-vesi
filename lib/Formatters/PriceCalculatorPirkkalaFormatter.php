<?php

namespace TMS\Theme\Vesi\Formatters;

/**
 * Class PriceCalculatorTampereFormatter
 *
 * @package TMS\Theme\Vesi\Formatters
 */
class PriceCalculatorPirkkalaFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'PriceCalculatorTampere';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/price-calculator-pirkkala/data',
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
            'usage_option_1'       => __( 'Erillinen pientalo', 'tms-theme-vesi' ),
            'usage_option_2'       => __( 'Rivi- tai ketjutalo', 'tms-theme-vesi' ),
            'usage_option_3'       => __( 'Asuinkerrostalo', 'tms-theme-vesi' ),
            'usage_option_4'       => __( 'Toimisto- tai liikerakennus', 'tms-theme-vesi' ),
            'usage_option_5'       => __( 'Teollisuus- tai varastorakennus', 'tms-theme-vesi' ),
            'usage_option_6'       => __( 'Pysäköintirakennus', 'tms-theme-vesi' ),
            'usage_option_8'       => __( 'Kylmä huoltoasema', 'tms-theme-vesi' ),
            'usage_option_9'       => __( 'Muu rakennus', 'tms-theme-vesi' ),
            'floor_area'           => __( 'Rakennusoikeus (m2)', 'tms-theme-vesi' ),
            'services'             => __( 'Palveluiden käyttö', 'tms-theme-vesi' ),
            'sewer'                => __( 'Viemäri', 'tms-theme-vesi' ),
            'run_off_water'        => __( 'Hulevesi', 'tms-theme-vesi' ),
            'water_pipe'           => __( 'Vesijohto', 'tms-theme-vesi' ),
            'usage'                => __( 'Käyttötarkoitus', 'tms-theme-vesi' ),
            'calculate'            => __( 'Laske', 'tms-theme-vesi' ),
            'price_without_vat'    => __( 'Veroton hinta', 'tms-theme-vesi' ),
            'vat'                  => __( 'alv 24%', 'tms-theme-vesi' ),
            'price_with_vat'       => __( 'Hinta sis. alv', 'tms-theme-vesi' ),
            'prices_waste'         => __( 'Liittymismaksu jätevesi', 'tms-theme-vesi' ),
            'prices_water'         => __( 'Liittymismaksu vesi', 'tms-theme-vesi' ),
            'prices_run_off_water' => __( 'Liittymismaksu hulevesi', 'tms-theme-vesi' ),
            'total'                => __( 'Yhteensä', 'tms-theme-vesi' ),
            'choose_usage_type'    => __( 'Valitse käyttökohde', 'tms-theme-vesi' ),
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

        if ( empty( $_GET['water'] ) && empty( $_GET['waste'] ) && empty( $_GET['run_off_water'] ) ) {
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

            $data = $this->calculate_prices( $data, $floor_area_touched, $usage_factor );
        }

        $data['floor_area']    = $floor_area;
        $data['usage_options'] = $this->usage_options();
        $data['strings']       = $strings;

        return $data;
    }

    /**
     * Calculate prices
     *
     * @param array     $data               Block data.
     * @param int|float $floor_area_touched Altered floor area.
     * @param int       $usage_factor       Usage factor.
     *
     * @return array
     */
    public function calculate_prices( $data, $floor_area_touched, $usage_factor ) : array {
        $data['prices'] = [];
        $factor_map     = [
            'water'         => 1.448,
            'waste'         => 1.81,
            'run_off_water' => 0.362,
        ];

        if ( isset( $_GET['water'] ) && intval( $_GET['water'] ) === 1 ) {
            $data['water_checked'] = 'checked';
            $price                 = $floor_area_touched * $factor_map['water'] * $usage_factor;

            $data['prices']['water'] = [
                'price'          => $price,
                'vat'            => $price * 0.24,
                'price_with_vat' => $price * 1.24,
            ];
        }

        if ( isset( $_GET['waste'] ) && intval( $_GET['waste'] ) === 1 ) {
            $data['waste_checked'] = 'checked';
            $price                 = $floor_area_touched * $factor_map['waste'] * $usage_factor;

            $data['prices']['waste'] = [
                'price'          => $price,
                'vat'            => $price * 0.24,
                'price_with_vat' => $price * 1.24,
            ];
        }

        if ( isset( $_GET['run_off_water'] ) && intval( $_GET['run_off_water'] ) === 1 ) {
            $data['run_off_water_checked'] = 'checked';
            $price                         = $floor_area_touched * $factor_map['run_off_water'] * $usage_factor;

            $data['prices']['run_off_water'] = [
                'price'          => $price,
                'vat'            => $price * 0.24,
                'price_with_vat' => $price * 1.24,
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
