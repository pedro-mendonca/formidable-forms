<?php

/**
 * @since 3.0
 */
class FrmFieldHTML extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'html';

	/**
	 * @var bool
	 * @since 3.0
	 */
	protected $has_input = false;

	public function default_html() {
		return '<div id="frm_field_[id]_container" class="frm_form_field form-field">[description]</div>';
	}
}
