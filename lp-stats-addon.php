<?php
/**
 * Plugin Name: LearnPress Stats Dashboard
 * Plugin URI: https://yourwebsite.com/lp-stats-addon
 * Description: Hiển thị thống kê học tập từ LearnPress: tổng số khóa học, học viên, khóa học hoàn thành
 * Version: 1.0.0
 * Author: Tên Của Bạn
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * Text Domain: lp-stats-addon
 * Requires Plugins: learnpress
 */

// Ngăn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Kiểm tra LearnPress đã được kích hoạt
function lp_stats_check_dependencies() {
    if (!class_exists('LearnPress')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p><strong>LearnPress Stats Dashboard</strong> yêu cầu plugin <strong>LearnPress</strong> phải được cài đặt và kích hoạt.</p></div>';
        });
        return false;
    }
    return true;
}

if (!lp_stats_check_dependencies()) {
    return;
}

// Định nghĩa hằng số
define('LP_STATS_VERSION', '1.0.0');
define('LP_STATS_PATH', plugin_dir_path(__FILE__));
define('LP_STATS_URL', plugin_dir_url(__FILE__));

// Load các file
require_once LP_STATS_PATH . 'includes/class-stats.php';
require_once LP_STATS_PATH . 'includes/class-widget.php';
require_once LP_STATS_PATH . 'includes/class-shortcode.php';

// Khởi tạo plugin
class LP_Stats_Addon {
    
    private static $instance = null;
    private $stats;
    private $widget;
    private $shortcode;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->stats = new LP_Stats_Data();
        $this->widget = new LP_Stats_Widget($this->stats);
        $this->shortcode = new LP_Stats_Shortcode($this->stats);
        
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('lp-stats-frontend', LP_STATS_URL . 'assets/css/frontend.css', [], LP_STATS_VERSION);
        wp_enqueue_script('lp-stats-frontend', LP_STATS_URL . 'assets/js/frontend.js', ['jquery'], LP_STATS_VERSION, true);
    }
    
    public function enqueue_admin_assets() {
        wp_enqueue_style('lp-stats-admin', LP_STATS_URL . 'assets/css/admin.css', [], LP_STATS_VERSION);
        wp_enqueue_script('lp-stats-admin', LP_STATS_URL . 'assets/js/admin.js', ['jquery'], LP_STATS_VERSION, true);
        
        wp_localize_script('lp-stats-admin', 'lp_stats_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lp_stats_nonce')
        ]);
    }
}

// Khởi chạy plugin
add_action('plugins_loaded', ['LP_Stats_Addon', 'get_instance']);