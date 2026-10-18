<?php

return [
    'auth' => [
        'logged_in' => 'Đăng nhập thành công. Chào mừng :name!',
        'logged_out' => 'Bạn đã đăng xuất.',
        'registered' => 'Tạo tài khoản thành công.',
        'inactive' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.',
    ],
    'profile' => [
        'updated' => 'Đã cập nhật hồ sơ.',
        'password_updated' => 'Đã đổi mật khẩu.',
    ],
    'favorites' => [
        'added' => 'Đã thêm vào bộ sưu tập yêu thích.',
        'removed' => 'Đã bỏ khỏi bộ sưu tập yêu thích.',
    ],
    'contributions' => [
        'submitted' => 'Cảm ơn bạn! Đóng góp đã được gửi và đang chờ kiểm duyệt.',
        'reviewed' => 'Đã cập nhật kết quả kiểm duyệt.',
    ],
    'booking' => [
        'held' => 'Đã giữ chỗ cho bạn trong :minutes phút. Vui lòng thanh toán để xác nhận.',
        'only_left' => 'Chỉ còn :count chỗ.',
        'slot_unavailable' => 'Khung giờ này không còn mở đặt chỗ.',
        'invalid_transition' => 'Không thể chuyển trạng thái đặt lịch từ ":from" sang ":to".',
        'cannot_cancel' => 'Đặt lịch này không thể hủy (đã quá hạn hủy hoặc trạng thái không cho phép).',
        'cannot_pay' => 'Đặt lịch này không thể thanh toán (đã hết hạn giữ chỗ hoặc đã xử lý).',
        'cancelled' => 'Đã hủy đặt lịch.',
        'refunded' => 'Đã hủy đặt lịch và hoàn tiền (mô phỏng).',
        'status_updated' => 'Đã cập nhật trạng thái đặt lịch.',
    ],
    'payment' => [
        'success' => 'Thanh toán thành công. Đặt lịch đã được xác nhận.',
        'failed' => 'Thanh toán thất bại. Bạn có thể thử lại trong thời gian giữ chỗ.',
        'cancelled' => 'Bạn đã hủy thanh toán.',
        'expired' => 'Giữ chỗ đã hết hạn nên giao dịch không được ghi nhận.',
    ],
    'admin' => [
        'created' => 'Đã tạo mới.',
        'updated' => 'Đã lưu thay đổi.',
        'deleted' => 'Đã xóa.',
        'slot_created' => 'Đã thêm khung giờ.',
        'slot_deleted' => 'Đã xóa khung giờ.',
        'slot_has_bookings' => 'Không thể xóa khung giờ đã có đặt lịch.',
        'media_uploaded' => 'Đã tải lên tệp đa phương tiện.',
        'user_updated' => 'Đã cập nhật người dùng.',
        'cannot_demote_self' => 'Bạn không thể tự hạ quyền hoặc khóa chính mình.',
        'quality_done' => 'Đã chạy kiểm tra chất lượng dữ liệu.',
        'quality_failed' => 'Không kết nối được dịch vụ phân tích. Vui lòng thử lại sau.',
    ],
    'search' => [
        'fallback_notice' => 'Đang dùng tìm kiếm cơ bản vì dịch vụ phân tích tạm thời không khả dụng.',
    ],
    'ical' => [
        'description' => 'Còn :available/:capacity chỗ. Hướng dẫn viên: :guide',
    ],
    'locales' => [
        'vi' => 'Tiếng Việt',
        'en' => 'English',
    ],
];
