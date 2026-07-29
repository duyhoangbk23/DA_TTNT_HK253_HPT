@extends('layouts.app')

@section('title', 'Lịch sử hoạt động')
@section('page-title', 'Lịch sử hoạt động')
@section('page-subtitle', 'Nhật ký thao tác của người dùng trong hệ thống.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Lịch sử hoạt động</li>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <form method="GET" action="{{ route('activities.index') }}" class="d-flex gap-2">
                <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm"
                       style="width: 240px;" placeholder="Tìm hoạt động...">
                <button type="submit" class="btn btn-sm btn-primary">Tìm</button>
            </form>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblActivities">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Người thực hiện</th>
                        <th>Hành động</th>
                        <th>Mô tả</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        @php
                            $displayName = $activity->user?->employee?->full_name
                                ?? $activity->user?->username
                                ?? $activity->user?->email
                                ?? 'Hệ thống';
                            $avatarPath = $activity->user?->employee?->avatar_path
                                ?? $activity->user?->avatar_path;
                            $icon = match ($activity->module) {
                                'Auth' => 'bi-box-arrow-in-right',
                                'Hợp đồng' => 'bi-file-earmark-plus',
                                'Khách hàng' => 'bi-person-gear',
                                'Bảo trì' => 'bi-check2-circle',
                                'Kho' => 'bi-box-seam',
                                'Thiết bị' => 'bi-cpu',
                                'Báo cáo' => 'bi-file-earmark-bar-graph',
                                'Nhân viên' => 'bi-person-plus',
                                default => 'bi-clock-history',
                            };
                        @endphp
                        <tr>
                            <td class="cell-sub">{{ $activity->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($avatarPath && Storage::disk('public')->exists($avatarPath))
                                        <img src="{{ Storage::url($avatarPath) }}" class="table-avatar" alt="{{ $displayName }}">
                                    @else
                                        <span class="table-avatar d-inline-flex align-items-center justify-content-center bg-light text-secondary">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    @endif
                                    <span class="cell-title">{{ $displayName }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="d-inline-flex align-items-center gap-2">
                                    <i class="bi {{ $icon }} text-primary"></i> {{ $activity->action }}
                                </span>
                            </td>
                            <td class="cell-sub">{{ $activity->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted-2 py-4">Chưa có lịch sử hoạt động.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $activities->links() }}</div>
    </x-panel>
@endsection
