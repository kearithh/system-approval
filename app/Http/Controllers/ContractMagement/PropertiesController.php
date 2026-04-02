<?php

namespace App\Http\Controllers\ContractMagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Model\ContractMagement\Properties;
use App\Model\ContractMagement\PropertiesOwner;

class PropertiesController extends Controller
{
    public function index(Request $request)
    {
        $properties = Properties::whereNotNull('data->name');
        $keyword = $request->keyword;
        if ($keyword)
        {
            $properties = Properties::Where('data->name', 'like', "%$keyword%")
                ->orWhere('data->type', 'like', "%$keyword%")
                ->orWhere('data->description', 'like', "%$keyword%")
                ->orWhereHas('proOwner', function($query) use ($keyword) {
                    $query->where('name','like', "%$keyword%");
                })
                ->orWhereHas('userCreated', function($query) use ($keyword) {
                    $query->where('username','like', "%$keyword%");
                });
        }
        $properties = $properties->orderBy('id', 'ASC')->paginate(30);
        $propertiestOwner = PropertiesOwner::select('id', 'name')->get();

        return view('contractMagement.properties.properties', compact('properties','propertiestOwner'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' =>'required|max:255',
        ]);
        $oldPro = Properties::where('id',@$request->id)->first();
        $obj = json_decode(@$oldPro->data);
        try {
            if($request->file('attachfile')){
                $image = $request->file('attachfile');
                $file_name = $image->getClientOriginalName();
                $image->move(public_path('uploads'), $file_name);
            }
            Properties::updateOrCreate(
                        [
                            'id' => $request->id,
                        ],[
                            'updated_by'            =>  $request->id ? Auth::id():null,
                            'created_by'            => @$oldPro->created_by ?? Auth::id() ,
                            'properties_owner_id'   => @$request->properties_owner_id,
                            'data'          => json_encode([
                                    'type'          => $request->type,
                                    'name'          => @$request->name,
                                    'attachfile'    => @$file_name ?? @$obj->attachfile,
                                    'description'   => $request->description,
                                ]),
                        ]);
        return redirect()->back()->with('success','Properties Owner  Successfully');
        } catch (\Exception $e) {
            return response()->json(["error" => $e], 200);
        }

    }
    public function show($id)
    {
        $properties = Properties::find($id);

        return view('properties.show', compact('properties'));
    }
    public function destroy($id){
        $Properties = Properties::find($id);
        $Properties->update([
            'deleted_by' => Auth::id(),
        ]);
       if ($Properties->delete()) {
        return Redirect::back()->with(['Successfully']);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }
}
