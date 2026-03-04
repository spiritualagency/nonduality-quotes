<?php
/**
 * Plugin Name:       Nonduality Spiritual Quotes Block
 * Description:       Displays curated nonduality and spiritual quotes from traditions including Advaita Vedanta, Buddhism, Taoism, Christian Mysticism, Sufism, Zen, and Inspirational. Supports Pixabay nature backgrounds.
 * Version:           0.1.0
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Author:            Vikas Pandey and The Spiritual Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nonduality-quotes
 *
 * @package NondualityQuotes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'nonduality_quotes_block_init' ) ) {
	function nonduality_quotes_block_init() {
		register_block_type( __DIR__ . '/build/' );
	}
}
add_action( 'init', 'nonduality_quotes_block_init' );

if ( ! function_exists( 'nonduality_quotes_register_settings' ) ) {
	function nonduality_quotes_register_settings() {
		register_setting( 'nonduality_quotes_settings', 'tnq_pixabay_api_key', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		) );
		register_setting( 'nonduality_quotes_settings', 'tnq_custom_quotes', array(
			'type'              => 'array',
			'sanitize_callback' => 'nonduality_quotes_sanitize_custom_quotes',
			'default'           => array(),
		) );
	}
}
add_action( 'admin_init', 'nonduality_quotes_register_settings' );

if ( ! function_exists( 'nonduality_quotes_sanitize_custom_quotes' ) ) {
	function nonduality_quotes_sanitize_custom_quotes( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$sanitized = array();
		foreach ( $value as $quote ) {
			if ( ! empty( $quote['text'] ) && ! empty( $quote['author'] ) && ! empty( $quote['category'] ) ) {
				$sanitized[] = array(
					'text'     => sanitize_textarea_field( $quote['text'] ),
					'author'   => sanitize_text_field( $quote['author'] ),
					'category' => sanitize_text_field( $quote['category'] ),
				);
			}
		}
		return $sanitized;
	}
}

if ( ! function_exists( 'nonduality_quotes_settings_page' ) ) {
	function nonduality_quotes_settings_page() {
		add_options_page(
			__( 'Nonduality Quotes Settings', 'nonduality-quotes' ),
			__( 'Nonduality Quotes', 'nonduality-quotes' ),
			'manage_options',
			'nonduality-quotes',
			'nonduality_quotes_settings_page_html'
		);
	}
}
add_action( 'admin_menu', 'nonduality_quotes_settings_page' );

if ( ! function_exists( 'nonduality_quotes_handle_actions' ) ) {
	function nonduality_quotes_handle_actions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle adding a single quote.
		if ( isset( $_POST['tnq_add_quote_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['tnq_add_quote_nonce'] ), 'tnq_add_quote' ) ) {
			$text     = isset( $_POST['tnq_new_quote_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tnq_new_quote_text'] ) ) : '';
			$author   = isset( $_POST['tnq_new_quote_author'] ) ? sanitize_text_field( wp_unslash( $_POST['tnq_new_quote_author'] ) ) : '';
			$category = isset( $_POST['tnq_new_quote_category'] ) ? sanitize_text_field( wp_unslash( $_POST['tnq_new_quote_category'] ) ) : '';

			if ( ! empty( $text ) && ! empty( $author ) && ! empty( $category ) ) {
				$custom_quotes   = get_option( 'tnq_custom_quotes', array() );
				$custom_quotes[] = array(
					'text'     => $text,
					'author'   => $author,
					'category' => $category,
				);
				update_option( 'tnq_custom_quotes', $custom_quotes );
				add_settings_error( 'tnq_messages', 'tnq_quote_added', __( 'Quote added successfully.', 'nonduality-quotes' ), 'success' );
			} else {
				add_settings_error( 'tnq_messages', 'tnq_quote_error', __( 'All fields are required to add a quote.', 'nonduality-quotes' ), 'error' );
			}
		}

		// Handle deleting a quote.
		if ( isset( $_GET['tnq_delete'] ) && isset( $_GET['_wpnonce'] ) ) {
			if ( wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'tnq_delete_quote' ) ) {
				$index         = absint( $_GET['tnq_delete'] );
				$custom_quotes = get_option( 'tnq_custom_quotes', array() );
				if ( isset( $custom_quotes[ $index ] ) ) {
					array_splice( $custom_quotes, $index, 1 );
					update_option( 'tnq_custom_quotes', $custom_quotes );
					add_settings_error( 'tnq_messages', 'tnq_quote_deleted', __( 'Quote deleted successfully.', 'nonduality-quotes' ), 'success' );
				}
				// Redirect to remove the query args.
				wp_safe_redirect( admin_url( 'options-general.php?page=nonduality-quotes&tab=manage' ) );
				exit;
			}
		}

		// Handle CSV upload.
		if ( isset( $_POST['tnq_csv_upload_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['tnq_csv_upload_nonce'] ), 'tnq_csv_upload' ) ) {
			if ( ! empty( $_FILES['tnq_csv_file']['tmp_name'] ) && UPLOAD_ERR_OK === $_FILES['tnq_csv_file']['error'] ) {
				$file   = $_FILES['tnq_csv_file']['tmp_name'];
				$handle = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
				if ( $handle ) {
					$custom_quotes = get_option( 'tnq_custom_quotes', array() );
					$count         = 0;
					$row_number    = 0;
					while ( ( $data = fgetcsv( $handle, 0, ',' ) ) !== false ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
						$row_number++;
						// Skip header row if it looks like one.
						if ( 1 === $row_number ) {
							$first_cell = strtolower( trim( $data[0] ) );
							if ( in_array( $first_cell, array( 'author', 'quote', 'text', 'category' ), true ) ) {
								continue;
							}
						}
						if ( count( $data ) >= 3 ) {
							$author   = sanitize_text_field( trim( $data[0] ) );
							$text     = sanitize_textarea_field( trim( $data[1] ) );
							$category = sanitize_text_field( trim( $data[2] ) );
							if ( ! empty( $text ) && ! empty( $author ) && ! empty( $category ) ) {
								$custom_quotes[] = array(
									'text'     => $text,
									'author'   => $author,
									'category' => $category,
								);
								$count++;
							}
						}
					}
					fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
					update_option( 'tnq_custom_quotes', $custom_quotes );
					/* translators: %d: number of quotes imported */
					add_settings_error( 'tnq_messages', 'tnq_csv_imported', sprintf( __( '%d quotes imported from CSV.', 'nonduality-quotes' ), $count ), 'success' );
				}
			} else {
				add_settings_error( 'tnq_messages', 'tnq_csv_error', __( 'Please select a valid CSV file to upload.', 'nonduality-quotes' ), 'error' );
			}
		}
	}
}
add_action( 'admin_init', 'nonduality_quotes_handle_actions' );

