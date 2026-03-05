<?php
/**
 * Server-side rendering for the Nonduality Spiritual Quotes block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category        = isset( $attributes['category'] ) ? $attributes['category'] : 'all';
$font_size       = isset( $attributes['fontSize'] ) ? $attributes['fontSize'] : 'medium';
$text_align      = isset( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'center';
$show_category   = isset( $attributes['showCategory'] ) ? $attributes['showCategory'] : true;
$show_button     = isset( $attributes['showButton'] ) ? $attributes['showButton'] : true;
$color_scheme    = isset( $attributes['colorScheme'] ) ? $attributes['colorScheme'] : 'light';
$show_background = isset( $attributes['showBackground'] ) ? $attributes['showBackground'] : true;

$api_key = get_option( 'tnq_pixabay_api_key', '' );

$extra_classes = sprintf(
	'tnq-scheme-%s tnq-size-%s tnq-align-%s',
	esc_attr( $color_scheme ),
	esc_attr( $font_size ),
	esc_attr( $text_align )
);

$wrapper_attributes = get_block_wrapper_attributes( array(
	'class'               => $extra_classes,
	'data-category'       => esc_attr( $category ),
	'data-show-category'  => $show_category ? 'true' : 'false',
	'data-show-button'    => $show_button ? 'true' : 'false',
	'data-show-background' => $show_background ? 'true' : 'false',
	'data-pixabay-key'    => ( $show_background && ! empty( $api_key ) ) ? esc_attr( $api_key ) : '',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="tnq-quote-card">
		<div class="tnq-bg-image" aria-hidden="true"></div>
		<div class="tnq-bg-overlay" aria-hidden="true"></div>
		<div class="tnq-quote-content">
			<div class="tnq-quote-icon" aria-hidden="true">&ldquo;</div>
			<blockquote class="tnq-quote-text"></blockquote>
			<div class="tnq-quote-meta">
				<span class="tnq-quote-author"></span>
				<span class="tnq-quote-category"></span>
			</div>
			<div class="tnq-quote-actions">
				<button type="button" class="tnq-new-quote-btn"<?php echo $show_button ? '' : ' style="display:none"'; ?>>New Quote</button>
				<div class="tnq-share-wrapper">
					<button type="button" class="tnq-new-quote-btn tnq-share-btn">Share</button>
					<div class="tnq-share-dropdown" style="display:none">
						<button type="button" class="tnq-share-option" data-platform="facebook">
							<span class="tnq-share-icon" aria-hidden="true">&#xf09a;</span>
							Facebook
						</button>
						<button type="button" class="tnq-share-option" data-platform="x">
							<span class="tnq-share-icon tnq-icon-text" aria-hidden="true">&#x1d54f;</span>
							X
						</button>
						<button type="button" class="tnq-share-option" data-platform="whatsapp">
							<span class="tnq-share-icon" aria-hidden="true">&#xf232;</span>
							WhatsApp
						</button>
						<button type="button" class="tnq-share-option" data-platform="linkedin">
							<span class="tnq-share-icon" aria-hidden="true">&#xf0e1;</span>
							LinkedIn
						</button>
						<button type="button" class="tnq-share-option" data-platform="instagram">
							<span class="tnq-share-icon" aria-hidden="true">&#xf16d;</span>
							Instagram
						</button>
						<button type="button" class="tnq-share-option" data-platform="link">
							<span class="tnq-share-icon tnq-icon-text" aria-hidden="true">&#128279;</span>
							Copy Link
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tnq-toast" style="display:none"></div>
</div>
