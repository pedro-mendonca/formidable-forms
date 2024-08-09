<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
class FrmFieldShapeStyleComponent extends FrmStyleComponent {

	/**
	 * The view file name.
	 *
	 * @since x.x
	 *
	 * @var string
	 */
	public $view_name = 'field-shape';

	/**
	 * Construct the FrmAlignStyleComponent.
	 *
	 * @since x.x
	 */
	public function __construct( $field_name, $field_value, $data ) {

		$this->data        = $data;
		$this->field_name  = $field_name;
		$this->field_value = $field_value;

		parent::get_instance();
		
		$this->load_view( $this->data );
	}

	/**
	 * Get the wrapper classname.
	 *
	 * @since x.x
	 *
	 * @return string
	 */
	protected function get_wrapper_class_name() {
		$class  = $this->get_default_wrapper_class_name();
		$class .= ' frm-align-component frm-radio-component';

		if ( empty( $this->data['options'] ) || 3 === count( $this->data['options'] ) ) {
			return $class;
		}

		return $class . ' frm-2-options';
	}
}
