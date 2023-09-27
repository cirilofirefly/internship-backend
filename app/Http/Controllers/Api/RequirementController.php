<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequirementRequest;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    public function getRequirements(Request $request)
    {
        $user_id = isset($request->from_supervisor) ? $request->user_id : $request->user()->id;
        $searchKeyword = isset($request->search) ?
            $request->search :
            '';
        return Requirement::where('user_id', $user_id)
            ->where(function($query) use($searchKeyword) {
                $query->where('file_name', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('type', 'like', '%' . $searchKeyword . '%');
            })
            ->paginate(5);
    }

    public function getRequirementsAsSupervisor(Request $request)
    {
        $searchKeyword = isset($request->search) ?
            $request->search :
            '';
        return User::whereIntern()
            // ->where(function($query) use($searchKeyword) {
            //     $query->where('product_name', 'LIKE', '%' . $searchKeyword . '%');

            //     $columns = ['product_code', 'place_location', 'remark'];

            //     foreach ($columns as $column ) {
            //         $query->orWhere($column, 'LIKE', '%' . $searchKeyword . '%');
            //     }
            // })
            ->with('requirements')
            ->has('requirements')
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
    
    public function submitRequirements(Request $request)
    {
        return Requirement::whereIn('id', $request->ids)
            ->where('status', 'default')
            ->update(['status' => 'submitted']);
    }

    public function deleteRequirement($id)
    {

        $requirment = Requirement::where('id', $id)->first();

        if(Storage::exists($requirment->file)) {
            Storage::delete($requirment->file);
        }
        
        return $requirment->delete();
    }

    public function downloadFile($id)
    {
        return Storage::download(Requirement::where('id', $id)->first()->file);
    }
}
