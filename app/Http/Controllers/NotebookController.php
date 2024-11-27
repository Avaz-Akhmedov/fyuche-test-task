<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateNotebookRequest;
use App\Http\Resources\NotebookResource;
use App\Models\Notebook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class NotebookController extends Controller
{
    public function index(): ResourceCollection
    {
        $notebooks = Notebook::query()->latest()->paginate(20);
        return NotebookResource::collection($notebooks);
    }

    public function show(Notebook $notebook): NotebookResource
    {
        return NotebookResource::make($notebook);
    }

    public function store(StoreUpdateNotebookRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $data['image'] = $imagePath;
        }

        Notebook::query()->create($data);
        return response()->json([
            'success' => true,
        ], 201);
    }

    public function update(StoreUpdateNotebookRequest $request, Notebook $notebook): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($notebook->image) {
                Storage::disk('public')->delete($notebook->image);
            }
            $imagePath = $request->file('image')->store('images', 'public');
            $data['image'] = $imagePath;
        }
        $notebook->update($data);
        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(Notebook $notebook): JsonResponse
    {
        $notebook->delete();

        return response()->json([
            'success' => true,
        ], 204);
    }
}
