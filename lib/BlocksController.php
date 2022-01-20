<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Vesi;

use TMS\Theme\Base\Interfaces\Controller;

/**
 * Class BlocksController
 *
 * This class handles the registration of Gutenberg blocks
 * that have been created with ACF Codifier.
 *
 * @package TMS\Theme\Vesi
 */
class BlocksController implements Controller {

    /**
     * Holds the block names for all ACF blocks.
     *
     * @var array
     */
    public array $registered_blocks = [];

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_action(
            'acf/init',
            \Closure::fromCallable( [ $this, 'require_block_files' ] )
        );
    }

    /**
     * This method loops through all files in the
     * Blocks directory and requires them.
     */
    private function require_block_files() : void {
        $files         = scandir( __DIR__ . '/Blocks' );
        $cleaned_files = array_diff( $files, [ '.', '..', 'BaseBlock.php' ] );

        array_walk( $cleaned_files, function ( $block ) {
            $block_class_name = str_replace( '.php', '', $block );

            if ( $block_class_name !== $block ) {
                $class_name = __NAMESPACE__ . "\\Blocks\\{$block_class_name}";

                if ( class_exists( $class_name ) ) {
                    ( new $class_name() );
                }
            }
        } );
    }
}
