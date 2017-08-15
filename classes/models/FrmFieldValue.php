<?php

/**
 * @since 2.03.11
 */
class FrmFieldValue {

	/**
	 * @since 2.03.11
	 *
	 * @var stdClass
	 */
	protected $field = null;

	/**
	 * @since 2.03.11
	 *
	 * @var stdClass
	 */
	protected $entry = null;

	/**
	 * @since 2.03.11
	 *
	 * @var string
	 */
	protected $source = '';

	/**
	 * @since 2.03.11
	 *
	 * @var mixed
	 */
	protected $saved_value = '';

	/**
	 * @since 2.03.11
	 *
	 * @var mixed
	 */
	protected $displayed_value = '';

	/**
	 * FrmFieldValue constructor.
	 *
	 * @param stdClass $field
	 * @param stdClass $entry
	 * @param array $atts
	 */
	public function __construct( $field, $entry, $atts = array() ) {
		if ( ! is_object( $field ) || ! is_object( $entry ) || ! isset( $entry->metas ) ) {
			return;
		}

		$this->field = $field;
		$this->entry = $entry;
		$this->init_source( $atts );
		$this->init_saved_value();
		$this->init_displayed_value();
	}

	/**
	 * Get any property
	 *
	 * @since 3.0
	 */
	public function __get( $key ) {
		$function_name = 'get_' . $key;
		if ( is_callable( $this, $function_name ) ) {
			$value = $this->{$function_name};
		} else if ( property_exists( $this, $key ) ) {
			$value = $this->{$key};
		}
		return $value;
	}

	/**
	 * Initialize the source property
	 *
	 * @since 2.03.11
	 *
	 * @param array $atts
	 */
	protected function init_source( $atts ) {
		if ( isset( $atts['source'] ) && is_string( $atts['source'] ) && $atts['source'] !== '' ) {
			$this->source = (string) $atts['source'];
		} else {
			$this->source = 'general';
		}
	}

	/**
	 * Initialize the saved_value property
	 *
	 * @since 2.03.11
	 */
	protected function init_saved_value() {
		if ( isset( $this->entry->metas[ $this->field->id ] ) ) {
			$this->saved_value = $this->entry->metas[ $this->field->id ];
		} else {
			$this->saved_value = '';
		}

		$this->clean_saved_value();
	}

	/**
	 * Initialize a field's displayed value
	 *
	 * @since 2.03.11
	 */
	protected function init_displayed_value() {
		$this->displayed_value = $this->saved_value;

		$this->generate_displayed_value_for_field_type();
		$this->filter_displayed_value();
	}

	/**
	 * Get the field property's label
	 *
	 * @since 2.03.11
	 */
	public function get_field_label() {
		return $this->field->name;
	}

	/**
	 * Get the field property's key
	 *
	 * @since 2.03.11
	 */
	public function get_field_key() {
		return $this->field->field_key;
	}

	/**
	 * Get the field property's type
	 *
	 * @since 2.03.11
	 */
	public function get_field_type() {
		return $this->field->type;
	}

	/**
	 * Get the saved_value property
	 *
	 * @since 2.03.11
	 */
	public function get_saved_value() {
		return $this->saved_value;
	}

	/**
	 * Get the displayed_value property
	 *
	 * @since 2.03.11
	 */
	public function get_displayed_value() {
		return $this->displayed_value;
	}

	protected function generate_displayed_value_for_field_type() {
		if ( $this->field->type == 'user_id' ) {
			$this->displayed_value = FrmFieldsHelper::get_user_id_display_value( $this->displayed_value );
		}
	}

	/**
	 * Filter the displayed_value property
	 *
	 * @since 2.03.11
	 */
	protected function filter_displayed_value() {

		if ( $this->source === 'entry_formatter' ) {
			// Deprecated frm_email_value hook
			$meta                  = array(
				'item_id'    => $this->entry->id,
				'field_id'   => $this->field->id,
				'meta_value' => $this->saved_value,
				'field_type' => $this->field->type
			);
			$this->displayed_value = apply_filters( 'frm_email_value', $this->displayed_value, (object) $meta, $this->entry, array(
				'field' => $this->field,
			) );
			if ( has_filter( 'frm_email_value' ) ) {
				_deprecated_function( 'The frm_email_value filter', '2.03.11', 'the frm_display_{fieldtype}_value_custom filter' );
			}
		}

		// frm_display_{fieldtype}_value_custom hook
		$this->displayed_value = apply_filters( 'frm_display_' . $this->field->type . '_value_custom', $this->displayed_value, array(
			'field' => $this->field,
		) );
	}

	/**
	 * Clean a field's saved value
	 *
	 * @since 2.03.11
	 */
	protected function clean_saved_value() {
		if ( $this->saved_value !== '' ) {

			$this->saved_value = maybe_unserialize( $this->saved_value );

			if ( is_array( $this->saved_value ) && empty( $this->saved_value ) ) {
				$this->saved_value = '';
			}
		}
	}
}