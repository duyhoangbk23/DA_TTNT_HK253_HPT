<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        // Activity Log chỉ đọc bản ghi đã lưu trong database; eager load user.employee tránh truy vấn lặp khi hiển thị người thực hiện.
        $activities = ActivityLog::query()
            ->with(['user.employee'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $query) use ($search): void {
                            $query->where('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhereHas('employee', fn (Builder $query) => $query->where('full_name', 'like', "%{$search}%"));
                        });
                });
            })
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('activities.index', [
            'activities' => $activities,
            'search' => $search,
        ]);
    }
}
