<?php

namespace App\Http\Controllers\Api\Intern;

use App\Http\Controllers\Controller;
use App\Http\Requests\DTRFileRequest;
use App\Models\DTRFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DTRFileController extends Controller
{

    public function getDTRFiles(Request $request)
    {
        return response()->json(DTRFile::where('user_id', $request->user()->id)->get());
    }

    public function uploadDTRFile(DTRFileRequest $request)
    {
        return DTRFile::create([
            'user_id'       => $request->user()->id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'file_name'     => $request->file('file')->getClientOriginalName(),
            'file'          => $request->file('file')->store('dtr_files'),
        ]);
    }

    public function updateDTRFile($id, DTRFileRequest $request) 
    {
        return response()->json(DTRFile::whereId($id)->update($request->validated()));
    }

    public function deleteDTRFile($id, Request $request)
    {
        $dtrFile = DTRFile::whereId($id)->first();

        if(Storage::exists($dtrFile->file)) {
            Storage::delete($dtrFile->file);
        }
        
        return response()->json($dtrFile->delete());
    }
}
