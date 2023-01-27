<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequirementRequest;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    public function getRequirements(Request $request)
    {
        return Requirement::where('user_id', $request->user()->id)
            ->paginate(5);
    }

    public function uploadRequirement(RequirementRequest $request)
    {
        return Requirement::create([
            'user_id'       => $request->user()->id,
            'type'          => $request->requirement_type,
            'file_name'     => $request->file('file')->getClientOriginalName(),
            'file'          => $request->file('file')->store('requirements'),
        ]);
    }

    public function deleteRequirement($id)
    {
        return Requirement::where('id', $id)->delete();
    }

    public function downloadFile($id)
    {
        return Storage::download(Requirement::where('id', $id)->first()->file);
    }
}