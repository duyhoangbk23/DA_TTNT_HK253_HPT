<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class BatchController extends Controller
{
    public function index()
    {
        return view('batch.index', [
            'batches' => MockData::batches(),
        ]);
    }

    public function show(int $id)
    {
        $batch = MockData::batches()->firstWhere('id', $id);
        abort_if(! $batch, 404);

        return view('batch.show', [
            'batch'   => $batch,
            'details' => MockData::batchDetails($id),
        ]);
    }
}
