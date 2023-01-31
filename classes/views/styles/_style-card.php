<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
// This view is used on the style page to render a single style card.
// It is used for both custom style cards and card templates.
// This includes a basic preview (text field and submit button only).
// It also includes the title of the style and possibly some basic tags if "selected" or "default".

$is_template  = 0 === $style->ID;
$include_info = $is_active_style;
?>
<div <?php FrmAppHelper::array_to_html_params( $params, true ); ?>>
	<div>
		<div class="frm-style-card-title-wrapper">
			<?php
			if ( $is_active_style ) {
				FrmAppHelper::icon_by_class( 'frmfont frm_checkmark_circle_icon' );
			}
			if ( ! empty( $is_locked ) ) {
				FrmAppHelper::icon_by_class( 'frmfont frm_lock_simple' );
			}
			?>
			<?php // The rename option uses the text content of .frm-style-card-title so don't leave any additional whitespace here. ?>
			<span class="frm-style-card-title"><?php echo esc_html( $style->post_title ); ?></span>
			<?php if ( $include_info ) { ?>
				<span class="frm-style-card-info">
					<?php
					$info_text = __( 'Applied', 'formidable' );
					echo '(' . esc_html( $info_text ) . ')';
					?>
				</span>
			<?php } ?>
		</div>
		<div class="frm-style-card-preview">
			<?php
			$colors = array(
				$style->post_content['label_color'],
				$style->post_content['text_color'],
				$style->post_content['submit_bg_color'],
			);
			foreach( $colors as $index => $color ) {
				$brightness = FrmStylesHelper::get_color_brightness( $color );

				if ( 0 !== strpos( $color, 'rgb' ) ) {
					$color = '#' . $color;
				}

				$circle_params = array(
					'class' => 'frm-style-circle' . absint( $index + 1 ),
					'style' => 'background-color: ' . $color,
				);

				if ( 255 === $brightness ) {
					$circle_params['class'] .= ' frm-darker-circle-border';
				}
			?>
				<div <?php FrmAppHelper::array_to_html_params( $circle_params, true ); ?>></div>
				<?php
			}
			?>
			<div class="frm-style-card-separator"></div>
			<div class="frm-button-style-example">
				<div></div>
			</div>
			<div class="frm-input-style-example">
				<div></div>
			</div>
		</div>
	</div>
</div>
