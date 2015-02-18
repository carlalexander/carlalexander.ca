<?php
/**
* Header for authy pages
*/
function authy_header( $step = '' ) { ?>
  <head>
    <?php
      global $wp_version;
      if ( version_compare( $wp_version, '3.3', '<=' ) ) {?>
        <link rel="stylesheet" type="text/css" href="<?php echo admin_url( 'css/login.css' ); ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo admin_url( 'css/colors-fresh.css' ); ?>" />
        <?php
      } elseif ( version_compare( $wp_version, '3.8', '<=' ) ) {
        wp_admin_css("wp-admin", true);
        wp_admin_css("colors-fresh", true);
        wp_admin_css("ie", true);
      } else{
        wp_admin_css("login", true);
      }
    ?>
    <link href="https://www.authy.com/form.authy.min.css" media="screen" rel="stylesheet" type="text/css">
    <script src="https://www.authy.com/form.authy.min.js" type="text/javascript"></script>
    <?php if ( $step == 'verify_installation' ) { ?>
        <link href="<?php echo plugins_url( 'assets/authy.css', __FILE__ ); ?>" media="screen" rel="stylesheet" type="text/css">
        <script type="text/javascript">
        /* <![CDATA[ */
        var AuthyAjax = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' ); ?>"};
        /* ]]> */
        </script>
        <?php wp_print_scripts( array( 'jquery', 'utils') ); ?>
        <script src="<?php echo plugins_url( 'assets/authy-installation.js', __FILE__ ); ?>" type="text/javascript"></script>
    <?php } ?>
  </head>
<?php }

/**
 * Generate the authy token form
 * @param string $username
 * @param array $user_data
 * @param array $user_signature
 * @return string
 */

function authy_token_form( $username, $user_data, $user_signature, $redirect, $remember_me ) {?>
  <html>
    <?php echo authy_header(); ?>
    <body class='login wp-core-ui'>
      <div id="login">
        <h1>
          <a href="http://wordpress.org/" title="Powered by WordPress"><?php echo get_bloginfo( 'name' ); ?></a>
        </h1>
        <h3 style="text-align: center; margin-bottom:10px;">Authy Two-Factor Authentication</h3>
        <p class="message">
          <?php _e( "You can get this token from the Authy mobile app. If you are not using the Authy app we've automatically sent you a token via text-message to cellphone number: ", 'authy' ); ?>
          <strong>
            <?php
              $cellphone = normalize_cellphone( $user_data['phone'] );
              $cellphone = preg_replace( "/^\d{1,3}\-/", 'XXX-', $cellphone );
              $cellphone = preg_replace( "/\-\d{1,3}\-/", '-XXX-', $cellphone );

              echo esc_attr( $cellphone );
            ?>
          </strong>
        </p>

        <form method="POST" id="authy" action="<?php echo wp_login_url(); ?>">
          <label for="authy_token">
            <?php _e( 'Authy Token', 'authy' ); ?>
            <br>
            <input type="text" name="authy_token" id="authy-token" class="input" value="" size="20" autofocus="true" />
          </label>
          <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect ); ?>"/>
          <input type="hidden" name="username" value="<?php echo esc_attr( $username ); ?>"/>
          <input type="hidden" name="rememberme" value="<?php echo esc_attr( $remember_me ); ?>"/>
          <?php if ( isset( $user_signature['authy_signature'] ) && isset( $user_signature['signed_at'] ) ) { ?>
            <input type="hidden" name="authy_signature" value="<?php echo esc_attr( $user_signature['authy_signature'] ); ?>"/>
          <?php } ?>
          <p class="submit">
            <input type="submit" value="<?php echo esc_attr_e( 'Login', 'authy' ) ?>" id="wp_submit" class="button button-primary button-large" />
          </p>
        </form>
      </div>
    </body>
  </html>
<?php }