if ( ! function_exists( 'nonduality_quotes_settings_page_html' ) ) {
	function nonduality_quotes_settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'settings';
		$categories = array(
			'Advaita Vedanta',
			'Buddhism',
			'Taoism',
			'Christian Mysticism',
			'Sufism',
			'Zen',
			'Inspirational',
		);
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php settings_errors( 'tnq_messages' ); ?>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=nonduality-quotes&tab=settings' ) ); ?>" class="nav-tab <?php echo 'settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Pixabay API', 'nonduality-quotes' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=nonduality-quotes&tab=manage' ) ); ?>" class="nav-tab <?php echo 'manage' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Manage Quotes', 'nonduality-quotes' ); ?>
				</a>
			</h2>

			<?php if ( 'settings' === $active_tab ) : ?>
				<form action="options.php" method="post">
					<?php settings_fields( 'nonduality_quotes_settings' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="tnq_pixabay_api_key"><?php esc_html_e( 'Pixabay API Key', 'nonduality-quotes' ); ?></label>
							</th>
							<td>
								<input type="text" id="tnq_pixabay_api_key" name="tnq_pixabay_api_key" value="<?php echo esc_attr( get_option( 'tnq_pixabay_api_key', '' ) ); ?>" class="regular-text" />
								<p class="description">
									<?php esc_html_e( 'Enter your Pixabay API key to enable nature background images on quote cards. Get a free key at pixabay.com/api/docs/', 'nonduality-quotes' ); ?>
								</p>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
			<?php else : ?>

				<h3><?php esc_html_e( 'Add New Quote', 'nonduality-quotes' ); ?></h3>
				<form method="post" action="">
					<?php wp_nonce_field( 'tnq_add_quote', 'tnq_add_quote_nonce' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="tnq_new_quote_text"><?php esc_html_e( 'Quote', 'nonduality-quotes' ); ?></label>
							</th>
							<td>
								<textarea id="tnq_new_quote_text" name="tnq_new_quote_text" rows="3" class="large-text" required></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="tnq_new_quote_author"><?php esc_html_e( 'Author', 'nonduality-quotes' ); ?></label>
							</th>
							<td>
								<input type="text" id="tnq_new_quote_author" name="tnq_new_quote_author" class="regular-text" required />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="tnq_new_quote_category"><?php esc_html_e( 'Category', 'nonduality-quotes' ); ?></label>
							</th>
							<td>
								<select id="tnq_new_quote_category" name="tnq_new_quote_category" required>
									<option value=""><?php esc_html_e( '— Select —', 'nonduality-quotes' ); ?></option>
									<?php foreach ( $categories as $cat ) : ?>
										<option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Choose a tradition category for this quote.', 'nonduality-quotes' ); ?></p>
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Add Quote', 'nonduality-quotes' ), 'primary', 'tnq_add_quote_submit' ); ?>
				</form>

				<hr />

				<h3><?php esc_html_e( 'Import Quotes from CSV', 'nonduality-quotes' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'Upload a CSV file with three columns: Author, Quote, Category. The first row is skipped if it looks like a header.', 'nonduality-quotes' ); ?>
				</p>
				<form method="post" enctype="multipart/form-data" action="">
					<?php wp_nonce_field( 'tnq_csv_upload', 'tnq_csv_upload_nonce' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="tnq_csv_file"><?php esc_html_e( 'CSV File', 'nonduality-quotes' ); ?></label>
							</th>
							<td>
								<input type="file" id="tnq_csv_file" name="tnq_csv_file" accept=".csv" required />
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Upload CSV', 'nonduality-quotes' ), 'secondary', 'tnq_csv_upload_submit' ); ?>
				</form>

				<hr />

				<h3><?php esc_html_e( 'Custom Quotes', 'nonduality-quotes' ); ?></h3>
				<?php
				$custom_quotes = get_option( 'tnq_custom_quotes', array() );
				if ( empty( $custom_quotes ) ) :
				?>
					<p><?php esc_html_e( 'No custom quotes added yet. Use the form above to add quotes or import from a CSV file.', 'nonduality-quotes' ); ?></p>
				<?php else : ?>
					<table class="widefat striped" style="max-width: 900px;">
						<thead>
							<tr>
								<th style="width:40%"><?php esc_html_e( 'Quote', 'nonduality-quotes' ); ?></th>
								<th style="width:20%"><?php esc_html_e( 'Author', 'nonduality-quotes' ); ?></th>
								<th style="width:20%"><?php esc_html_e( 'Category', 'nonduality-quotes' ); ?></th>
								<th style="width:20%"><?php esc_html_e( 'Actions', 'nonduality-quotes' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $custom_quotes as $i => $q ) : ?>
								<tr>
									<td><?php echo esc_html( wp_trim_words( $q['text'], 20, '...' ) ); ?></td>
									<td><?php echo esc_html( $q['author'] ); ?></td>
									<td><?php echo esc_html( $q['category'] ); ?></td>
									<td>
										<a
											href="<?php echo esc_url( wp_nonce_url( admin_url( 'options-general.php?page=nonduality-quotes&tab=manage&tnq_delete=' . $i ), 'tnq_delete_quote' ) ); ?>"
											class="button button-small"
											onclick="return confirm('<?php echo esc_js( __( 'Delete this quote?', 'nonduality-quotes' ) ); ?>');"
											style="color:#b32d2e;"
										>
											<?php esc_html_e( 'Delete', 'nonduality-quotes' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<p class="description" style="margin-top:10px;">
						<?php
						/* translators: %d: number of custom quotes */
						printf( esc_html__( 'Total custom quotes: %d', 'nonduality-quotes' ), count( $custom_quotes ) );
						?>
					</p>
				<?php endif; ?>

			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nonduality_quotes_get_custom_quotes_json' ) ) {
	function nonduality_quotes_get_custom_quotes_json() {
		$custom_quotes = get_option( 'tnq_custom_quotes', array() );
		if ( empty( $custom_quotes ) ) {
			return '[]';
		}
		return wp_json_encode( array_values( $custom_quotes ) );
	}
}

if ( ! function_exists( 'nonduality_quotes_enqueue_custom_quotes' ) ) {
	function nonduality_quotes_enqueue_custom_quotes() {
		$custom_quotes = get_option( 'tnq_custom_quotes', array() );
		if ( ! empty( $custom_quotes ) ) {
			$handle = 'nonduality-quotes-spiritual-agency-nonduality-quotes-view-script';
			wp_add_inline_script(
				$handle,
				'var tnqCustomQuotes = ' . wp_json_encode( array_values( $custom_quotes ) ) . ';',
				'before'
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'nonduality_quotes_enqueue_custom_quotes', 20 );
