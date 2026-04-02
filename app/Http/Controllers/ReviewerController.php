<?php

namespace App\Http\Controllers;

use App\Position;
use App\Reviewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Redirect;

class ReviewerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions = Position::orderBy('level', 'ASC')->orderBy('short_name', 'ASC')->paginate(30);
        return view('reviewer.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reviewer.create');
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

        return redirect()->route('reviewer.index')->with(['status' => 1]);
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
        return view('reviewer.edit', compact('position'));
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
            return redirect()->route('reviewer.index')->with(['status' => 2]);
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
