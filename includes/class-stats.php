<?php
/**
 * Class LP_Stats_Data
 * Xử lý truy vấn dữ liệu thống kê từ LearnPress
 */
class LP_Stats_Data {
    
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Lấy tổng số khóa học (Courses)
     */
    public function get_total_courses() {
        $args = [
            'post_type' => 'lp_course',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ];
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    /**
     * Lấy tổng số học viên đã đăng ký (Students)
     * Học viên là users có vai trò 'lp_student'
     */
    public function get_total_students() {
        $args = [
            'role' => 'lp_student',
            'fields' => 'ID',
            'number' => -1
        ];
        
        $users = get_users($args);
        return count($users);
    }
    
    /**
     * Lấy tổng số khóa học đã được hoàn thành
     * (Status: completed)
     */
    public function get_completed_courses() {
        global $wpdb;
        
        // Bảng lưu trữ khóa học của học viên trong LearnPress
        $table = $wpdb->prefix . 'learnpress_user_items';
        
        $query = $wpdb->prepare("
            SELECT COUNT(DISTINCT item_id) 
            FROM {$table} 
            WHERE item_type = 'lp_course' 
            AND status = %s
        ", 'completed');
        
        $result = $wpdb->get_var($query);
        return $result ? (int)$result : 0;
    }
    
    /**
     * Lấy chi tiết thống kê đầy đủ
     */
    public function get_all_stats() {
        return [
            'total_courses' => $this->get_total_courses(),
            'total_students' => $this->get_total_students(),
            'completed_courses' => $this->get_completed_courses(),
            'completion_rate' => $this->get_completion_rate()
        ];
    }
    
    /**
     * Tính tỷ lệ hoàn thành khóa học
     */
    public function get_completion_rate() {
        $total_courses = $this->get_total_courses();
        $completed = $this->get_completed_courses();
        
        if ($total_courses == 0) {
            return 0;
        }
        
        return round(($completed / $total_courses) * 100, 2);
    }
    
    /**
     * Lấy danh sách khóa học phổ biến (nhiều học viên nhất)
     */
    public function get_popular_courses($limit = 5) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'learnpress_user_items';
        
        $query = $wpdb->prepare("
            SELECT item_id as course_id, COUNT(*) as student_count
            FROM {$table}
            WHERE item_type = 'lp_course'
            GROUP BY item_id
            ORDER BY student_count DESC
            LIMIT %d
        ", $limit);
        
        $results = $wpdb->get_results($query);
        
        $courses = [];
        foreach ($results as $row) {
            $course = get_post($row->course_id);
            if ($course) {
                $courses[] = [
                    'id' => $course->ID,
                    'title' => $course->post_title,
                    'students' => $row->student_count
                ];
            }
        }
        
        return $courses;
    }
}