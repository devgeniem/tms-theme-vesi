<?php
/**
 *  Copyright (c) 2022. Geniem Oy
 */

namespace TMS\Theme\Vesi\Formatters;

use TMS\Theme\Base\Interfaces\Formatter;

/**
 * Class NoticeBannerFormatter
 *
 * @package TMS\Theme\Vesi\Formatters
 */
class NoticeBannerFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'NoticeBanner';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/notice_banner/data',
            [ $this, 'format' ],
            20
        );

        add_filter(
            'tms/acf/block/notice_banner/data',
            [ $this, 'format' ],
            20
        );
    }

    /**
     * Format layout or block data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        if ( $data['is_alarm'] ) {
            $data['text_color']        = 'has-text-primary';
            $data['container_classes'] = 'has-background-highlight';
        }

        return $data;
    }
}
