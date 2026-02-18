<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Http\Requests\ContestRequest;
use Illuminate\Http\Request;

class ContestController extends Controller
{
    public function index()
    {
        $contests = Contest::latest()->paginate(10);
        return response()->json($contests);
    }

    public function store(ContestRequest $request)
    {
        $contest = Contest::create($request->validated());
        return response()->json($contest, 201);
    }

    public function show(Contest $contest)
    {
        return response()->json($contest->load('submissions'));
    }

    public function update(ContestRequest $request, Contest $contest)
    {
        $contest->update($request->validated());
        return response()->json($contest);
    }

    public function destroy(Contest $contest)
    {
        $contest->delete();
        return response()->json(null, 204);
    }

    public function active()
    {
        $contests = Contest::active()->get();
        return response()->json($contests);
    }
}