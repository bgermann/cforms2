<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2017 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Enhanced Internationalization Functions for cforms2
 * Provides comprehensive i18n support with modern WordPress standards
 */

/**
 * Initialize internationalization
 */
function cforms2_init_i18n() {
    // Load plugin text domain
    load_plugin_textdomain('cforms2', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    
    // Set up locale-specific configurations
    cforms2_setup_locale_config();
    
    // Register date/time formats
    cforms2_register_datetime_formats();
    
    // Setup RTL support
    cforms2_setup_rtl_support();
}

/**
 * Setup locale-specific configurations
 */
function cforms2_setup_locale_config() {
    $locale = get_locale();
    
    // Store locale-specific settings
    $locale_config = array(
        'locale' => $locale,
        'text_direction' => is_rtl() ? 'rtl' : 'ltr',
        'date_format' => get_option('date_format'),
        'time_format' => get_option('time_format'),
        'start_of_week' => get_option('start_of_week'),
        'timezone' => get_option('timezone_string'),
        'decimal_separator' => cforms2_get_decimal_separator(),
        'thousands_separator' => cforms2_get_thousands_separator(),
        'currency_symbol' => cforms2_get_currency_symbol(),
        'currency_position' => cforms2_get_currency_position()
    );
    
    // Cache locale config
    wp_cache_set('cforms2_locale_config', $locale_config, 'cforms2', HOUR_IN_SECONDS);
}

/**
 * Get decimal separator for current locale
 */
function cforms2_get_decimal_separator() {
    $locale = get_locale();
    
    $separators = array(
        'de_DE' => ',',
        'de_AT' => ',',
        'de_CH' => '.',
        'fr_FR' => ',',
        'fr_BE' => ',',
        'fr_CA' => ',',
        'es_ES' => ',',
        'es_MX' => '.',
        'it_IT' => ',',
        'pt_BR' => ',',
        'pt_PT' => ',',
        'ru_RU' => ',',
        'pl_PL' => ',',
        'nl_NL' => ',',
        'sv_SE' => ',',
        'da_DK' => ',',
        'no_NO' => ',',
        'fi_FI' => ',',
    );
    
    return isset($separators[$locale]) ? $separators[$locale] : '.';
}

/**
 * Get thousands separator for current locale
 */
function cforms2_get_thousands_separator() {
    $locale = get_locale();
    
    $separators = array(
        'de_DE' => '.',
        'de_AT' => '.',
        'de_CH' => "'",
        'fr_FR' => ' ',
        'fr_BE' => ' ',
        'fr_CA' => ' ',
        'es_ES' => '.',
        'es_MX' => ',',
        'it_IT' => '.',
        'pt_BR' => '.',
        'pt_PT' => ' ',
        'ru_RU' => ' ',
        'pl_PL' => ' ',
        'nl_NL' => '.',
        'sv_SE' => ' ',
        'da_DK' => '.',
        'no_NO' => ' ',
        'fi_FI' => ' ',
    );
    
    return isset($separators[$locale]) ? $separators[$locale] : ',';
}

/**
 * Get currency symbol for current locale
 */
function cforms2_get_currency_symbol() {
    $locale = get_locale();
    
    $symbols = array(
        'de_DE' => '€',
        'de_AT' => '€',
        'de_CH' => 'CHF',
        'fr_FR' => '€',
        'fr_BE' => '€',
        'fr_CA' => '$',
        'es_ES' => '€',
        'es_MX' => '$',
        'it_IT' => '€',
        'pt_BR' => 'R$',
        'pt_PT' => '€',
        'ru_RU' => '₽',
        'pl_PL' => 'zł',
        'nl_NL' => '€',
        'sv_SE' => 'kr',
        'da_DK' => 'kr',
        'no_NO' => 'kr',
        'fi_FI' => '€',
        'ja_JP' => '¥',
        'ko_KR' => '₩',
        'zh_CN' => '¥',
        'en_GB' => '£',
        'en_AU' => '$',
        'en_CA' => '$',
    );
    
    return isset($symbols[$locale]) ? $symbols[$locale] : '$';
}

/**
 * Get currency position for current locale
 */
function cforms2_get_currency_position() {
    $locale = get_locale();
    
    // 'before' or 'after'
    $positions = array(
        'de_DE' => 'after',
        'de_AT' => 'after',
        'de_CH' => 'before',
        'fr_FR' => 'after',
        'fr_BE' => 'after',
        'fr_CA' => 'before',
        'es_ES' => 'after',
        'es_MX' => 'before',
        'it_IT' => 'after',
        'pt_BR' => 'before',
        'pt_PT' => 'after',
        'ru_RU' => 'after',
        'pl_PL' => 'after',
        'nl_NL' => 'before',
        'sv_SE' => 'after',
        'da_DK' => 'after',
        'no_NO' => 'after',
        'fi_FI' => 'after',
    );
    
    return isset($positions[$locale]) ? $positions[$locale] : 'before';
}

/**
 * Register locale-specific date/time formats
 */
function cforms2_register_datetime_formats() {
    $locale = get_locale();
    
    $formats = array(
        'de_DE' => array(
            'date' => 'd.m.Y',
            'time' => 'H:i',
            'datetime' => 'd.m.Y H:i',
            'short_date' => 'd.m.y',
            'long_date' => 'l, d. F Y'
        ),
        'fr_FR' => array(
            'date' => 'd/m/Y',
            'time' => 'H:i',
            'datetime' => 'd/m/Y H:i',
            'short_date' => 'd/m/y',
            'long_date' => 'l d F Y'
        ),
        'es_ES' => array(
            'date' => 'd/m/Y',
            'time' => 'H:i',
            'datetime' => 'd/m/Y H:i',
            'short_date' => 'd/m/y',
            'long_date' => 'l, d \d\e F \d\e Y'
        ),
        'it_IT' => array(
            'date' => 'd/m/Y',
            'time' => 'H:i',
            'datetime' => 'd/m/Y H:i',
            'short_date' => 'd/m/y',
            'long_date' => 'l d F Y'
        ),
        'pt_BR' => array(
            'date' => 'd/m/Y',
            'time' => 'H:i',
            'datetime' => 'd/m/Y H:i',
            'short_date' => 'd/m/y',
            'long_date' => 'l, d \d\e F \d\e Y'
        ),
        'ja_JP' => array(
            'date' => 'Y/m/d',
            'time' => 'H:i',
            'datetime' => 'Y/m/d H:i',
            'short_date' => 'y/m/d',
            'long_date' => 'Y年m月d日(l)'
        ),
        'ko_KR' => array(
            'date' => 'Y.m.d',
            'time' => 'H:i',
            'datetime' => 'Y.m.d H:i',
            'short_date' => 'y.m.d',
            'long_date' => 'Y년 m월 d일 l'
        ),
        'zh_CN' => array(
            'date' => 'Y/m/d',
            'time' => 'H:i',
            'datetime' => 'Y/m/d H:i',
            'short_date' => 'y/m/d',
            'long_date' => 'Y年m月d日 l'
        )
    );
    
    $default_formats = array(
        'date' => 'm/d/Y',
        'time' => 'g:i A',
        'datetime' => 'm/d/Y g:i A',
        'short_date' => 'm/d/y',
        'long_date' => 'l, F j, Y'
    );
    
    $locale_formats = isset($formats[$locale]) ? $formats[$locale] : $default_formats;
    
    // Cache formats
    wp_cache_set('cforms2_datetime_formats', $locale_formats, 'cforms2', HOUR_IN_SECONDS);
}

/**
 * Setup RTL support
 */
function cforms2_setup_rtl_support() {
    if (is_rtl()) {
        // Enqueue RTL stylesheet
        add_action('wp_enqueue_scripts', 'cforms2_enqueue_rtl_styles');
        add_action('admin_enqueue_scripts', 'cforms2_enqueue_rtl_admin_styles');
        
        // Add RTL body class
        add_filter('body_class', 'cforms2_add_rtl_body_class');
    }
}

/**
 * Enqueue RTL styles for frontend
 */
function cforms2_enqueue_rtl_styles() {
    wp_enqueue_style(
        'cforms2-rtl',
        plugin_dir_url(__FILE__) . 'styling/rtl.css',
        array('cforms2-style'),
        CFORMS2_VERSION
    );
}

/**
 * Enqueue RTL styles for admin
 */
function cforms2_enqueue_rtl_admin_styles() {
    wp_enqueue_style(
        'cforms2-admin-rtl',
        plugin_dir_url(__FILE__) . 'admin-rtl.css',
        array('cforms2-admin'),
        CFORMS2_VERSION
    );
}

/**
 * Add RTL body class
 */
function cforms2_add_rtl_body_class($classes) {
    $classes[] = 'cforms2-rtl';
    return $classes;
}

/**
 * Format number according to locale
 */
function cforms2_format_number($number, $decimals = 2) {
    $config = wp_cache_get('cforms2_locale_config', 'cforms2');
    if (!$config) {
        cforms2_setup_locale_config();
        $config = wp_cache_get('cforms2_locale_config', 'cforms2');
    }
    
    return number_format(
        $number,
        $decimals,
        $config['decimal_separator'],
        $config['thousands_separator']
    );
}

/**
 * Format currency according to locale
 */
function cforms2_format_currency($amount, $decimals = 2) {
    $config = wp_cache_get('cforms2_locale_config', 'cforms2');
    if (!$config) {
        cforms2_setup_locale_config();
        $config = wp_cache_get('cforms2_locale_config', 'cforms2');
    }
    
    $formatted_amount = cforms2_format_number($amount, $decimals);
    
    if ($config['currency_position'] === 'before') {
        return $config['currency_symbol'] . ' ' . $formatted_amount;
    } else {
        return $formatted_amount . ' ' . $config['currency_symbol'];
    }
}

/**
 * Format date according to locale
 */
function cforms2_format_date($timestamp, $format_type = 'date') {
    $formats = wp_cache_get('cforms2_datetime_formats', 'cforms2');
    if (!$formats) {
        cforms2_register_datetime_formats();
        $formats = wp_cache_get('cforms2_datetime_formats', 'cforms2');
    }
    
    $format = isset($formats[$format_type]) ? $formats[$format_type] : $formats['date'];
    
    // Use WordPress timezone
    $timezone = new \DateTimeZone(get_option('timezone_string') ?: 'UTC');
    $date = new \DateTime('@' . $timestamp);
    $date->setTimezone($timezone);
    
    return $date->format($format);
}

/**
 * Get localized field labels
 */
function cforms2_get_field_labels() {
    return array(
        'required' => __('Required', 'cforms2'),
        'optional' => __('Optional', 'cforms2'),
        'name' => __('Name', 'cforms2'),
        'first_name' => __('First Name', 'cforms2'),
        'last_name' => __('Last Name', 'cforms2'),
        'email' => __('Email', 'cforms2'),
        'phone' => __('Phone', 'cforms2'),
        'address' => __('Address', 'cforms2'),
        'city' => __('City', 'cforms2'),
        'state' => __('State/Province', 'cforms2'),
        'zip' => __('ZIP/Postal Code', 'cforms2'),
        'country' => __('Country', 'cforms2'),
        'website' => __('Website', 'cforms2'),
        'company' => __('Company', 'cforms2'),
        'message' => __('Message', 'cforms2'),
        'subject' => __('Subject', 'cforms2'),
        'date' => __('Date', 'cforms2'),
        'time' => __('Time', 'cforms2'),
        'file' => __('File', 'cforms2'),
        'submit' => __('Submit', 'cforms2'),
        'reset' => __('Reset', 'cforms2'),
        'cancel' => __('Cancel', 'cforms2'),
        'next' => __('Next', 'cforms2'),
        'previous' => __('Previous', 'cforms2'),
        'finish' => __('Finish', 'cforms2')
    );
}

/**
 * Get localized validation messages
 */
function cforms2_get_validation_messages() {
    return array(
        'required' => __('This field is required.', 'cforms2'),
        'email' => __('Please enter a valid email address.', 'cforms2'),
        'url' => __('Please enter a valid URL.', 'cforms2'),
        'number' => __('Please enter a valid number.', 'cforms2'),
        'integer' => __('Please enter a valid integer.', 'cforms2'),
        'min_length' => __('Please enter at least %d characters.', 'cforms2'),
        'max_length' => __('Please enter no more than %d characters.', 'cforms2'),
        'min_value' => __('Please enter a value greater than or equal to %s.', 'cforms2'),
        'max_value' => __('Please enter a value less than or equal to %s.', 'cforms2'),
        'pattern' => __('Please match the requested format.', 'cforms2'),
        'file_size' => __('File size must be less than %s.', 'cforms2'),
        'file_type' => __('File type not allowed.', 'cforms2'),
        'date_format' => __('Please enter a valid date.', 'cforms2'),
        'time_format' => __('Please enter a valid time.', 'cforms2'),
        'captcha' => __('Please verify that you are human.', 'cforms2'),
        'terms' => __('Please accept the terms and conditions.', 'cforms2'),
        'privacy' => __('Please accept the privacy policy.', 'cforms2')
    );
}

/**
 * Get localized admin messages
 */
function cforms2_get_admin_messages() {
    return array(
        'form_saved' => __('Form saved successfully.', 'cforms2'),
        'form_deleted' => __('Form deleted successfully.', 'cforms2'),
        'settings_saved' => __('Settings saved successfully.', 'cforms2'),
        'import_success' => __('Import completed successfully.', 'cforms2'),
        'export_success' => __('Export completed successfully.', 'cforms2'),
        'backup_created' => __('Backup created successfully.', 'cforms2'),
        'backup_restored' => __('Backup restored successfully.', 'cforms2'),
        'cache_cleared' => __('Cache cleared successfully.', 'cforms2'),
        'error_occurred' => __('An error occurred. Please try again.', 'cforms2'),
        'invalid_form' => __('Invalid form data.', 'cforms2'),
        'permission_denied' => __('You do not have permission to perform this action.', 'cforms2'),
        'file_upload_error' => __('File upload failed.', 'cforms2'),
        'database_error' => __('Database error occurred.', 'cforms2'),
        'email_send_error' => __('Failed to send email.', 'cforms2'),
        'form_not_found' => __('Form not found.', 'cforms2')
    );
}

/**
 * Get localized country list
 */
function cforms2_get_countries() {
    return array(
        'AD' => __('Andorra', 'cforms2'),
        'AE' => __('United Arab Emirates', 'cforms2'),
        'AF' => __('Afghanistan', 'cforms2'),
        'AG' => __('Antigua and Barbuda', 'cforms2'),
        'AI' => __('Anguilla', 'cforms2'),
        'AL' => __('Albania', 'cforms2'),
        'AM' => __('Armenia', 'cforms2'),
        'AO' => __('Angola', 'cforms2'),
        'AQ' => __('Antarctica', 'cforms2'),
        'AR' => __('Argentina', 'cforms2'),
        'AS' => __('American Samoa', 'cforms2'),
        'AT' => __('Austria', 'cforms2'),
        'AU' => __('Australia', 'cforms2'),
        'AW' => __('Aruba', 'cforms2'),
        'AX' => __('Åland Islands', 'cforms2'),
        'AZ' => __('Azerbaijan', 'cforms2'),
        'BA' => __('Bosnia and Herzegovina', 'cforms2'),
        'BB' => __('Barbados', 'cforms2'),
        'BD' => __('Bangladesh', 'cforms2'),
        'BE' => __('Belgium', 'cforms2'),
        'BF' => __('Burkina Faso', 'cforms2'),
        'BG' => __('Bulgaria', 'cforms2'),
        'BH' => __('Bahrain', 'cforms2'),
        'BI' => __('Burundi', 'cforms2'),
        'BJ' => __('Benin', 'cforms2'),
        'BL' => __('Saint Barthélemy', 'cforms2'),
        'BM' => __('Bermuda', 'cforms2'),
        'BN' => __('Brunei', 'cforms2'),
        'BO' => __('Bolivia', 'cforms2'),
        'BQ' => __('Bonaire, Sint Eustatius and Saba', 'cforms2'),
        'BR' => __('Brazil', 'cforms2'),
        'BS' => __('Bahamas', 'cforms2'),
        'BT' => __('Bhutan', 'cforms2'),
        'BV' => __('Bouvet Island', 'cforms2'),
        'BW' => __('Botswana', 'cforms2'),
        'BY' => __('Belarus', 'cforms2'),
        'BZ' => __('Belize', 'cforms2'),
        'CA' => __('Canada', 'cforms2'),
        'CC' => __('Cocos (Keeling) Islands', 'cforms2'),
        'CD' => __('Congo (Kinshasa)', 'cforms2'),
        'CF' => __('Central African Republic', 'cforms2'),
        'CG' => __('Congo (Brazzaville)', 'cforms2'),
        'CH' => __('Switzerland', 'cforms2'),
        'CI' => __('Côte d\'Ivoire', 'cforms2'),
        'CK' => __('Cook Islands', 'cforms2'),
        'CL' => __('Chile', 'cforms2'),
        'CM' => __('Cameroon', 'cforms2'),
        'CN' => __('China', 'cforms2'),
        'CO' => __('Colombia', 'cforms2'),
        'CR' => __('Costa Rica', 'cforms2'),
        'CU' => __('Cuba', 'cforms2'),
        'CV' => __('Cape Verde', 'cforms2'),
        'CW' => __('Curaçao', 'cforms2'),
        'CX' => __('Christmas Island', 'cforms2'),
        'CY' => __('Cyprus', 'cforms2'),
        'CZ' => __('Czech Republic', 'cforms2'),
        'DE' => __('Germany', 'cforms2'),
        'DJ' => __('Djibouti', 'cforms2'),
        'DK' => __('Denmark', 'cforms2'),
        'DM' => __('Dominica', 'cforms2'),
        'DO' => __('Dominican Republic', 'cforms2'),
        'DZ' => __('Algeria', 'cforms2'),
        'EC' => __('Ecuador', 'cforms2'),
        'EE' => __('Estonia', 'cforms2'),
        'EG' => __('Egypt', 'cforms2'),
        'EH' => __('Western Sahara', 'cforms2'),
        'ER' => __('Eritrea', 'cforms2'),
        'ES' => __('Spain', 'cforms2'),
        'ET' => __('Ethiopia', 'cforms2'),
        'FI' => __('Finland', 'cforms2'),
        'FJ' => __('Fiji', 'cforms2'),
        'FK' => __('Falkland Islands', 'cforms2'),
        'FM' => __('Micronesia', 'cforms2'),
        'FO' => __('Faroe Islands', 'cforms2'),
        'FR' => __('France', 'cforms2'),
        'GA' => __('Gabon', 'cforms2'),
        'GB' => __('United Kingdom', 'cforms2'),
        'GD' => __('Grenada', 'cforms2'),
        'GE' => __('Georgia', 'cforms2'),
        'GF' => __('French Guiana', 'cforms2'),
        'GG' => __('Guernsey', 'cforms2'),
        'GH' => __('Ghana', 'cforms2'),
        'GI' => __('Gibraltar', 'cforms2'),
        'GL' => __('Greenland', 'cforms2'),
        'GM' => __('Gambia', 'cforms2'),
        'GN' => __('Guinea', 'cforms2'),
        'GP' => __('Guadeloupe', 'cforms2'),
        'GQ' => __('Equatorial Guinea', 'cforms2'),
        'GR' => __('Greece', 'cforms2'),
        'GS' => __('South Georgia and the South Sandwich Islands', 'cforms2'),
        'GT' => __('Guatemala', 'cforms2'),
        'GU' => __('Guam', 'cforms2'),
        'GW' => __('Guinea-Bissau', 'cforms2'),
        'GY' => __('Guyana', 'cforms2'),
        'HK' => __('Hong Kong', 'cforms2'),
        'HM' => __('Heard Island and McDonald Islands', 'cforms2'),
        'HN' => __('Honduras', 'cforms2'),
        'HR' => __('Croatia', 'cforms2'),
        'HT' => __('Haiti', 'cforms2'),
        'HU' => __('Hungary', 'cforms2'),
        'ID' => __('Indonesia', 'cforms2'),
        'IE' => __('Ireland', 'cforms2'),
        'IL' => __('Israel', 'cforms2'),
        'IM' => __('Isle of Man', 'cforms2'),
        'IN' => __('India', 'cforms2'),
        'IO' => __('British Indian Ocean Territory', 'cforms2'),
        'IQ' => __('Iraq', 'cforms2'),
        'IR' => __('Iran', 'cforms2'),
        'IS' => __('Iceland', 'cforms2'),
        'IT' => __('Italy', 'cforms2'),
        'JE' => __('Jersey', 'cforms2'),
        'JM' => __('Jamaica', 'cforms2'),
        'JO' => __('Jordan', 'cforms2'),
        'JP' => __('Japan', 'cforms2'),
        'KE' => __('Kenya', 'cforms2'),
        'KG' => __('Kyrgyzstan', 'cforms2'),
        'KH' => __('Cambodia', 'cforms2'),
        'KI' => __('Kiribati', 'cforms2'),
        'KM' => __('Comoros', 'cforms2'),
        'KN' => __('Saint Kitts and Nevis', 'cforms2'),
        'KP' => __('North Korea', 'cforms2'),
        'KR' => __('South Korea', 'cforms2'),
        'KW' => __('Kuwait', 'cforms2'),
        'KY' => __('Cayman Islands', 'cforms2'),
        'KZ' => __('Kazakhstan', 'cforms2'),
        'LA' => __('Laos', 'cforms2'),
        'LB' => __('Lebanon', 'cforms2'),
        'LC' => __('Saint Lucia', 'cforms2'),
        'LI' => __('Liechtenstein', 'cforms2'),
        'LK' => __('Sri Lanka', 'cforms2'),
        'LR' => __('Liberia', 'cforms2'),
        'LS' => __('Lesotho', 'cforms2'),
        'LT' => __('Lithuania', 'cforms2'),
        'LU' => __('Luxembourg', 'cforms2'),
        'LV' => __('Latvia', 'cforms2'),
        'LY' => __('Libya', 'cforms2'),
        'MA' => __('Morocco', 'cforms2'),
        'MC' => __('Monaco', 'cforms2'),
        'MD' => __('Moldova', 'cforms2'),
        'ME' => __('Montenegro', 'cforms2'),
        'MF' => __('Saint Martin (French part)', 'cforms2'),
        'MG' => __('Madagascar', 'cforms2'),
        'MH' => __('Marshall Islands', 'cforms2'),
        'MK' => __('North Macedonia', 'cforms2'),
        'ML' => __('Mali', 'cforms2'),
        'MM' => __('Myanmar', 'cforms2'),
        'MN' => __('Mongolia', 'cforms2'),
        'MO' => __('Macao', 'cforms2'),
        'MP' => __('Northern Mariana Islands', 'cforms2'),
        'MQ' => __('Martinique', 'cforms2'),
        'MR' => __('Mauritania', 'cforms2'),
        'MS' => __('Montserrat', 'cforms2'),
        'MT' => __('Malta', 'cforms2'),
        'MU' => __('Mauritius', 'cforms2'),
        'MV' => __('Maldives', 'cforms2'),
        'MW' => __('Malawi', 'cforms2'),
        'MX' => __('Mexico', 'cforms2'),
        'MY' => __('Malaysia', 'cforms2'),
        'MZ' => __('Mozambique', 'cforms2'),
        'NA' => __('Namibia', 'cforms2'),
        'NC' => __('New Caledonia', 'cforms2'),
        'NE' => __('Niger', 'cforms2'),
        'NF' => __('Norfolk Island', 'cforms2'),
        'NG' => __('Nigeria', 'cforms2'),
        'NI' => __('Nicaragua', 'cforms2'),
        'NL' => __('Netherlands', 'cforms2'),
        'NO' => __('Norway', 'cforms2'),
        'NP' => __('Nepal', 'cforms2'),
        'NR' => __('Nauru', 'cforms2'),
        'NU' => __('Niue', 'cforms2'),
        'NZ' => __('New Zealand', 'cforms2'),
        'OM' => __('Oman', 'cforms2'),
        'PA' => __('Panama', 'cforms2'),
        'PE' => __('Peru', 'cforms2'),
        'PF' => __('French Polynesia', 'cforms2'),
        'PG' => __('Papua New Guinea', 'cforms2'),
        'PH' => __('Philippines', 'cforms2'),
        'PK' => __('Pakistan', 'cforms2'),
        'PL' => __('Poland', 'cforms2'),
        'PM' => __('Saint Pierre and Miquelon', 'cforms2'),
        'PN' => __('Pitcairn', 'cforms2'),
        'PR' => __('Puerto Rico', 'cforms2'),
        'PS' => __('Palestinian Territory', 'cforms2'),
        'PT' => __('Portugal', 'cforms2'),
        'PW' => __('Palau', 'cforms2'),
        'PY' => __('Paraguay', 'cforms2'),
        'QA' => __('Qatar', 'cforms2'),
        'RE' => __('Réunion', 'cforms2'),
        'RO' => __('Romania', 'cforms2'),
        'RS' => __('Serbia', 'cforms2'),
        'RU' => __('Russia', 'cforms2'),
        'RW' => __('Rwanda', 'cforms2'),
        'SA' => __('Saudi Arabia', 'cforms2'),
        'SB' => __('Solomon Islands', 'cforms2'),
        'SC' => __('Seychelles', 'cforms2'),
        'SD' => __('Sudan', 'cforms2'),
        'SE' => __('Sweden', 'cforms2'),
        'SG' => __('Singapore', 'cforms2'),
        'SH' => __('Saint Helena', 'cforms2'),
        'SI' => __('Slovenia', 'cforms2'),
        'SJ' => __('Svalbard and Jan Mayen', 'cforms2'),
        'SK' => __('Slovakia', 'cforms2'),
        'SL' => __('Sierra Leone', 'cforms2'),
        'SM' => __('San Marino', 'cforms2'),
        'SN' => __('Senegal', 'cforms2'),
        'SO' => __('Somalia', 'cforms2'),
        'SR' => __('Suriname', 'cforms2'),
        'SS' => __('South Sudan', 'cforms2'),
        'ST' => __('São Tomé and Príncipe', 'cforms2'),
        'SV' => __('El Salvador', 'cforms2'),
        'SX' => __('Sint Maarten', 'cforms2'),
        'SY' => __('Syria', 'cforms2'),
        'SZ' => __('Eswatini', 'cforms2'),
        'TC' => __('Turks and Caicos Islands', 'cforms2'),
        'TD' => __('Chad', 'cforms2'),
        'TF' => __('French Southern Territories', 'cforms2'),
        'TG' => __('Togo', 'cforms2'),
        'TH' => __('Thailand', 'cforms2'),
        'TJ' => __('Tajikistan', 'cforms2'),
        'TK' => __('Tokelau', 'cforms2'),
        'TL' => __('Timor-Leste', 'cforms2'),
        'TM' => __('Turkmenistan', 'cforms2'),
        'TN' => __('Tunisia', 'cforms2'),
        'TO' => __('Tonga', 'cforms2'),
        'TR' => __('Turkey', 'cforms2'),
        'TT' => __('Trinidad and Tobago', 'cforms2'),
        'TV' => __('Tuvalu', 'cforms2'),
        'TW' => __('Taiwan', 'cforms2'),
        'TZ' => __('Tanzania', 'cforms2'),
        'UA' => __('Ukraine', 'cforms2'),
        'UG' => __('Uganda', 'cforms2'),
        'UM' => __('United States Minor Outlying Islands', 'cforms2'),
        'US' => __('United States', 'cforms2'),
        'UY' => __('Uruguay', 'cforms2'),
        'UZ' => __('Uzbekistan', 'cforms2'),
        'VA' => __('Vatican', 'cforms2'),
        'VC' => __('Saint Vincent and the Grenadines', 'cforms2'),
        'VE' => __('Venezuela', 'cforms2'),
        'VG' => __('British Virgin Islands', 'cforms2'),
        'VI' => __('U.S. Virgin Islands', 'cforms2'),
        'VN' => __('Vietnam', 'cforms2'),
        'VU' => __('Vanuatu', 'cforms2'),
        'WF' => __('Wallis and Futuna', 'cforms2'),
        'WS' => __('Samoa', 'cforms2'),
        'YE' => __('Yemen', 'cforms2'),
        'YT' => __('Mayotte', 'cforms2'),
        'ZA' => __('South Africa', 'cforms2'),
        'ZM' => __('Zambia', 'cforms2'),
        'ZW' => __('Zimbabwe', 'cforms2')
    );
}

/**
 * Get JavaScript localization data
 */
function cforms2_get_js_i18n_data() {
    return array(
        'labels' => cforms2_get_field_labels(),
        'validation' => cforms2_get_validation_messages(),
        'locale_config' => wp_cache_get('cforms2_locale_config', 'cforms2'),
        'datetime_formats' => wp_cache_get('cforms2_datetime_formats', 'cforms2'),
        'countries' => cforms2_get_countries()
    );
}

/**
 * Enqueue localized scripts
 */
function cforms2_enqueue_i18n_scripts() {
    wp_localize_script('cforms2-script', 'cforms2_i18n', cforms2_get_js_i18n_data());
}

// Initialize i18n on plugin load
add_action('plugins_loaded', 'cforms2_init_i18n');
add_action('wp_enqueue_scripts', 'cforms2_enqueue_i18n_scripts');
