<?php
/**
 * Class LP_Stats_Widget
 * Tạo Dashboard Widget trong Admin
 */
class LP_Stats_Widget {
    
    private $stats;
    
    public function __construct($stats) {
        $this->stats = $stats;
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);
    }
    
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'lp_stats_widget',
            '📊 Thống kê học tập (LearnPress)',
            [$this, 'render_widget'],
            null,
            null,
            'normal',
            'high'
        );
    }
    
    public function render_widget() {
        $stats = $this->stats->get_all_stats();
        $popular = $this->stats->get_popular_courses(3);
        ?>
        <style>
            .lp-stats-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
                margin-bottom: 20px;
            }
            .lp-stats-card {
                background: #f8f9fa;
                border: 1px solid #e9ecef;
                border-radius: 8px;
                padding: 15px;
                text-align: center;
            }
            .lp-stats-card h3 {
                font-size: 14px;
                color: #6c757d;
                margin: 0 0 10px 0;
            }
            .lp-stats-number {
                font-size: 28px;
                font-weight: bold;
                color: #0073aa;
            }
            .lp-stats-label {
                font-size: 12px;
                color: #6c757d;
                margin-top: 5px;
            }
            .lp-stats-progress {
                margin-top: 10px;
                background: #e9ecef;
                border-radius: 10px;
                overflow: hidden;
            }
            .lp-stats-progress-bar {
                background: #0073aa;
                height: 8px;
                width: <?php echo $stats['completion_rate']; ?>%;
                border-radius: 10px;
            }
            .lp-popular-list {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .lp-popular-list li {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #e9ecef;
            }
            .lp-popular-list li:last-child {
                border-bottom: none;
            }
            .lp-popular-title {
                flex: 1;
            }
            .lp-popular-count {
                background: #0073aa;
                color: white;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 12px;
            }
        </style>
        
        <div class="lp-stats-grid">
            <div class="lp-stats-card">
                <h3>📚 Tổng số khóa học</h3>
                <div class="lp-stats-number"><?php echo $stats['total_courses']; ?></div>
                <div class="lp-stats-label">khóa học đang mở</div>
            </div>
            <div class="lp-stats-card">
                <h3>👨‍🎓 Tổng số học viên</h3>
                <div class="lp-stats-number"><?php echo $stats['total_students']; ?></div>
                <div class="lp-stats-label">học viên đã đăng ký</div>
            </div>
            <div class="lp-stats-card">
                <h3>✅ Khóa học hoàn thành</h3>
                <div class="lp-stats-number"><?php echo $stats['completed_courses']; ?></div>
                <div class="lp-stats-label">khóa học đã hoàn thành</div>
                <div class="lp-stats-progress">
                    <div class="lp-stats-progress-bar"></div>
                </div>
                <div class="lp-stats-label">Tỷ lệ: <?php echo $stats['completion_rate']; ?>%</div>
            </div>
        </div>
        
        <?php if (!empty($popular)) : ?>
        <div class="lp-stats-popular">
            <h4>🏆 Khóa học phổ biến nhất</h4>
            <ul class="lp-popular-list">
                <?php foreach ($popular as $course) : ?>
                <li>
                    <span class="lp-popular-title">
                        <a href="<?php echo get_edit_post_link($course['id']); ?>">
                            <?php echo esc_html($course['title']); ?>
                        </a>
                    </span>
                    <span class="lp-popular-count">
                        <?php echo $course['students']; ?> học viên
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 15px; text-align: right;">
            <small>
                <a href="<?php echo admin_url('admin.php?page=learnpress-statistics'); ?>">
                    Xem chi tiết →
                </a>
            </small>
        </div>
        <?php
    }
}