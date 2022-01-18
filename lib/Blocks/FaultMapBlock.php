<?php
/**
 * Copyright (c) 2022. Geniem Oy
 */

namespace TMS\Theme\Vesi\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\Blocks\BaseBlock;
use TMS\Theme\Vesi\ACF\Fields\FaultMapFields;

/**
 * Class FaultMapBlock
 *
 * @package TMS\Theme\Vesi\Blocks
 */
class FaultMapBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'fault-map';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'fault_map';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'sos';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Häiriökartta';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new FaultMapFields( $this->title, self::NAME );

        return apply_filters(
            'tms/block/' . self::KEY . '/fields',
            $group->get_fields(),
            self::KEY
        );
    }

    /**
     * This filters the block ACF data.
     *
     * @param array  $data       Block's ACF data.
     * @param Block  $instance   The block instance.
     * @param array  $block      The original ACF block array.
     * @param string $content    The HTML content.
     * @param bool   $is_preview A flag that shows if we're in preview.
     * @param int    $post_id    The parent post's ID.
     *
     * @return array The block data.
     */
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        return apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );
    }
}
