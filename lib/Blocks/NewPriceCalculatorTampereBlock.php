<?php

namespace TMS\Theme\Vesi\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\Blocks\BaseBlock;
use TMS\Theme\Vesi\ACF\Fields\PriceCalculatorTampereFields;

/**
 * Class NewPriceCalculatorBlock
 *
 * @package TMS\Theme\Vesi\Blocks
 */
class NewPriceCalculatorTampereBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'new-price-calculator-tampere';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'new-price-calculator-tampere';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'forms';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Liittymislaskuri Tampere (alv 25.5%)';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new PriceCalculatorTampereFields( $this->title, self::NAME );

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
