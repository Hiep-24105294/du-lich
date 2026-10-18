<?php

namespace App\Services;

use App\Models\PageView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/** Ghi lượt xem: mỗi phiên chỉ đếm 1 lần cho mỗi đối tượng. */
class PageViewRecorder
{
    public function record(Model $subject, Request $request): bool
    {
        $key = 'viewed.'.$subject->getMorphClass().'.'.$subject->getKey();
        if (! $request->hasSession() || $request->session()->has($key)) {
            return false;
        }

        try {
            PageView::create([
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
                'user_id' => $request->user()?->id,
                'locale' => app()->getLocale(),
                'viewed_on' => now()->toDateString(),
                'created_at' => now(),
            ]);
            // Tăng trực tiếp để không đổi updated_at của bản ghi nội dung
            DB::table($subject->getTable())->where('id', $subject->getKey())->increment('view_count');
            $subject->view_count = (int) $subject->view_count + 1;
            $request->session()->put($key, true);

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
