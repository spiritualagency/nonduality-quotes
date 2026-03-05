<?php
/**
 * Plugin Name:       Nonduality Spiritual Quotes Block
 * Description:       Displays curated nonduality and spiritual quotes from traditions including Advaita Vedanta, Buddhism, Taoism, Christian Mysticism, Sufism, Zen, and Inspirational. Supports Pixabay nature backgrounds.
 * Version:           0.1.0
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Author:            Vikas Pandey and The Spiritual Agency®
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       telex-nonduality-quotes
 *
 * @package NondualityQuotes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'telex_nonduality_quotes_block_init' ) ) {
	function telex_nonduality_quotes_block_init() {
		register_block_type( __DIR__ . '/build/' );
	}
}
add_action( 'init', 'telex_nonduality_quotes_block_init' );

if ( ! function_exists( 'telex_nonduality_quotes_register_settings' ) ) {
	function telex_nonduality_quotes_register_settings() {
		register_setting( 'telex_nonduality_quotes_settings', 'tnq_pixabay_api_key', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		) );
		register_setting( 'telex_nonduality_quotes_settings', 'tnq_custom_quotes', array(
			'type'              => 'array',
			'sanitize_callback' => 'telex_nonduality_quotes_sanitize_custom_quotes',
			'default'           => array(),
		) );
	}
}
add_action( 'admin_init', 'telex_nonduality_quotes_register_settings' );

if ( ! function_exists( 'telex_nonduality_quotes_sanitize_custom_quotes' ) ) {
	function telex_nonduality_quotes_sanitize_custom_quotes( $value ) {
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

if ( ! function_exists( 'telex_nonduality_quotes_settings_page' ) ) {
	function telex_nonduality_quotes_settings_page() {
		add_options_page(
			__( 'Nonduality Quotes Settings', 'telex-nonduality-quotes' ),
			__( 'Nonduality Quotes', 'telex-nonduality-quotes' ),
			'manage_options',
			'telex-nonduality-quotes',
			'telex_nonduality_quotes_settings_page_html'
		);
	}
}
add_action( 'admin_menu', 'telex_nonduality_quotes_settings_page' );

if ( ! function_exists( 'telex_nonduality_quotes_handle_actions' ) ) {
	function telex_nonduality_quotes_handle_actions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle adding a single quote.
		if ( isset( $_POST['tnq_add_quote_nonce'] ) && wp_verify_nonce( $_POST['tnq_add_quote_nonce'], 'tnq_add_quote' ) ) {
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
				add_settings_error( 'tnq_messages', 'tnq_quote_added', __( 'Quote added successfully.', 'telex-nonduality-quotes' ), 'success' );
			} else {
				add_settings_error( 'tnq_messages', 'tnq_quote_error', __( 'All fields are required to add a quote.', 'telex-nonduality-quotes' ), 'error' );
			}
		}

		// Handle deleting a quote.
		if ( isset( $_GET['tnq_delete'] ) && isset( $_GET['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'tnq_delete_quote' ) ) {
				$index         = absint( $_GET['tnq_delete'] );
				$custom_quotes = get_option( 'tnq_custom_quotes', array() );
				if ( isset( $custom_quotes[ $index ] ) ) {
					array_splice( $custom_quotes, $index, 1 );
					update_option( 'tnq_custom_quotes', $custom_quotes );
					add_settings_error( 'tnq_messages', 'tnq_quote_deleted', __( 'Quote deleted successfully.', 'telex-nonduality-quotes' ), 'success' );
				}
				// Redirect to remove the query args.
				wp_safe_redirect( admin_url( 'options-general.php?page=telex-nonduality-quotes&tab=manage' ) );
				exit;
			}
		}

		// Handle CSV upload.
		if ( isset( $_POST['tnq_csv_upload_nonce'] ) && wp_verify_nonce( $_POST['tnq_csv_upload_nonce'], 'tnq_csv_upload' ) ) {
			if ( ! empty( $_FILES['tnq_csv_file']['tmp_name'] ) && $_FILES['tnq_csv_file']['error'] === UPLOAD_ERR_OK ) {
				$file   = $_FILES['tnq_csv_file']['tmp_name'];
				$handle = fopen( $file, 'r' );
				if ( $handle ) {
					$custom_quotes = get_option( 'tnq_custom_quotes', array() );
					$count         = 0;
					$row_number    = 0;
					while ( ( $data = fgetcsv( $handle, 0, ',' ) ) !== false ) {
						$row_number++;
						// Skip header row if it looks like one.
						if ( $row_number === 1 ) {
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
					fclose( $handle );
					update_option( 'tnq_custom_quotes', $custom_quotes );
					/* translators: %d: number of quotes imported */
					add_settings_error( 'tnq_messages', 'tnq_csv_imported', sprintf( __( '%d quotes imported from CSV.', 'telex-nonduality-quotes' ), $count ), 'success' );
				}
			} else {
				add_settings_error( 'tnq_messages', 'tnq_csv_error', __( 'Please select a valid CSV file to upload.', 'telex-nonduality-quotes' ), 'error' );
			}
		}
	}
}
add_action( 'admin_init', 'telex_nonduality_quotes_handle_actions' );

