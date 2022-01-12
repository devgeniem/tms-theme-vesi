<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vesi\Formatters;

/**
 * Class HeroFormatter
 *
 * @package TMS\Theme\Vesi\Formatters
 */
class HeroFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Hero';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/hero/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    public function format( array $layout ) : array {
        $layout['align']           = $layout['align'] ?? 'right';
        $layout['container_class'] = 'hero--' . $layout['align'];
        $layout['column_class']    = 'is-5-desktop';

        if ( $layout['align'] === 'right' ) {
            $layout['column_class'] .= ' is-offset-7-desktop';
        }

        $layout['has_filled_fields'] = $this->has_filled_text_fields( $layout );

        return $layout;
    }

    /**
     * Has filled text fields
     *
     * @param array $layout ACF Layout data.
     *
     * @return bool
     */
    protected function has_filled_text_fields( array $layout ) : bool {
        $fields = [
            'title',
            'description',
            'link',
        ];

        foreach ( $fields as $field_key ) {
            if ( ! empty( $layout[ $field_key ] ) ) {
                return true;
            }
        }

        return false;
    }
}
