<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<span class="frm-style-component frm-field-shape frm-radio-component">
	<div class="frm-radio-container frm-flex-box frm-flex-justify">
		<input id="frm-field-shape-regular" <?php checked( $field_value, 'regular' ); ?> type="radio" <?php echo esc_attr( $field_name ); ?> value="regular" />
		<label for="frm-field-shape-regular">
			<?php FrmAppHelper::icon_by_class( 'frm_icon_font frm-square' ); ?>
		</label>

		<input data-frm-show-element="field-shape-corner-radius" id="frm-field-shape-rounded-corners" <?php checked( $field_value, 'rounded-corner' ); ?> type="radio" <?php echo esc_attr( $field_name ); ?> value="rounded-corner" />
		<label for="frm-field-shape-rounded-corners">
			<?php FrmAppHelper::icon_by_class( 'frm_icon_font frm-rounded-square' ); ?>
		</label>

		<input id="frm-field-shape-circle" <?php checked( $field_value, 'circle' ); ?> type="radio" <?php echo esc_attr( $field_name ); ?> value="circle" />
		<label for="frm-field-shape-circle">
			<?php FrmAppHelper::icon_by_class( 'frm_icon_font frm-circle' ); ?>
		</label>

		<input id="frm-field-shape-underline" <?php checked( $field_value, 'underline' ); ?> type="radio" <?php echo esc_attr( $field_name ); ?> value="underline" />
		<label for="frm-field-shape-underline">
			<?php FrmAppHelper::icon_by_class( 'frm_icon_font frm-underline' ); ?>
		</label>

		<span class="frm-radio-active-tracker"></span>
	</div>
</span>

