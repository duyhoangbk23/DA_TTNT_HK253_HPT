<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\BatchService;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;

class BatchController extends Controller
{
    protected $batchService;

    public function __construct(BatchService $batchService)
    {
        $this->batchService = $batchService;
    }

    public function index()
    {
        $batches = $this->batchService->getAllBatches();
        $suppliers = Supplier::all();
        return view('batch.index', [
            'batches' => $batches,
            'suppliers' => $suppliers,
        ]);
    }

    public function show(int $id)
    {
        $batch = $this->batchService->getBatchById($id);
        return view('batch.show', ['batch' => $batch]);
    }

    public function store(StoreBatchRequest $request)
    {
        $batch = $this->batchService->createBatch($request->validated());
        return redirect()->route('batches.index')->with('success', 'Lô hàng đã được tạo');
    }

    public function update(UpdateBatchRequest $request, $id)
    {
        $batch = $this->batchService->updateBatch($id, $request->validated());
        return redirect()->route('batches.index')->with('success', 'Lô hàng đã được cập nhật');
    }

    public function destroy($id)
    {
        $this->batchService->deleteBatch($id);
        return redirect()->route('batches.index')->with('success', 'Lô hàng đã được xóa');
    }
}
