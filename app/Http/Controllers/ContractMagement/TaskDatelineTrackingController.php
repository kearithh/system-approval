<?php

namespace App\Http\Controllers\ContractMagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use App\Imports\TaskDatelineTrackingImport;
use App\Model\ContractMagement\TaskDatelineTracking;

class TaskDatelineTrackingController extends Controller
{
    public function index(Request $request){
        $keyword = $request->keyword;
        $data = TaskDatelineTracking::whereNull('deleted_at');
        if(Auth::id() != 2844){
            $data = $data->where('created_by',Auth::id());
        }
        if($keyword){
            $data = $data->Where('data->description', 'like', "%$keyword%");
            $data = $data->orWhere('data->due_date', 'like', "%$keyword%");
        }
        $data = $data->orderBy('id', 'DESC')->paginate(30);
        return view('contractMagement.taskTracking.task_date_tracking', compact('data'));
    }
    public function store(Request $request)
    {
        $oldPro = TaskDatelineTracking::where('id',@$request->id)->first();
        $obj = json_decode(@$oldPro->data);
        try {
            if($request->file('attachfile')){
                $image = $request->file('attachfile');
                $file_name = $image->getClientOriginalName();
                $image->move(public_path('uploads'), $file_name);
            }
            TaskDatelineTracking::updateOrCreate(
                [
                    'id' => $request->id,
                ],[
                    'updated_by'    => $request->id ? Auth::id() :null,
                    'created_by'    =>  $oldPro->created_by ?? Auth::id(),
                    'data' => json_encode([
                        'attachfile'        => @$file_name ?? @$obj->attachfile,
                        'due_date'          => date('d-m-Y', strtotime(@$request->due_date)),
                        'description'       => @$request->description,
                        'is_id_telegram'    => @$request->is_id_telegram,
                    ]),

                ]);
            return redirect()->back()->with('success',' Successfully');
        } catch (\Exception $e) {
            return response()->json(["Eerror" => $e]);
        }
    }
    public function destroy($id)
    {
        $data = TaskDatelineTracking::find($id);
        $data->update([
            'deleted_by' => Auth::id(),
        ]);
       if ($data->delete()) {
        return Redirect::back()->with(['Successfully']);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function import(Request $request)
    {
        $this->validate($request, [
            'staff_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path1 = $request->file('staff_file')->store('temp');
        $path=storage_path('app').'/'.$path1;
        Excel::import(new TaskDatelineTrackingImport, $path);
        return back()->with('success', 'Excel Data Imported successfully.');
    }
}
