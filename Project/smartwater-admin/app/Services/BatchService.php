<?php

namespace App\Services;

use App\Models\Batch;

class BatchService
{
    public function getAllBatches()
    {
        return Batch::with('supplier')->get();
    }

    public function getBatchById($id)
    {
        return Batch::with('supplier')->findOrFail($id);
    }

    public function createBatch(array $data)
    {
        return Batch::create([
            'batch_code' => $data['batch_code'],
            'supplier_id' => $data['supplier_id'],
            'import_date' => $data['import_date'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
        ]);
    }

    public function updateBatch($id, array $data)
    {
        $batch = Batch::findOrFail($id);
        $batch->update([
            'batch_code' => $data['batch_code'],
            'supplier_id' => $data['supplier_id'],
            'import_date' => $data['import_date'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
        ]);
        return $batch;
    }

    public function deleteBatch($id)
    {
        $batch = Batch::findOrFail($id);
        $batch->delete();
        return true;
    }
}
