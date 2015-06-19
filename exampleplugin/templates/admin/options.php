<h1>Adwords URL Tracking Settings</h1>
<div class="wrap">
    
    <form method="post" action="options.php">
        <?php settings_fields( $prefix . '-group' );   ?>
    <h2>Configuration</h2>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="cookie-path">Cookie Path</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'cookie-path'); ?>
                    <p class="description">The path scope of which the tracking cookie will be applied.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cookie-path">Cookie Name:</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'cookie-name'); ?>
                    <p class="description">The name of the cookie (NO SPACES!).</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cookie-path">Cookie Lifetime (Days)</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'cookie-lifetime'); ?>
                    <p class="description">The lifetime of the cookie in days.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cookie-path">Session Parameter Name:</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'session-param'); ?>
                    <p class="description">The name of the session parameter (Only change to avoid clashes with custom code).</p>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit"><?php submit_button('Save'); ?></p>
    <h2>Template Parameters</h2>
    <p class="description">These settings will govern how you setup your campaign level URL tracking templates in AdWords.</p>
    <p><input class="widefat" type="text" disabled value="http://<?php echo $_SERVER['HTTP_HOST']; ?>/wp-adwtracker?<?php echo $options[$prefix . '-option-url-template-lpurl']; ?>={lpurl}&<?php echo $options[$prefix . '-option-url-template-keyword']; ?>={keyword}&<?php echo $options[$prefix . '-option-url-template-matchtype']; ?>={matchtype}&<?php echo $options[$prefix . '-option-url-template-creative']; ?>={creative}"></p>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="url-template-keyword">Keyword</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'url-template-keyword'); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="url-template-matchtype">Match Type:</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'url-template-matchtype'); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="url-template-creative">Creative:</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'url-template-creative'); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="url-template-lpurl">Landing Page URL:</label>
                </th>
                <td>
                    <?php render_option_field($prefix, $options, 'url-template-lpurl'); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit"><?php submit_button('Save'); ?></p>
    </form>
    
</div>