if ( ! function_exists( 'telex_nonduality_quotes_settings_page_html' ) ) {
	function telex_nonduality_quotes_settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'settings';
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
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-nonduality-quotes&tab=settings' ) ); ?>" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Pixabay API', 'telex-nonduality-quotes' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-nonduality-quotes&tab=manage' ) ); ?>" class="nav-tab <?php echo $active_tab === 'manage' ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Manage Quotes', 'telex-nonduality-quotes' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-nonduality-quotes&tab=embed' ) ); ?>" class="nav-tab <?php echo $active_tab === 'embed' ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Embed / Daily Quote', 'telex-nonduality-quotes' ); ?>
				</a>
			</h2>

			<?php if ( $active_tab === 'embed' ) : ?>
				<?php
				$rest_url   = esc_url( rest_url( 'tnq/v1/daily-quote' ) );
				$iframe_src = "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body><div id='tnq-dq' style='text-align:center'></div><script>(function(){var x=new XMLHttpRequest();x.open('GET','" . esc_js( $rest_url ) . "',true);x.onreadystatechange=function(){if(x.readyState===4&amp;&amp;x.status===200){var d=JSON.parse(x.responseText);var el=document.getElementById('tnq-dq');el.innerHTML='<blockquote style=\"margin:0 auto;text-align:center\">'+d.text+'</blockquote><cite style=\"display:block;text-align:center;margin-top:0.5em\">&mdash; '+d.author+'</cite>';}};x.send();})()</script></body></html>";
				$embed_code = '<iframe srcdoc="' . esc_attr( $iframe_src ) . '" style="border:none;width:100%;min-height:120px;" title="Daily Spiritual Quote"></iframe>';
				?>
				<h3><?php esc_html_e( 'Embed Daily Quote on External Sites', 'telex-nonduality-quotes' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'Copy the iframe code below and paste it into any website. It will display today\'s daily quote (text and author only) and automatically inherit the recipient site\'s CSS — no extra styles are imposed.', 'telex-nonduality-quotes' ); ?>
				</p>
				<p class="description" style="margin-top:6px;">
					<?php esc_html_e( 'The quote changes once per day automatically.', 'telex-nonduality-quotes' ); ?>
				</p>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'REST Endpoint', 'telex-nonduality-quotes' ); ?></th>
						<td>
							<code style="display:inline-block;padding:6px 12px;background:#f0f0f0;border-radius:4px;user-select:all;"><?php echo esc_html( $rest_url ); ?></code>
							<p class="description"><?php esc_html_e( 'Returns JSON with "text" and "author" fields. Publicly accessible, no authentication required.', 'telex-nonduality-quotes' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="tnq-embed-code"><?php esc_html_e( 'Embed Code', 'telex-nonduality-quotes' ); ?></label>
						</th>
						<td>
							<textarea id="tnq-embed-code" rows="6" class="large-text code" readonly onclick="this.select()"><?php echo esc_textarea( $embed_code ); ?></textarea>
							<p style="margin-top:8px;">
								<button type="button" class="button button-secondary" onclick="var t=document.getElementById('tnq-embed-code');t.select();document.execCommand('copy');this.textContent='Copied!';var b=this;setTimeout(function(){b.textContent='<?php echo esc_js( __( 'Copy Embed Code', 'telex-nonduality-quotes' ) ); ?>';},2000);">
									<?php esc_html_e( 'Copy Embed Code', 'telex-nonduality-quotes' ); ?>
								</button>
							</p>
							<p class="description"><?php esc_html_e( 'This iframe uses srcdoc and fetches the daily quote from your site\'s REST API. It renders plain HTML (a blockquote and cite element) with no imposed styles, so it inherits the CSS of whatever page it\'s embedded on.', 'telex-nonduality-quotes' ); ?></p>
						</td>
					</tr>
				</table>

				<hr />
				<h3><?php esc_html_e( 'Preview', 'telex-nonduality-quotes' ); ?></h3>
				<p class="description"><?php esc_html_e( 'This is how the embedded daily quote looks with default WordPress admin styles:', 'telex-nonduality-quotes' ); ?></p>
				<div style="max-width:600px;margin:16px 0;padding:16px;border:1px solid #ddd;border-radius:8px;background:#fafafa;">
					<?php echo $embed_code; ?>
				</div>

			<?php elseif ( $active_tab === 'settings' ) : ?>
				<form action="options.php" method="post">
					<?php settings_fields( 'telex_nonduality_quotes_settings' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="tnq_pixabay_api_key"><?php esc_html_e( 'Pixabay API Key', 'telex-nonduality-quotes' ); ?></label>
							</th>
							<td>
								<input type="text" id="tnq_pixabay_api_key" name="tnq_pixabay_api_key" value="<?php echo esc_attr( get_option( 'tnq_pixabay_api_key', '' ) ); ?>" class="regular-text" />
								<p class="description">
									<?php esc_html_e( 'Enter your Pixabay API key to enable nature background images on quote cards. Get a free key at pixabay.com/api/docs/', 'telex-nonduality-quotes' ); ?>
								</p>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
			<?php else : ?>

				<h3><?php esc_html_e( 'Add New Quote', 'telex-nonduality-quotes' ); ?></h3>
				<form method="post" action="">
					<?php wp_nonce_field( 'tnq_add_quote', 'tnq_add_quote_nonce' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="tnq_new_quote_text"><?php esc_html_e( 'Quote', 'telex-nonduality-quotes' ); ?></label>
							</th>
							<td>
								<textarea id="tnq_new_quote_text" name="tnq_new_quote_text" rows="3" class="large-text" required></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="tnq_new_quote_author"><?php esc_html_e( 'Author', 'telex-nonduality-quotes' ); ?></label>
							</th>
							<td>
								<input type="text" id="tnq_new_quote_author" name="tnq_new_quote_author" class="regular-text" required />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="tnq_new_quote_category"><?php esc_html_e( 'Category', 'telex-nonduality-quotes' ); ?></label>
							</th>
							<td>
								<select id="tnq_new_quote_category" name="tnq_new_quote_category" required>
									<option value=""><?php esc_html_e( '— Select —', 'telex-nonduality-quotes' ); ?></option>
									<?php foreach ( $categories as $cat ) : ?>
										<option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Choose a tradition category for this quote.', 'telex-nonduality-quotes' ); ?></p>
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Add Quote', 'telex-nonduality-quotes' ), 'primary', 'tnq_add_quote_submit' ); ?>
				</form>

				<hr />

				<h3><?php esc_html_e( 'Import Quotes from CSV', 'telex-nonduality-quotes' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'Upload a CSV file with three columns: Author, Quote, Category. The first row is skipped if it looks like a header.', 'telex-nonduality-quotes' ); ?>
				</p>
				<form method="post" enctype="multipart/form-data" action="">
					<?php wp_nonce_field( 'tnq_csv_upload', 'tnq_csv_upload_nonce' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="tnq_csv_file"><?php esc_html_e( 'CSV File', 'telex-nonduality-quotes' ); ?></label>
							</th>
							<td>
								<input type="file" id="tnq_csv_file" name="tnq_csv_file" accept=".csv" required />
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Upload CSV', 'telex-nonduality-quotes' ), 'secondary', 'tnq_csv_upload_submit' ); ?>
				</form>

				<hr />

				<h3><?php esc_html_e( 'Custom Quotes', 'telex-nonduality-quotes' ); ?></h3>
				<?php
				$custom_quotes = get_option( 'tnq_custom_quotes', array() );
				if ( empty( $custom_quotes ) ) :
				?>
					<p><?php esc_html_e( 'No custom quotes added yet. Use the form above to add quotes or import from a CSV file.', 'telex-nonduality-quotes' ); ?></p>
				<?php else : ?>
					<table class="widefat striped" style="max-width: 900px;">
						<thead>
							<tr>
								<th style="width:40%"><?php esc_html_e( 'Quote', 'telex-nonduality-quotes' ); ?></th>
								<th style="width:20%"><?php esc_html_e( 'Author', 'telex-nonduality-quotes' ); ?></th>
								<th style="width:20%"><?php esc_html_e( 'Category', 'telex-nonduality-quotes' ); ?></th>
								<th style="width:20%"><?php esc_html_e( 'Actions', 'telex-nonduality-quotes' ); ?></th>
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
											href="<?php echo esc_url( wp_nonce_url( admin_url( 'options-general.php?page=telex-nonduality-quotes&tab=manage&tnq_delete=' . $i ), 'tnq_delete_quote' ) ); ?>"
											class="button button-small"
											onclick="return confirm('<?php echo esc_js( __( 'Delete this quote?', 'telex-nonduality-quotes' ) ); ?>');"
											style="color:#b32d2e;"
										>
											<?php esc_html_e( 'Delete', 'telex-nonduality-quotes' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<p class="description" style="margin-top:10px;">
						<?php
						/* translators: %d: number of custom quotes */
						printf( esc_html__( 'Total custom quotes: %d', 'telex-nonduality-quotes' ), count( $custom_quotes ) );
						?>
					</p>
				<?php endif; ?>

			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'telex_nonduality_quotes_register_rest_route' ) ) {
	function telex_nonduality_quotes_register_rest_route() {
		register_rest_route( 'tnq/v1', '/daily-quote', array(
			'methods'             => 'GET',
			'callback'            => 'telex_nonduality_quotes_daily_quote_callback',
			'permission_callback' => '__return_true',
		) );
	}
}
add_action( 'rest_api_init', 'telex_nonduality_quotes_register_rest_route' );

if ( ! function_exists( 'telex_nonduality_quotes_get_all_quotes' ) ) {
	function telex_nonduality_quotes_get_all_quotes() {
		$quotes = array(
			array( 'text' => 'You are not a drop in the ocean. You are the entire ocean in a drop.', 'author' => 'Rumi', 'category' => 'Sufism' ),
			array( 'text' => 'The eye through which I see God is the same eye through which God sees me.', 'author' => 'Meister Eckhart', 'category' => 'Christian Mysticism' ),
			array( 'text' => 'The Tao that can be told is not the eternal Tao.', 'author' => 'Lao Tzu', 'category' => 'Taoism' ),
			array( 'text' => 'The world is illusion. Brahman alone is real. The world is Brahman.', 'author' => 'Adi Shankara', 'category' => 'Advaita Vedanta' ),
			array( 'text' => 'You are awareness. Awareness is another name for you.', 'author' => 'Ramana Maharshi', 'category' => 'Advaita Vedanta' ),
			array( 'text' => 'Form is emptiness, emptiness is form.', 'author' => 'Heart Sutra', 'category' => 'Buddhism' ),
			array( 'text' => 'Silence is the language of God, all else is poor translation.', 'author' => 'Rumi', 'category' => 'Sufism' ),
			array( 'text' => 'What you seek is seeking you.', 'author' => 'Rumi', 'category' => 'Sufism' ),
			array( 'text' => 'We are not human beings having a spiritual experience. We are spiritual beings having a human experience.', 'author' => 'Pierre Teilhard de Chardin', 'category' => 'Inspirational' ),
			array( 'text' => 'Be still, and know that I am God.', 'author' => 'Psalm 46:10', 'category' => 'Christian Mysticism' ),
			array( 'text' => 'Nature does not hurry, yet everything is accomplished.', 'author' => 'Lao Tzu', 'category' => 'Taoism' ),
			array( 'text' => 'Your own Self-realization is the greatest service you can render the world.', 'author' => 'Ramana Maharshi', 'category' => 'Advaita Vedanta' ),
			array( 'text' => 'In the sky, there is no distinction of east and west; people create distinctions out of their own minds and then believe them to be true.', 'author' => 'Buddha', 'category' => 'Buddhism' ),
			array( 'text' => 'Out beyond ideas of wrongdoing and rightdoing, there is a field. I\'ll meet you there.', 'author' => 'Rumi', 'category' => 'Sufism' ),
			array( 'text' => 'The privilege of a lifetime is to become who you truly are.', 'author' => 'Carl Jung', 'category' => 'Inspirational' ),
			array( 'text' => 'To study the Way is to study the self. To study the self is to forget the self. To forget the self is to be enlightened by all things.', 'author' => 'Dogen', 'category' => 'Zen' ),
			array( 'text' => 'In the beginner\'s mind there are many possibilities, but in the expert\'s mind there are few.', 'author' => 'Shunryu Suzuki', 'category' => 'Zen' ),
			array( 'text' => 'Sitting quietly, doing nothing, spring comes, and the grass grows by itself.', 'author' => 'Matsuo Basho', 'category' => 'Zen' ),
			array( 'text' => 'No snowflake ever falls in the wrong place.', 'author' => 'Zen Proverb', 'category' => 'Zen' ),
			array( 'text' => 'The lamps are different, but the Light is the same.', 'author' => 'Rumi', 'category' => 'Sufism' ),
			array( 'text' => 'God is not found in the soul by adding anything, but by a process of subtraction.', 'author' => 'Meister Eckhart', 'category' => 'Christian Mysticism' ),
			array( 'text' => 'You are an aperture through which the universe is looking at and exploring itself.', 'author' => 'Alan Watts', 'category' => 'Inspirational' ),
			array( 'text' => 'Muddy water is best cleared by leaving it alone.', 'author' => 'Alan Watts', 'category' => 'Taoism' ),
			array( 'text' => 'The mind turned inwards is the Self; turned outwards, it becomes the ego and all the world.', 'author' => 'Ramana Maharshi', 'category' => 'Advaita Vedanta' ),
			array( 'text' => 'When I let go of what I am, I become what I might be.', 'author' => 'Lao Tzu', 'category' => 'Taoism' ),
			array( 'text' => 'The finger pointing at the moon is not the moon.', 'author' => 'Zen Proverb', 'category' => 'Buddhism' ),
			array( 'text' => 'Before enlightenment, chop wood, carry water. After enlightenment, chop wood, carry water.', 'author' => 'Zen Proverb', 'category' => 'Buddhism' ),
			array( 'text' => 'All the Buddhas and all sentient beings are nothing but the One Mind, beside which nothing exists.', 'author' => 'Huang Po', 'category' => 'Zen' ),
			array( 'text' => 'Knowing others is intelligence; knowing yourself is true wisdom.', 'author' => 'Lao Tzu', 'category' => 'Taoism' ),
			array( 'text' => 'The only way to make sense out of change is to plunge into it, move with it, and join the dance.', 'author' => 'Alan Watts', 'category' => 'Inspirational' ),
		);
		$custom_quotes = get_option( 'tnq_custom_quotes', array() );
		if ( ! empty( $custom_quotes ) ) {
			$quotes = array_merge( $quotes, $custom_quotes );
		}
		return $quotes;
	}
}

if ( ! function_exists( 'telex_nonduality_quotes_daily_quote_callback' ) ) {
	function telex_nonduality_quotes_daily_quote_callback( $request ) {
		$quotes    = telex_nonduality_quotes_get_all_quotes();
		$day_seed  = (int) gmdate( 'Ymd' );
		$index     = $day_seed % count( $quotes );
		$quote     = $quotes[ $index ];

		return rest_ensure_response( array(
			'text'   => $quote['text'],
			'author' => $quote['author'],
		) );
	}
}

if ( ! function_exists( 'telex_nonduality_quotes_get_custom_quotes_json' ) ) {
	function telex_nonduality_quotes_get_custom_quotes_json() {
		$custom_quotes = get_option( 'tnq_custom_quotes', array() );
		if ( empty( $custom_quotes ) ) {
			return '[]';
		}
		return wp_json_encode( array_values( $custom_quotes ) );
	}
}

if ( ! function_exists( 'telex_nonduality_quotes_enqueue_custom_quotes' ) ) {
	function telex_nonduality_quotes_enqueue_custom_quotes() {
		$custom_quotes = get_option( 'tnq_custom_quotes', array() );
		if ( ! empty( $custom_quotes ) ) {
			$handle = 'telex-block-telex-nonduality-quotes-view-script';
			wp_add_inline_script(
				$handle,
				'var tnqCustomQuotes = ' . wp_json_encode( array_values( $custom_quotes ) ) . ';',
				'before'
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'telex_nonduality_quotes_enqueue_custom_quotes', 20 );
