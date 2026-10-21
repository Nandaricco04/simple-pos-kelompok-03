<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function model(array $row)
    {
        $validator = Validator::make($row, [
            'name' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        // Lewati row jika validasi gagal
        if ($validator->fails()) {
            return null;
        }

        $data = $validator->validated();

        // Gunakan updateOrCreate agar tetap idempotent (aman diulang)
        return Product::updateOrCreate(
            ['name' => $data['name'], 'category_id' => $data['category_id']],
            ['price' => $data['price']],
        );
    }

    public function chunkSize(): int
    {
        return 500;
    }
}