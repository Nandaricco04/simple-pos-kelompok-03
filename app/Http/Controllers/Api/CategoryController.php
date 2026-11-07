<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['sometimes', 'string'],
            'sort' => ['sometimes', 'in:name,-name,id,-id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $sort = $request->input('sort', 'name');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

        $categories = Category::query()
            ->when(
                $request->filled('q'),
                fn ($query) => $query->where(
                    'name',
                    'like',
                    '%' . $request->input('q') . '%'
                )
            )
            ->orderBy(ltrim($sort, '-'), $direction)
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return CategoryResource::collection($categories);
    }
}
