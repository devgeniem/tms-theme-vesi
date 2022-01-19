<?php
/**
 *  Copyright (c) 2022. Geniem Oy
 */

namespace TMS\Theme\Vesi\Formatters;

/**
 * Class GridFormatter
 *
 * @package TMS\Theme\Vesi\Formatters
 */
class GridFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Grid';

    /**
     * Formatter hooks.
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/grid/data',
            [ $this, 'format' ],
            20
        );

        add_filter(
            'tms/acf/layout/grid/data',
            [ $this, 'format' ],
            20
        );
    }

    /**
     * Format block data
     *
     * @param array $data ACF Block data.
     *
     * @return array
     */
    public static function format( array $data ) : array {
        if ( empty( $data['repeater'] ) ) {
            return $data;
        }

        foreach ( $data['repeater'] as $key => $item ) {
            $data['repeater'][ $key ]['button']  = 'is-primary';
            $data['repeater'][ $key ]['classes'] = str_replace(
                [
                    'has-colors-primary',
                    'has-colors-secondary',
                    'has-colors-accent',
                ],
                '',
                $item['classes']
            );
        }

        return $data;
    }
}