/**
* Enable authy page
*
* @param mixed $user
* @return string
*/
function render_enable_authy_page( $user, $signature, $errors = array() ) {?>
  <html>
    <?php echo authy_header(); ?>
    <body class='login wp-core-ui'>
      <div id="login">
        <h1><a href="http://wordpress.org/" title="Powered by WordPress"><?php echo get_bloginfo( 'name' ); ?></a></h1>
        <h3 style="text-align: center; margin-bottom:10px;"><?php _e('Enable Authy Two-Factor Authentication', 'authy')?></h3>
        <?php
          if ( !empty( $errors ) ) {
            $message = '';
            foreach ( $errors as $msg ) {
              $message .= '<strong>ERROR: </strong>' . $msg . '<br>';
            }
            ?><div id="login_error"><?php echo _e( $message, 'authy' ); ?></div><?php
          }
        ?>
        <p class="message"><?php _e( 'Your administrator has requested that you add Two-Factor Authentication to your account, please enter your cellphone below to enable.', 'authy' ); ?></p>
        <form method="POST" id="authy" action="wp-login.php">
          <label for="authy_user[country_code]"><?php _e( 'Country', 'authy' ); ?></label>
          <input type="text" name="authy_user[country_code]" id="authy-countries" class="input" />

          <label for="authy_user[cellphone]"><?php _e( 'Cellphone number', 'authy' ); ?></label>
          <input type="tel" name="authy_user[cellphone]" id="authy-cellphone" class="input" />
          <input type="hidden" name="username" value="<?php echo esc_attr( $user->user_login ); ?>"/>
          <input type="hidden" name="step" value="enable_authy"/>
          <input type="hidden" name="authy_signature" value="<?php echo esc_attr( $signature ); ?>"/>

          <p class="submit">
            <input type="submit" value="<?php echo esc_attr_e( 'Enable', 'authy' ) ?>" id="wp_submit" class="button button-primary button-large">
          </p>
        </form>
      </div>
    </body>
  </html>
<?php }

/**
 * Form enable authy on profile
 * @param string $users_key
 * @param array $user_datas
 * @return string
 */
function register_form_on_profile( $users_key, $user_data ) {?>
  <table class="form-table" id="<?php echo esc_attr( $users_key ); ?>">
    <tr>
      <th><label for="phone"><?php _e( 'Country', 'authy' ); ?></label></th>
      <td>
        <input type="text" id="authy-countries" class="small-text" name="<?php echo esc_attr( $users_key ); ?>[country_code]" value="<?php echo esc_attr( $user_data['country_code'] ); ?>" />
      </td>
    </tr>
    <tr>
      <th><label for="phone"><?php _e( 'Cellphone number', 'authy' ); ?></label></th>
      <td>
        <input type="tel" id="authy-cellphone" class="regular-text" name="<?php echo esc_attr( $users_key ); ?>[phone]" value="<?php echo esc_attr( $user_data['phone'] ); ?>" />

        <?php wp_nonce_field( $users_key . 'edit_own', $users_key . '[nonce]' ); ?>
      </td>
    </tr>
  </table>
<?php }

/**
 * Form disable authy on profile
 * @return string
 */
function disable_form_on_profile( $users_key ) {?>
  <table class="form-table" id="<?php echo esc_attr( $users_key ); ?>">
    <tr>
      <th><label for="<?php echo esc_attr( $users_key ); ?>_disable"><?php _e( 'Disable Two Factor Authentication?', 'authy' ); ?></label></th>
      <td>
        <input type="checkbox" id="<?php echo esc_attr( $users_key ); ?>_disable" name="<?php echo esc_attr( $users_key ); ?>[disable_own]" value="1" />
        <label for="<?php echo esc_attr( $users_key ); ?>_disable"><?php _e( 'Yes, disable Authy for your account.', 'authy' ); ?></label>

        <?php wp_nonce_field( $users_key . 'disable_own', $users_key . '[nonce]' ); ?>
      </td>
    </tr>
  </table>
<?php }

/**
 * Form verify authy installation
 * @return string
 */
