<?php
/**
 * @package WP2Static
 *
 * Copyright (c) 2011 Leon Stafford
 */

$ajax_nonce = wp_create_nonce( 'wpstatichtmloutput' );

$tpl = new \WP2Static\TemplateHelper();

?>

<div class="wrap wp2static">
    <?php if ( PHP_VERSION < 7 ) : ?>

   <div class="notice notice-error inline wp2static-notice">
      <h2 class="title">Outdated PHP version detected</h2>
      <p>The current officially supported PHP versions can be found on <a href="http://php.net/supported-versions.php" target="_blank">PHP.net</a></p>

      <p>Whilst the plugin tries to work on the most common PHP environments, it currently requires PHP 5.6 or higher.</p>

      <p>As official security support drops for PHP 5.6 at the end of 2018, it is strongly recommended to upgraded your WordPress hosting environment to PHP 7, ideally, PHP 7.1 or 7.2, as 7.0 will also stop being supported in December, 2018.<br><br>For help on upgrading your environment, please join our support community at <a href="https://wp2static.com/community/" target="_blank">https://wp2static.com/community/</a></p>

      <p>Your current PHP version is: <?php echo PHP_VERSION; ?></p>
    </div>

    <?php endif; ?>


    <?php if ( ! $view['uploads_writable'] ) : ?>

   <div class="notice notice-error inline wp2static-notice">
      <h2 class="title">Your uploads directory is not writable</h2>
      <p>Please ensure that <code><?php echo $view['site_info']->uploads_path; ?></code>
            is writable by your webserver. 
    </p>
    </div>

    <?php endif; ?>

    <?php if ( ! $view['curl_supported'] ) : ?>

   <div class="notice notice-error inline wp2static-notice">
      <h2 class="title">You need the cURL extension enabled on your web server</h2>
        <p> This is a library that allows the plugin to better export your static site out to services like GitHub, S3, Dropbox, BunnyCDN, etc. It's usually an easy fix to get this working. You can try Googling "How to enable cURL extension for PHP", along with the name of the environment you are using to run your WordPress site. This may be something like DigitalOcean, GoDaddy or LAMP, MAMP, WAMP for your webserver on your local computer. If you're still having trouble, the developer of this plugin is easger to help you get up and running. Please ask for help on our <a href="https://forum.wp2static.com">forum</a>.</p>
    </div>

    <?php endif; ?>

    <?php if ( ! class_exists( 'DOMDocument' ) ) : ?>

   <div class="notice notice-error inline wp2static-notice">
      <h2 class="title">You're missing a required PHP library (DOMDocument)</h2>
        <p> This is a library that is used to parse the HTML documents when WP2Static crawls your site. It's usually an easy fix to get this working. You can try Googling "DOMDocument missing", along with the name of the environment you are using to run your WordPress site. This may be something like DigitalOcean, GoDaddy or LAMP, MAMP, WAMP for your webserver on your local computer. If you're still having trouble, the developer of this plugin is easger to help you get up and running. Please ask for help on our <a href="https://forum.wp2static.com">forum</a>.</p>
    </div>

    <?php endif; ?>

    <?php if ( ! $view['permalinks_defined'] ) : ?>

   <div class="notice notice-error inline wp2static-notice">
      <h2 class="title">You need to set your WordPress Pemalinks</h2>

        <p>Due to the nature of how static sites work, you'll need to have some kind of permalinks structure defined in your <a href="<?php echo admin_url( 'options-permalink.php' ); ?>">Permalink Settings</a> within WordPress. To learn more on how to do this, please see WordPress's official guide to the <a href="https://codex.wordpress.org/Settings_Permalinks_Screen">Settings Permalinks Screen</a>.</p>
    </div>

    <?php endif; ?>

  <nav class="nav-tab-wrapper">
    <?php $tab_names = [
            'Help',
            'URL Detection',
            'Crawling',
            'Processing',
            'Advanced Options',
            'Deployment',
            'Logs',
            'Add-ons',
        ];
    ?>

    <?php foreach ( $tab_names as $tab_name ) : ?>
        <?php $active_tab = $tab_name === 'Help' ? ' nav-tab-active' : ''; ?>

        <a href="#" class="nav-tab<?php echo $active_tab; ?>"><?php echo $tab_name; ?></a>

    <?php endforeach; ?>
  </nav>


  <!-- main form containing options that get sent -->
  <form id="general-options" class="options-form" method="post" action="">

    <!-- placeholder input fields to allow select menu deployment options to use existing behaviour -->
    <span class="hiddenExportOptions" style="display:none;">
        <?php $tpl->displayCheckbox( $this, 'createZip', 'Create a ZIP file of your statically exported site, ready for you to manually deploy. Download link will appear in the Export Log below' ); ?>
    </span>

    <?php

    function generateDeploymentMethodOptions() {
        $options = array(
            'folder' => array( 'Subdirectory on current server', 'free' ),
            'zip' => array( 'ZIP archive (.zip)', 'free' ),
        );

        $options = apply_filters(
            'wp2static_add_deployment_method_option_to_ui',
            $options
        );

        // TODO: format list here, grouping free and add-on deploy types

        foreach ( $options as $key => $value ) {
            echo "<option value='$key'>$value[0]</option>";
        }
    }

    ?>

    <div class="wp2static-content-wrapper">

    <?php require_once __DIR__ . '/tab_help.php'; ?>
    <?php require_once __DIR__ . '/tab_detection.php'; ?>
    <?php require_once __DIR__ . '/tab_crawling.php'; ?>
    <?php require_once __DIR__ . '/tab_processing.php'; ?>
    <?php require_once __DIR__ . '/tab_advanced.php'; ?>
    <?php require_once __DIR__ . '/tab_export.php'; ?>
    <?php require_once __DIR__ . '/tab_logs.php'; ?>
    <?php require_once __DIR__ . '/tab_add_ons.php'; ?>

    </div>

    <span class="submit" style="display:none;">
        <?php wp_nonce_field( $view['onceAction'] ); ?>
      <input id="formActionHiddenField" class="hiddenActionField" type="hidden" name="action" value="wp_static_html_output_ajax" />
      <input id="basedir" type="hidden" name="basedir" value="" />
      <input id="site_url" type="hidden" name="site_url" value="" />
      <input id="wp_uploads_path" type="hidden" name="wp_uploads_path" value="" />
      <input id="wp_uploads_url" type="hidden" name="wp_uploads_url" value="" />
      <input id="subdirectory" type="hidden" name="subdirectory" value="<?php echo $view['site_info']->subdirectory; ?>" />
      <input id="site_path" type="hidden" name="site_path" value="" />
      <input id="wp_inc" type="hidden" name="wp_inc" value="" />
      <input id="wp_active_theme" type="hidden" name="wp_active_theme" value="" />
      <input id="wp_uploads" type="hidden" name="wp_uploads" value="" />
      <input id="hiddenNonceField" type="hidden" name="nonce" value="<?php echo $ajax_nonce; ?>" />
      <input id="hiddenAJAXAction" type="hidden" name="ajax_action" value="" />
      <input name="staticExportSettings" class="hiddenSettingsField" type="hidden" name="action" value="" />
    </span>
  </form>

    </div>
  </div>

    <div id="wp2static-footer">

          <div class="inside">

            <div class="submit">
                <?php wp_nonce_field( $view['onceAction'] ); ?>
              <button id="startExportButton" class="wp2static-btn blue" disabled>
                <?php echo __( 'Start Static Site Export', 'static-html-output-plugin' ); ?>
              </button>
              <button class="wp2static-btn saveSettingsButton" disabled>
                <?php echo __( 'Save Current Options', 'static-html-output-plugin' ); ?>
              </button>
              <button class="wp2static-btn resetDefaultSettingsButton" disabled>
                <?php echo __( 'Reset to Default Settings', 'static-html-output-plugin' ); ?>
              </button>
              <button style="display:none;" class="wp2static-btn cancelExportButton">
                <?php echo __( 'Cancel Export', 'static-html-output-plugin' ); ?>
                </button>

              <a href="" id="downloadZIP">
                <button class="wp2static-btn btn-call-to-action" target="_blank">
                <?php echo __( 'Download ZIP', 'static-html-output-plugin' ); ?>
                </button>
              </a>

              <a href="#" class="wp2static-btn btn-call-to-action" target="_blank" id="goToMyStaticSite" style="display:none;">
                <?php echo __( 'Open Deployed Site', 'static-html-output-plugin' ); ?>
              </a>

              <div id="export_timer"></div>

            </div>

        </div>

            <div id="pbar-container">
                <div id="pbar-fill">

                </div>

                <div id="progress-container">
                  <div id="progress">
                    <div class="pulsate-css"></div>
                    <div id="current_action">
                        <?php echo __( 'Starting Export', 'static-html-output-plugin' ); ?>
                    </div>
                  </div>

                  <p id="exportDuration" style="display:block;"></p>
                </div>
            </div>

    </div><!-- end wp2static-footer -->

</div>
