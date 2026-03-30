<?php
/**
 * Class LP_Stats_Shortcode
 * Đăng ký shortcode [lp_total_stats] để hiển thị thống kê ra frontend
 */
class LP_Stats_Shortcode {
    
    private $stats;
    
    public function __construct($stats) {
        $this->stats = $stats;
        add_shortcode('lp_total_stats', [$this, 'render_shortcode']);
    }
    
    public function render_shortcode($atts) {
        $atts = shortcode_atts([
            'show_completion_rate' => 'yes',
            'show_popular' => 'no',
            'background' => '#ffffff',
            'text_color' => '#333333'
        ], $atts);
        
        $stats = $this->stats->get_all_stats();
        
        ob_start();
        ?>
        <div class="lp-stats-frontend" style="background: <?php echo esc_attr($atts['background']); ?>; color: <?php echo esc_attr($atts['text_color']); ?>; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">📊 Thống kê khóa học</h3>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; text-align: center;">
                <div>
                    <div style="font-size: 32px; font-weight: bold; color: #0073aa;">
                        <?php echo $stats['total_courses']; ?>
                    </div>
                    <div>Khóa học</div>
                </div>
                <div>
                    <div style="font-size: 32px; font-weight: bold; color: #0073aa;">
                        <?php echo $stats['total_students']; ?>
                    </div>
                    <div>Học viên</div>
                </div>
                <div>
                    <div style="font-size: 32px; font-weight: bold; color: #0073aa;">
                        <?php echo $stats['completed_courses']; ?>
                    </div>
                    <div>Khóa hoàn thành</div>
                </div>
            </div>
            
            <?php if ($atts['show_completion_rate'] === 'yes') : ?>
            <div style="margin-top: 15px; background: #f0f0f0; border-radius: 10px; overflow: hidden;">
                <div style="background: #0073aa; height: 20px; width: <?php echo $stats['completion_rate']; ?>%; text-align: center; color: white; line-height: 20px; font-size: 12px;">
                    <?php echo $stats['completion_rate']; ?>%
                </div>
            </div>
            <div style="text-align: center; margin-top: 5px; font-size: 12px;">
                Tỷ lệ hoàn thành khóa học
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_popular'] === 'yes') : 
                $popular = $this->stats->get_popular_courses(3);
                if (!empty($popular)) : ?>
                <div style="margin-top: 20px;">
                    <h4>🏆 Khóa học phổ biến</h4>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($popular as $course) : ?>
                        <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                            <span><?php echo esc_html($course['title']); ?></span>
                            <span style="background: #0073aa; color: white; padding: 2px 8px; border-radius: 20px; font-size: 12px;">
                                <?php echo $course['students']; ?> học viên
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <div style="margin-top: 15px; font-size: 12px; text-align: center; color: #666;">
                Cập nhật: <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>
        
        <style>
            .lp-stats-frontend {
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            @media (max-width: 768px) {
                .lp-stats-frontend > div:first-child {
                    grid-template-columns: repeat(1, 1fr) !important;
                    gap: 10px;
                }
            }
        </style>
        <?php
        return ob_get_clean();
    }
}