function authy_installation_form( $user, $user_data, $user_signature, $errors ) {?>
  <html>
    <?php echo authy_header( 'verify_installation' ); ?>
    <body class='login wp-core-ui'>
      <div id="authy-verify">
        <h1><a href="http://wordpress.org/" title="Powered by WordPress"><?php echo get_bloginfo( 'name' ); ?></a></h1>
        <?php if ( !empty( $errors ) ) {?>
            <div id="login_error"><strong><?php echo esc_attr_e( 'ERROR: ', 'authy' ); ?></strong><?php echo esc_attr_e( $errors, 'authy' ); ?></div>
        <?php } ?>
        <form method="POST" id="authy" action="wp-login.php">
          <p><?php echo esc_attr_e( 'To activate your account you need to setup Authy Two-Factor Authentication.', 'authy' ); ?></p>

          <div class='step'>
            <div class='description-step'>
              <span class='number'>1.</span>
              <span><?php printf( __( 'On your phone browser go to <a href="%1$s" alt="install authy" style="padding-left: 18px;">%1$s</a>.', 'authy' ), 'https://www.authy.com/install' ); ?></span>
            </div>
            <img src="<?php echo plugins_url( '/assets/images/step1-image.png', __FILE__ ); ?>" alt='installation' />
          </div>

          <div class='step'>
            <div class='description-step'>
              <span class='number'>2.</span>
              <span><?php printf( __('Open the App and register.', 'authy' ) ) ?></span>
            </div>
            <img src="<?php echo plugins_url( '/assets/images/step2-image.png', __FILE__ ); ?>" alt='smartphones' style='padding-left: 22px;' />
          </div>

          <p class='italic-text'>
            <?php echo esc_attr_e( 'If you don’t have an iPhone or Android ', 'authy' ); ?>
            <a href="#" class="request-sms-link"
              data-username="<?php echo esc_attr( $user->user_login );?>"
              data-signature="<?php echo esc_attr( $user_signature ); ?>"><?php echo esc_attr_e( 'click here to get the Token as a Text Message.', 'authy' ); ?>
            </a>
          </p>

          <label for="authy_token">
            <?php _e( 'Authy Token', 'authy' ); ?>
            <br>
            <input type="text" name="authy_token" id="authy-token" class="input" value="" size="20" />
          </label>
          <input type="hidden" name="username" value="<?php echo esc_attr( $user->user_login ); ?>"/>
          <input type="hidden" name="step" value="verify_installation"/>
          <?php if ( isset( $user_signature ) ) { ?>
            <input type="hidden" name="authy_signature" value="<?php echo esc_attr( $user_signature ); ?>"/>
          <?php } ?>

          <input type="submit" value="<?php echo esc_attr_e( 'Verify Token', 'authy' ) ?>" id="wp_submit" class="button button-primary">
          <div class="rsms">
            <img src="<?php echo plugins_url( '/assets/images/phone-icon.png', __FILE__ ); ?>" alt="cellphone">
            <a href="#" class='request-sms-link' data-username="<?php echo esc_attr( $user->user_login );?>" data-signature="<?php echo esc_attr( $user_signature ); ?>">
              <?php echo esc_attr_e( 'Get the token via SMS', 'authy' ); ?>
            </a>
          </div>
        </form>
      </div>
    </body>
  </html>
<?php }

/**
 * Form for enable authy with JS
 * @return string
 */
function form_enable_on_modal( $users_key, $username, $authy_data, $errors ) {?>
  <p><?php printf( __( 'Authy is not yet configured for your the <strong>%s</strong> account.', 'authy' ), $username ); ?></p>

  <p><?php _e( 'To enable Authy for this account, complete the form below, then click <em>Continue</em>.', 'authy' ); ?></p>

  <?php if ( !empty($errors) ) { ?>
    <div class='error'>
      <?php
        foreach ($errors as $key => $value) {
          if ($key == 'country_code') : ?>
            <p><strong>Country code</strong> is not valid.</p>
          <?php elseif ( $key != 'message' ) : ?>
            <p><strong><?php echo ucfirst($key); ?></strong><?php echo ' ' . $value; ?></p>
          <?php endif;
        }
      ?>
    </div>
  <?php } ?>

  <table class="form-table" id="<?php echo esc_attr( $users_key ); ?>-ajax">
    <tr>
      <th><label for="phone"><?php _e( 'Country', 'authy' ); ?></label></th>
      <td>
        <input type="text" id="authy-countries" class="small-text" name="authy_country_code" value="<?php echo esc_attr( $authy_data['country_code'] ); ?>" required />
      </td>
    </tr>
    <tr>
      <th><label for="phone"><?php _e( 'Cellphone number', 'authy' ); ?></label></th>
      <td>
        <input type="tel" id="authy-cellphone" class="regular-text" name="authy_phone" value="<?php echo esc_attr( $authy_data['phone'] ); ?>" style="width:140px;" />
      </td>
    </tr>
  </table>

  <input type="hidden" name="authy_step" value="" />
  <?php wp_nonce_field( $users_key . '_ajax_check' ); ?>

  <p class="submit">
    <input name="Continue" type="submit" value="<?php esc_attr_e( 'Continue', 'authy' );?>" class="button-primary">
  </p>
<?php }

/**
 * Checkbox for admin disable authy to the user
 * @return string
 */
function checkbox_for_admin_disable_authy( $users_key ) { ?>
  <tr>
      <th><label for="<?php echo esc_attr( $users_key ); ?>"><?php _e( 'Two Factor Authentication', 'authy' ); ?></label></th>
      <td>
          <input type="checkbox" id="<?php echo esc_attr( $users_key ); ?>" name="<?php echo esc_attr( $users_key ); ?>" value="1" checked/>
      </td>
  </tr>
<?php }

/**
 * Render the form to enable authy by Admin user
 * @return string
 */
