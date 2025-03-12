<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiseaseController\storeRequest;
use App\Http\Requests\DiseaseController\updateRequest;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'diseases' => Disease::all(),
        ]);
    }

    public function store(storeRequest $request)
    {
        $request->validate([
            'name' => 'required|string|unique:diseases,name',
            'description' => 'nullable|string',
        ]);

        $disease = Disease::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'بیماری با موفقیت اضافه شد',
            'disease' => $disease,
        ], 201);
    }

    public function show(Disease $disease)
    {
        return response()->json([
            'success' => true,
            'disease' => $disease,
        ]);
    }

    public function update(updateRequest $request, Disease $disease)
    {


        $disease->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'بیماری بروزرسانی شد',
            'disease' => $disease,
        ]);
    }

    public function destroy(Disease $disease)
    {
        $disease->delete();

        return response()->json([
            'success' => true,
            'message' => 'بیماری حذف شد',
        ]);
    }
}
