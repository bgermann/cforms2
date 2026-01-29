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
 * Enhanced Security Functions for cforms2
 * Provides improved input validation and sanitization
 */

/**
 * Enhanced input sanitization with context-aware cleaning
 */
function cforms2_sanitize_input($input, $context = 'text') {
    if (is_array($input)) {
        return array_map(function($item) use ($context) {
            return cforms2_sanitize_input($item, $context);
        }, $input);
    }
    
    switch ($context) {
        case 'email':
            return sanitize_email($input);
            
        case 'url':
            return esc_url_raw($input);
            
        case 'filename':
            // Secure filename sanitization
            $input = sanitize_file_name($input);
            // Additional security: remove potential dangerous extensions
            $dangerous_extensions = array('php', 'js', 'html', 'htm', 'exe', 'bat', 'cmd');
            $extension = pathinfo($input, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), $dangerous_extensions)) {
                $input = pathinfo($input, PATHINFO_FILENAME) . '.txt';
            }
            return $input;
            
        case 'textarea':
            return wp_kses_post($input);
            
        case 'html':
            return wp_kses_post($input);
            
        case 'numeric':
            return is_numeric($input) ? intval($input) : 0;
            
        case 'float':
            return is_numeric($input) ? floatval($input) : 0.0;
            
        case 'boolean':
            return (bool) $input;
            
        case 'text':
        default:
            return sanitize_text_field($input);
    }
}

/**
 * Validate file upload security
 */
function cforms2_validate_file_upload($file) {
    $errors = array();
    
    // Check if file was actually uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $errors[] = __('Invalid file upload.', 'cforms2');
        return $errors;
    }
    
    // Check file size
    $max_size = wp_max_upload_size();
    if ($file['size'] > $max_size) {
        $errors[] = sprintf(__('File size exceeds maximum allowed size of %s.', 'cforms2'), size_format($max_size));
    }
    
    // Validate file extension
    $allowed_extensions = get_allowed_mime_types();
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    $extension_allowed = false;
    foreach ($allowed_extensions as $ext => $mime) {
        $extensions = explode('|', $ext);
        if (in_array($file_extension, $extensions)) {
            $extension_allowed = true;
            break;
        }
    }
    
    if (!$extension_allowed) {
        $errors[] = sprintf(__('File type "%s" is not allowed.', 'cforms2'), $file_extension);
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_extensions)) {
        $errors[] = __('File MIME type is not allowed.', 'cforms2');
    }
    
    // Additional security checks
    $file_content = file_get_contents($file['tmp_name'], false, null, 0, 1024);
    
    // Check for PHP tags
    if (strpos($file_content, '<?php') !== false || strpos($file_content, '<?=') !== false) {
        $errors[] = __('File contains potentially dangerous content.', 'cforms2');
    }
    
    // Check for script tags
    if (stripos($file_content, '<script') !== false) {
        $errors[] = __('File contains potentially dangerous script content.', 'cforms2');
    }
    
    return $errors;
}

/**
 * Enhanced nonce verification with context
 */
function cforms2_verify_nonce($nonce, $action, $context = 'form') {
    if (!wp_verify_nonce($nonce, $action)) {
        cforms2_log_security_event('nonce_verification_failed', array(
            'action' => $action,
            'context' => $context,
            'ip' => cforms2_get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ));
        return false;
    }
    return true;
}

/**
 * Get client IP address securely
 */
function cforms2_get_client_ip() {
    $ip_keys = array(
        'HTTP_CF_CONNECTING_IP',     // Cloudflare
        'HTTP_CLIENT_IP',            // Proxy
        'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
        'HTTP_X_FORWARDED',          // Proxy
        'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
        'HTTP_FORWARDED_FOR',        // Proxy
        'HTTP_FORWARDED',            // Proxy
        'REMOTE_ADDR'                // Standard
    );
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            
            // Validate IP address
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Log security events for monitoring
 */
function cforms2_log_security_event($event_type, $data = array()) {
    if (!WP_DEBUG_CFORMS2) {
        return;
    }
    
    $log_entry = array(
        'timestamp' => current_time('mysql'),
        'event_type' => $event_type,
        'ip' => cforms2_get_client_ip(),
        'user_id' => get_current_user_id(),
        'data' => $data
    );
    
    error_log('cforms2_security: ' . json_encode($log_entry));
}

/**
 * Rate limiting for form submissions
 */
function cforms2_check_rate_limit($form_id, $max_attempts = 5, $time_window = 300) {
    $ip = cforms2_get_client_ip();
    $cache_key = "cforms2_rate_limit_{$form_id}_{$ip}";
    
    $attempts = get_transient($cache_key);
    
    if ($attempts === false) {
        // First attempt
        set_transient($cache_key, 1, $time_window);
        return true;
    }
    
    if ($attempts >= $max_attempts) {
        cforms2_log_security_event('rate_limit_exceeded', array(
            'form_id' => $form_id,
            'ip' => $ip,
            'attempts' => $attempts
        ));
        return false;
    }
    
    // Increment attempts
    set_transient($cache_key, $attempts + 1, $time_window);
    return true;
}

/**
 * Enhanced CSRF protection
 */
function cforms2_generate_csrf_token($form_id) {
    $token = wp_create_nonce("cforms2_submit_{$form_id}");
    
    // Store token in session for additional verification
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION["cforms2_csrf_{$form_id}"] = $token;
    }
    
    return $token;
}

/**
 * Verify CSRF token
 */
function cforms2_verify_csrf_token($token, $form_id) {
    // Verify WordPress nonce
    if (!wp_verify_nonce($token, "cforms2_submit_{$form_id}")) {
        return false;
    }
    
    // Additional session-based verification
    if (session_status() === PHP_SESSION_ACTIVE) {
        $session_token = $_SESSION["cforms2_csrf_{$form_id}"] ?? '';
        if ($session_token !== $token) {
            cforms2_log_security_event('csrf_session_mismatch', array(
                'form_id' => $form_id,
                'provided_token' => $token,
                'session_token' => $session_token
            ));
            return false;
        }
        
        // Clear used token
        unset($_SESSION["cforms2_csrf_{$form_id}"]);
    }
    
    return true;
}

/**
 * Sanitize form field data based on field type
 */
function cforms2_sanitize_field_data($value, $field_type) {
    switch ($field_type) {
        case 'email':
        case 'html5email':
            return cforms2_sanitize_input($value, 'email');
            
        case 'url':
        case 'html5url':
            return cforms2_sanitize_input($value, 'url');
            
        case 'textarea':
            return cforms2_sanitize_input($value, 'textarea');
            
        case 'html5number':
        case 'html5range':
            return cforms2_sanitize_input($value, 'numeric');
            
        case 'upload':
            return cforms2_sanitize_input($value, 'filename');
            
        case 'checkbox':
        case 'checkboxgroup':
            return cforms2_sanitize_input($value, 'boolean');
            
        default:
            return cforms2_sanitize_input($value, 'text');
    }
}