function render_admin_form_enable_authy( $users_key, $authy_data ) { ?>
  <tr>
      <p><?php _e( 'To enable Authy enter the country and cellphone number of the person who is going to use this account.', 'authy' )?></p>
      <th><label for="phone"><?php _e( 'Country', 'authy' ); ?></label></th>
      <td>
          <input type="text" id="authy-countries" class="small-text" name="<?php echo esc_attr( $users_key ); ?>[country_code]" value="<?php echo esc_attr( $authy_data['country_code'] ); ?>" />
      </td>
  </tr>
  <tr>
      <th><label for="phone"><?php _e( 'Cellphone number', 'authy' ); ?></label></th>
      <td>
          <input type="tel" class="regular-text" id="authy-cellphone" name="<?php echo esc_attr( $users_key ); ?>[phone]" value="<?php echo esc_attr( $authy_data['phone'] ); ?>" />
      </td>
      <?php wp_nonce_field( $users_key . '_edit', "_{$users_key}_wpnonce" ); ?>
  </tr>
  <tr>
      <th><?php _e( 'Force enable Authy', 'authy' ); ?></th>
      <td>
          <label for="force-enable">
              <input name="<?php echo esc_attr( $users_key ); ?>[force_enable_authy]" type="checkbox" value="true" <?php if ($authy_data['force_by_admin'] == 'true') echo 'checked="checked"'; ?> />
              <?php _e( 'Force this user to enable Authy Two-Factor Authentication on the next login.', 'authy' ); ?>
          </label>
      </td>
  </tr>
<?php }

/**
 * Input for user disable authy on modal
 * @return string
 */
function render_disable_authy_on_modal( $users_key, $username ) { ?>
  <p><?php _e( 'Authy is enabled for this account.', 'authy' ); ?></p>
  <p><?php printf( __( 'Click the button below to disable Two-Factor Authentication for <strong>%s</strong>', 'authy' ), $username ); ?></p>

  <p class="submit">
      <input name="Disable" type="submit" value="<?php esc_attr_e( 'Disable Authy', 'authy' );?>" class="button-primary">
  </p>
  <input type="hidden" name="authy_step" value="disable" />

  <?php wp_nonce_field( $users_key . '_ajax_disable' );
}

/**
 * Confirmation when the user enables Authy.
 * @return string
 */
function render_confirmation_authy_enabled( $authy_id, $username, $cellphone, $ajax_url ) {
  if ( $authy_id ) : ?>
    <p>
      <?php printf( __( 'Congratulations, Authy is now configured for <strong>%s</strong> user account.', 'authy' ), $username ); ?>
    </p>
    <p>
      <?php _e( "We've sent you an e-mail and text-message with instruction on how to install the Authy App. If you do not install the App, we'll automatically send you a text-message to your cellphone ", 'authy' ); ?>
      <strong><?php echo esc_attr( $cellphone ); ?></strong>
      <?php _e( 'on every login with the token that you need to use for when you login.', 'authy' ); ?>
    </p>
    <p><a class="button button-primary" href="#" onClick="self.parent.tb_remove();return false;"><?php _e( 'Return to your profile', 'authy' ); ?></a></p>
  <?php else : ?>
    <p><?php printf( __( 'Authy could not be activated for the <strong>%s</strong> user account.', 'authy' ), $username ); ?></p>
    <p><?php _e( 'Please try again later.', 'authy' ); ?></p>
    <p>
      <a class="button button-primary" href="<?php echo esc_url( $ajax_url ); ?>"><?php _e( 'Try again', 'authy' ); ?></a>
    </p>
  <?php endif;
}

/**
 * Confirmation when the user disables Authy.
 */
function render_confirmation_authy_disabled(  ) { ?>
  <p><?php echo esc_attr_e( 'Authy was disabled', 'authy' );?></p>
  <p>
      <a class="button button-primary" href="#" onClick="self.parent.tb_remove();return false;">
          <?php _e( 'Return to your profile', 'authy' ); ?>
      </a>
  </p>
<?php }

/**
 * Normalize cellphone
 * given a cellphone return the normal form
 * 17654305034 -> 765-430-5034
 * normal form: 10 digits, {3}-{3}-{4}
 * @param string $cellphone
 * @return string
 */
function normalize_cellphone( $cellphone ) {
  $cellphone = substr( $cellphone, 0, -4 ) . '-' . substr( $cellphone, -4 );
  if ( strlen( $cellphone ) - 5 > 3 ) {
    $cellphone = substr( $cellphone, 0, -8 ) . '-' . substr( $cellphone, -8 );
  }
  return $cellphone;
}

// closing the last tag is not recommended: http://php.net/basic-syntax.instruction-separation
