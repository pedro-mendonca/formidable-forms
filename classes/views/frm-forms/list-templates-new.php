<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<ul class="frm-featured-forms-new">
	<li class="frm-add-blank-form frm-selectable">
		<div class="frm-new-form-button frm-featured-form">
			<div style="background-color: #F4AD3D;">
				<?php FrmAppHelper::icon_by_class( 'frmfont frm_plus_icon' ); ?>
			</div><div>
				<h3><?php esc_html_e( 'Blank Form', 'formidable' ); ?></h3>
				<p><?php esc_html_e( 'Create a new form from scratch', 'formidable' ); ?></p>
			</div>
		</div>
	</li><?php
	$render_icon = true;
	foreach ( array( 20872734, 20874748, 20882522, 20874739 ) as $template ) {
		if ( ! isset( $templates[ $template ] ) ) {
			continue;
		}

		$template              = $templates[ $template ];
		$plan_required         = FrmFormsHelper::get_plan_required( $template );
		$args['plan_required'] = $plan_required;
		$link                  = FrmFormsHelper::get_template_install_link( $template, $args );
		require dirname( __FILE__ ) . '/list-template.php';
	}
	?><li class="frm-selectable" data-href="<?php echo esc_url( admin_url( 'admin.php?page=formidable-import' ) ); ?>">
		<div class="frm-featured-form">
			<div style="background-color: #805EF6;">
				<?php FrmAppHelper::icon_by_class( 'frmfont frm_upload_icon' ); ?>
			</div><div>
				<h3><?php esc_html_e( 'Import', 'formidable' ); ?></h3>
				<p><?php esc_html_e( 'Upload your Formidable XML or CSV file to import forms.', 'formidable' ); ?></p>
			</div>
		</div>
	</li>
</ul>
<?php
FrmAppHelper::show_search_box(
	array(
		'input_id'    => 'template',
		'placeholder' => __( 'Search Templates', 'formidable' ),
		'tosearch'    => 'frm-searchable-template',
	)
);
?>
<div class="accordion-container">
	<ul class="frm-featured-forms-new categories-list">
		<?php foreach ( $categories as $category ) { ?>
			<?php
			$category_templates = $templates_by_category[ $category ];
			$count              = count( $category_templates );
			$available          = FrmFormsHelper::available_count( $category_templates, $args );
			?>
			<li class="control-section accordion-section">
				<div class="frm-featured-form">
					<div style="background-color: #805EF6;">
						<?php FrmFormsHelper::template_icon( array( $category ) ); ?>
					</div><div>
						<div class="accordion-section-title">
							<h3><?php echo esc_attr( $category ); ?></h3>
							<p><span class="frm-template-count"><?php echo esc_html( $count ); ?></span> <span class="templates-plural <?php echo $count === 1 ? 'frm_hidden' : ''; ?>"><?php esc_html_e( 'templates', 'formidable' ); ?></span><span class="templates-singular <?php echo $count !== 1 ? 'frm_hidden' : ''; ?>"><?php esc_html_e( 'template', 'formidable' ); ?></span><?php echo $available ? '&nbsp;&nbsp;|&nbsp;&nbsp;<span class="frm-available-templates-count">' . esc_html( $available ) . '</span> ' . esc_html__( 'available', 'formidable' ) : ''; ?></p>
						</div>
						<div class="accordion-section-content" aria-expanded="false">
							<ul>
							<?php
							$searchable  = true;
							$render_icon = false;
							foreach ( $category_templates as $category_template ) {
								$template              = $category_template;
								$plan_required         = FrmFormsHelper::get_plan_required( $template );
								$args['plan_required'] = $plan_required;
								$link                  = FrmFormsHelper::get_template_install_link( $template, $args );
								require dirname( __FILE__ ) . '/list-template.php';
							}
							?>
							</ul>
						</div>
					</div>
				</div>
			</li>
		<?php } ?>
	</ul>
</div>
