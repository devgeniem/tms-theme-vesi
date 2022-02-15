<?php

namespace TMS\Theme\Vesi;

use TMS\Theme\Base\Interfaces\Controller;

/**
 * Class GravityForms
 *
 * @package TMS\Theme\Vesi
 */
class GravityForms implements Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'gform_submit_button',
            \Closure::fromCallable( [ $this, 'form_submit_button' ] ),
            20,
            2
        );
    }

    /**
     * Change submit input to button.
     *
     * @param string $button Submit's HTML.
     * @param array  $form   Form data in array.
     *
     * @return string
     */
    protected function form_submit_button( $button, $form ) {
        return "<button type='submit' class='button is-secondary gform_button' id='gform_submit_button_{$form['id']}'><span>{$form['button']['text']}</span> <svg class='icon icon--arrow-right icon--medium' aria-hidden='true'> <use xlink:href='#icon-arrow-right'></use></svg></button>"; // phpcs:ignore
    }

}
