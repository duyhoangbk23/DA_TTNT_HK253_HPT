@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')
@section('page-subtitle', 'Danh mục các dòng máy lọc nước và phụ kiện đang kinh doanh.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Quản lý sản phẩm</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Thêm sản phẩm</button>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm sản phẩm..." data-dt-search="#tblProducts">
                <select class="form-select form-select-sm" style="width: 180px;"
                        data-dt-filter="#tblProducts" data-dt-column="2">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm" style="width: 160px;"
                        data-dt-filter="#tblProducts" data-dt-column="5">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Hoạt động">Hoạt động</option>
                    <option value="Bảo trì">Bảo trì</option>
                    <option value="Ngưng hoạt động">Ngưng hoạt động</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblProducts" data-datatable data-no-sort="0,5">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Model</th>
                        <th>Công suất</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $p)
                        <tr>
                            <td><span class="table-thumb"><i class="bi bi-droplet-half"></i></span></td>
                            <td>
                                <div class="cell-title">{{ $p->product_name }}</div>
                                <div class="cell-sub">{{ $p->product_code }}</div>
                            </td>
                            <td>{{ $p->category->name ?? '-' }}</td>
                            <td>{{ $p->model }}</td>
                            <td>{{ $p->capacity }}</td>
                            <td>
                                <x-status-badge :status="$p->status" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection
