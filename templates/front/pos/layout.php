<?php
/**
 * Layout template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Front\POS;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Layout' ) ) {
	/**
	 * Layout template class
	 */
	class DDWCPOS_Layout {
		/**
		 * Construct
		 */
		public function __construct( $ddwcpos_configuration ) {
            $icons_path = DDWCPOS_PLUGIN_URL . 'assets/images/';

            ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
				<head>
					<title><?php echo esc_html( get_bloginfo() ); ?></title>
					<meta charset="<?php bloginfo( 'charset' ); ?>">
					<meta name="viewport" content="width=device-width, initial-scale=1"/>
					<link rel="icon" type="image/x-icon" href="<?php echo esc_url( get_site_icon_url() ); ?>">
					<?php wp_head(); ?>
				</head>
				<body>
					<?php
					if ( is_user_logged_in() ) {
						$user = wp_get_current_user();

						if ( in_array( 'ddwcpos_cashier', $user->roles, true ) || in_array( 'administrator', $user->roles, true ) || in_array( 'shop_manager', $user->roles, true ) || apply_filters( 'ddwcpos_allow_administrator_access_for_pos_to_user', false ) ) {
							?>
							<div id="app"></div>
							<?php
						}
					} else {
						?>
						<div class="ddwcpos-login-wrapper">
							<div class="ddwcpos-mesh-background"></div>
							<div class="ddwcpos-login-card">
								<div class="ddwcpos-login-header">
									<div class="ddwcpos-brand-icon">
										<img src="<?php echo esc_url( ! empty( $ddwcpos_configuration[ 'logo' ] ) ? wp_get_attachment_url( $ddwcpos_configuration[ 'logo' ] ) : $icons_path . 'logo.png' ); ?>" />
									</div>
									<h1><?php echo esc_html( $ddwcpos_configuration[ 'login_heading_text' ] ); ?></h1>
									<p class="ddwcpos-login-subtitle"><?php echo esc_html( $ddwcpos_configuration[ 'login_subtitle_text' ] ); ?></p>
								</div>

								<?php
								// In case of a login error.
									// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only login status flag.
									if ( ! empty( $_GET[ 'login' ] ) && 'failed' === sanitize_text_field( wp_unslash( $_GET[ 'login' ] ) ) ) {
									?>
									<div class="ddwcpos-login-error">
										<p>
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="currentColor" stroke-width="1.5"/><path d="M10 6V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="14" r="1" fill="currentColor"/></svg>
											<?php esc_html_e( 'Wrong Credentials, Try Again!', 'devdiggers-multipos-for-woocommerce' ); ?>
										</p>
									</div>
									<?php
								}
								$args = [
									'echo'           => true,
									'redirect'       => site_url( $ddwcpos_configuration[ 'endpoint' ] ),
									'form_id'        => 'ddwcpos-login-form',
									'label_username' => esc_html__( 'Username or Email', 'devdiggers-multipos-for-woocommerce' ),
									'label_password' => esc_html__( 'Password', 'devdiggers-multipos-for-woocommerce' ),
									'label_remember' => esc_html__( 'Remember Me', 'devdiggers-multipos-for-woocommerce' ),
									'label_log_in'   =>  $ddwcpos_configuration[ 'login_button_text' ],
									'id_username'    => 'user_login',
									'id_password'    => 'user_pass',
									'id_remember'    => 'rememberme',
									'id_submit'      => 'ddwcpos-login-submit',
									'remember'       => ! empty( $ddwcpos_configuration[ 'login_rememberme_enabled' ] ),
									'value_username' => null,
									'value_remember' => true,
								];

								$before_login_button = apply_filters( 'ddwcpos_before_login_button', '', $args );

								$form =
									sprintf(
										'<form name="%1$s" id="%1$s" action="%2$s" method="post">',
										esc_attr( $args[ 'form_id' ] ),
										esc_url( site_url( 'wp-login.php', 'login_post' ) )
									) .
									sprintf(
										'<div class="login-input-group">
											<label for="%1$s">%2$s</label>
											<div class="input-with-icon">
												<span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
												<input type="text" name="log" id="%1$s" autocomplete="username" class="input" value="%3$s" placeholder="%2$s" />
											</div>
										</div>',
										esc_attr( $args[ 'id_username' ] ),
										esc_html( $args[ 'label_username' ] ),
										esc_attr( $args[ 'value_username' ] )
									) .
									sprintf(
										'<div class="login-input-group">
											<label for="%1$s">%2$s</label>
											<div class="input-with-icon">
												<span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
												<input type="password" name="pwd" id="%1$s" autocomplete="current-password" spellcheck="false" class="input" value="" placeholder="%2$s" />
											</div>
										</div>',
										esc_attr( $args[ 'id_password' ] ),
										esc_html( $args[ 'label_password' ] )
									) .
                                    '<div class="login-extra-options">' .
									( $args[ 'remember' ] ?
										sprintf(
											'<div class="login-remember"><label><input name="rememberme" type="checkbox" id="%1$s" value="forever"%2$s /> <span>%3$s</span></label></div>',
											esc_attr( $args[ 'id_remember' ] ),
											( $args['value_remember'] ? ' checked="checked"' : '' ),
											esc_html( $args[ 'label_remember' ] )
										) : ''
									) .
									( ! empty( $ddwcpos_configuration[ 'login_forgot_enabled' ] ) ?
										sprintf(
											'<div class="login-forgot-password"><a href="%1$s" class="ddwcpos-lost-password">%2$s</a></div>',
											esc_url( wc_lostpassword_url() ),
											esc_html__( 'Forgot Password?', 'devdiggers-multipos-for-woocommerce' )
										) : ''
									) .
                                    '</div>' .
									$before_login_button .
									sprintf(
										'<div class="login-submit">
											<button type="submit" name="wp-submit" id="%1$s" class="button button-primary">
												<span>%2$s</span>
												<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
											</button>
											<input type="hidden" name="redirect_to" value="%3$s" />
										</div>',
										esc_attr( $args[ 'id_submit' ] ),
										esc_attr( $args[ 'label_log_in' ] ),
										esc_url( $args[ 'redirect' ] )
									) .
									'</form>';

									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Login form markup is assembled from escaped values and filter output.
									echo $form;

								?>
								<div class="ddwcpos-login-footer">
									<p><?php echo esc_html( $ddwcpos_configuration[ 'login_footer_text' ] ); ?></p>
									<?php if ( ! empty( $ddwcpos_configuration['login_branding_enabled'] ) ) : ?>
										<p class="ddwcpos-login-branding">
											<?php esc_html_e( 'Powered by ', 'devdiggers-multipos-for-woocommerce' ); ?><a href="https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'MultiPOS', 'devdiggers-multipos-for-woocommerce' ); ?></a>
										</p>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</body>

				<!-- Prompt a message in the browser if users disabled JS -->
				<noscript><?php esc_html_e( 'Your browser does not support JavaScript!', 'devdiggers-multipos-for-woocommerce' ); ?></noscript>
            </html>
            <?php
        }
	}
}
