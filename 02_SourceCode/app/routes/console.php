<?php

use Illuminate\Support\Facades\Schedule;

// Lịch chạy nền: giải phóng giữ chỗ hết hạn mỗi phút, sao lưu CSDL hằng ngày lúc 02:00.
Schedule::command('app:expire-holds')->everyMinute();
Schedule::command('app:backup-db')->dailyAt('02:00');
