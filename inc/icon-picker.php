<?php
/**
 * Custom ACF field type: RGeometry Icon Picker.
 *
 * A visual replacement for the plain select used on Services, Process, and
 * Footer social fields. Click a button, a modal shows the full icon set as
 * thumbnails with search, click one to select. Values are stored as the icon
 * key (e.g. "home", "pencil") so the existing rgeometry_icon() helper still
 * works unchanged.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/include_field_types', 'rgeometry_register_icon_picker_field' );
function rgeometry_register_icon_picker_field() {
	if ( ! class_exists( 'acf_field' ) ) {
		return;
	}
	acf_register_field_type( 'RGeometry_Icon_Picker_Field' );
}

class RGeometry_Icon_Picker_Field extends acf_field {

	public function initialize() {
		$this->name     = 'rg_icon_picker';
		$this->label    = __( 'Icon Picker (RGeometry)', 'rgeometry' );
		$this->category = 'choice';
		$this->defaults = array(
			'return_format' => 'value', // 'value' = key, 'svg' = inline SVG HTML
		);
	}

	/**
	 * Field type settings shown in the Field Group editor.
	 */
	public function render_field_settings( $field ) {
		acf_render_field_setting( $field, array(
			'label'        => __( 'Return Value', 'rgeometry' ),
			'instructions' => __( 'Choose whether get_field() returns the icon key (use with rgeometry_icon()) or the inline SVG HTML directly.', 'rgeometry' ),
			'name'         => 'return_format',
			'type'         => 'radio',
			'choices'      => array(
				'value' => __( 'Key (e.g. "home") — use with rgeometry_icon()', 'rgeometry' ),
				'svg'   => __( 'Inline SVG HTML', 'rgeometry' ),
			),
			'layout' => 'horizontal',
		) );
	}

	/**
	 * The editor UI shown in the admin when this field is rendered.
	 * Hidden input carries the value, button shows the preview.
	 * The modal/grid lives in a template that's cloned on open via JS.
	 */
	public function render_field( $field ) {
		$value = isset( $field['value'] ) ? $field['value'] : '';
		$svg_preview = $value ? rgeometry_icon( $value ) : '';
		$label       = '';
		$meta        = rgeometry_icon_metadata();
		if ( $value && isset( $meta[ $value ] ) ) {
			$label = $meta[ $value ]['label'];
		}
		?>
		<div class="rg-iconpicker" data-rg-iconpicker>
			<input type="hidden"
				name="<?php echo esc_attr( $field['name'] ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				data-rg-iconpicker-input />

			<button type="button" class="button rg-iconpicker__btn" data-rg-iconpicker-open>
				<span class="rg-iconpicker__preview" data-rg-iconpicker-preview>
					<?php echo $svg_preview ? $svg_preview : '<span class="rg-iconpicker__placeholder" aria-hidden="true">?</span>'; ?>
				</span>
				<span class="rg-iconpicker__label" data-rg-iconpicker-label>
					<?php echo $value ? esc_html( $label ? $label : $value ) : esc_html__( 'Choose icon…', 'rgeometry' ); ?>
				</span>
				<?php if ( $value ) : ?>
					<button type="button" class="rg-iconpicker__clear" data-rg-iconpicker-clear aria-label="<?php esc_attr_e( 'Clear icon', 'rgeometry' ); ?>">×</button>
				<?php endif; ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Enqueue admin JS + CSS, localize the icon library so the picker can
	 * render without an AJAX round-trip.
	 */
	public function input_admin_enqueue_scripts() {
		$ver = defined( 'RGEOMETRY_ASSET_VER' ) ? RGEOMETRY_ASSET_VER : '1';

		wp_enqueue_style(
			'rg-iconpicker',
			RGEOMETRY_URI . '/assets/css/icon-picker.css',
			array(),
			$ver
		);

		wp_enqueue_script(
			'rg-iconpicker',
			RGEOMETRY_URI . '/assets/js/icon-picker.js',
			array( 'jquery' ),
			$ver,
			true
		);

		// Build a compact data set the JS can consume. Categories group the grid.
		$meta = rgeometry_icon_metadata();
		$registry = rgeometry_icon_registry();
		$payload = array();
		foreach ( $meta as $key => $info ) {
			if ( empty( $registry[ $key ] ) ) {
				continue;
			}
			$payload[] = array(
				'key'      => $key,
				'label'    => $info['label'],
				'category' => $info['category'],
				'keywords' => $info['keywords'],
				'svg'      => $registry[ $key ],
			);
		}

		wp_localize_script( 'rg-iconpicker', 'rgIconPickerData', array(
			'icons'  => $payload,
			'labels' => array(
				'title'       => __( 'Choose an icon', 'rgeometry' ),
				'searchLabel' => __( 'Search icons', 'rgeometry' ),
				'searchPlace' => __( 'Search by name or keyword…', 'rgeometry' ),
				'clear'       => __( 'Clear', 'rgeometry' ),
				'close'       => __( 'Close', 'rgeometry' ),
				'empty'       => __( 'No icons match that search.', 'rgeometry' ),
			),
		) );
	}

	/**
	 * Transform the stored value when get_field() is called. If the field's
	 * return_format is "svg", return the inline SVG HTML; otherwise return
	 * the raw key.
	 */
	public function format_value( $value, $post_id, $field ) {
		if ( empty( $value ) ) {
			return '';
		}
		$format = isset( $field['return_format'] ) ? $field['return_format'] : 'value';
		if ( $format === 'svg' ) {
			return rgeometry_icon( $value );
		}
		return $value;
	}
}
