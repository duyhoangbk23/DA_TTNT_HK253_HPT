@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')
@section('page-subtitle', 'Danh mục các dòng máy lọc nước và phụ kiện đang kinh doanh.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Quản lý sản phẩm</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
        <i class="bi bi-plus-lg me-1"></i> Thêm sản phẩm
    </button>
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
                        <th>Thao tác</th>
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
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditProduct"
                                        data-id="{{ $p->id }}"
                                        data-code="{{ $p->product_code }}"
                                        data-name="{{ $p->product_name }}"
                                        data-category="{{ $p->category_id }}"
                                        data-model="{{ $p->model }}"
                                        data-capacity="{{ $p->capacity }}"
                                        data-unit="{{ $p->unit }}"
                                        data-price="{{ $p->price }}"
                                        data-status="{{ $p->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('products.destroy', $p->id) }}"
                                      style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>

    <!-- Modal Thêm sản phẩm -->
    <div class="modal fade" id="modalAddProduct" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   name="code" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                    name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('model') is-invalid @enderror"
                                   name="model" value="{{ old('model') }}" required>
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Công suất <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('capacity') is-invalid @enderror"
                                   name="capacity" value="{{ old('capacity') }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đơn vị</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                   name="unit" value="{{ old('unit', 'Chiếc') }}">
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                   name="price" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa sản phẩm -->
    <div class="modal fade" id="modalEditProduct" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditProduct">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   name="code" id="editProductCode" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" id="editProductName" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                    name="category_id" id="editProductCategory" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('model') is-invalid @enderror"
                                   name="model" id="editProductModel" value="{{ old('model') }}" required>
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Công suất <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('capacity') is-invalid @enderror"
                                   name="capacity" id="editProductCapacity" value="{{ old('capacity') }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đơn vị</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                   name="unit" id="editProductUnit" value="{{ old('unit') }}">
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                   name="price" id="editProductPrice" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    name="status" id="editProductStatus" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="active">Hoạt động</option>
                                <option value="maintenance">Bảo trì</option>
                                <option value="inactive">Ngưng hoạt động</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = document.getElementById('modalEditProduct');
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const code = button.getAttribute('data-code');
                const name = button.getAttribute('data-name');
                const category = button.getAttribute('data-category');
                const model = button.getAttribute('data-model');
                const capacity = button.getAttribute('data-capacity');
                const unit = button.getAttribute('data-unit');
                const price = button.getAttribute('data-price');
                const status = button.getAttribute('data-status');

                document.getElementById('editProductCode').value = code;
                document.getElementById('editProductName').value = name;
                document.getElementById('editProductCategory').value = category;
                document.getElementById('editProductModel').value = model;
                document.getElementById('editProductCapacity').value = capacity;
                document.getElementById('editProductUnit').value = unit;
                document.getElementById('editProductPrice').value = price;
                document.getElementById('editProductStatus').value = status;

                const form = document.getElementById('formEditProduct');
                form.action = `/products/${id}`;
            });
        });
    </script>
@endsection
