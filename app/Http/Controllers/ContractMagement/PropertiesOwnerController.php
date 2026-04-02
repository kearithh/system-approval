<?php

namespace App\Http\Controllers\ContractMagement;

use App\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Model\ContractMagement\PropertiesOwner;
use App\Traits\TelegramNotification;

class PropertiesOwnerController extends Controller
{
    use TelegramNotification;
    
    public function index(Request $request)
    {
        $propertiesOwner = PropertiesOwner::whereNotNull('name');
        $keyword = $request->keyword;
        if ($keyword)
        {
            $propertiesOwner = PropertiesOwner::Where('name', 'like', "%$keyword%");
        }
        $propertiesOwner = $propertiesOwner->orderBy('id', 'ASC')->paginate(30);
        return view('contractMagement.propertiesOwner.index', compact('propertiesOwner'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' =>'required|max:255',
        ]);
        $oldPro = PropertiesOwner::where('id',@$request->id)->first();
        try {
            PropertiesOwner::updateOrCreate([
                'id'            => $request->id,
            ],[
                'name'          =>$request->name,
                'description'   =>$request->description,
                'created_by'    => $oldPro->created_by ?? Auth::id(),
                'updated_by'    => $request->id ? Auth::id() :'',
            ]);
        return redirect()->back()->with('success','Properties Owner  Successfully');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

    }
    public function destroy($id) {
       $propertiesOwner = PropertiesOwner::find($id);
       $propertiesOwner->update([
        'deleted_by' => Auth::id(),
       ]);
       if ($propertiesOwner->delete()) {
        return Redirect::back()->with(['Successfully']);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }
}
