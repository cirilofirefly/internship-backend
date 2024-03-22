<?php

namespace App\Http\Controllers\Api\Intern;

use App\Http\Controllers\Controller;
use App\Http\Requests\DTRFileRequest;
use App\Models\DailyTimeRecord;
use App\Models\DTRFile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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

    public function downloadDTRFile(Request $request)
    {
        $data = DailyTimeRecord::where('user_id', $request->user()->id)
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->selectRaw('
                am_start_time,
                am_end_time,
                pm_start_time,
                pm_end_time,
                overtime_start_time,
                overtime_end_time,
                DAY(daily_time_records.date) as day
            ')
            ->get();

        $collection = new Collection();

        for($i = 1; $i <= 31; $i++) {

            $findItem = $data->filter(function($d) use($i) { return (int)$d->day == $i; })->first();

            $newItem = [
                'day'                 => $i,
                'am_start_time'       => $findItem->am_start_time ?? '',
                'am_end_time'         => $findItem->am_end_time ?? '',
                'pm_start_time'       => $findItem->pm_start_time ?? '',
                'pm_end_time'         => $findItem->pm_end_time ?? '',
                'overtime_start_time' => $findItem->overtime_start_time ?? '',
                'overtime_end_time'   => $findItem->overtime_end_time ?? '',
            ];

            $collection->push($newItem);
        }

        $date = Carbon::create()->day(1)
            ->year($request->year)
            ->month($request->month);

        $full_name = $request->user()->full_name;

        $pdf = Pdf::loadView('pdfs.dtr', ['data' => [
            'name'         => $full_name,
            'month'        => $date->format('F Y'),
            'collection'   => $collection,
        ]]);

        return $pdf->download();
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
