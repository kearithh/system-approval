<?php

namespace App\Http\Controllers;

use App\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $positions = Position::whereNotNull('name_km');
        $keyword = $request->keyword;
        if ($keyword)
        {
            $positions = Position::orWhere('short_name', 'like', "%$keyword%")
                ->orWhere('name_en', 'like', "%$keyword%")
                ->orWhere('name_km', 'like', "%$keyword%")
                ->orWhere('level', 'like', "%$keyword%")
                ;
        }
        // dd($positions);
        $positions = $positions->orderBy('short_name', 'ASC')->orderBy('name_km', 'ASC')->orderBy('level', 'ASC')->paginate(30);
        return view('position.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('position.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            "short_name" => "required",
            "name_km" => "required",
            "level" => "required"
        ]);
        $data = Position::create([
            'short_name' => $request->input('short_name'),
            'name_km' => $request->input('name_km'),
            'name_en' => $request->input('name_en'),
            'level' => $request->input('level'),
            'desc' => $request->input('desc')
        ]);

        return redirect()->route('position.index')->with(['status' => 1]);
    }

     /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $position = Position::find($id);
        return view('position.edit', compact('position'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request,[
            "short_name" => "required",
            "name_km" => "required",
            "level" => "required"
        ]);
        $position = Position::find($id);
        $position->short_name = $request->short_name;
        $position->name_km = $request->name_km;
        $position->name_en = $request->name_en;
        $position->level = $request->level;
        $position->desc = $request->desc;
        // $position->updated_by = Auth::user()->name;
        if ($position->save()) {
            return redirect()->route('position.index')->with(['status' => 2]);
        }
        else{
            return Redirect::back()->with(['status' => 0]);
        }

    }


    public function destroy($id)
    {
        $position=Position::find($id);
        // $position->deleted_at = Carbon::now();
        // $position->deleted_by = Auth::user()->name;
        if ($position->delete()) {
            return Redirect::back()->with(['status' => 3]);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }

}
