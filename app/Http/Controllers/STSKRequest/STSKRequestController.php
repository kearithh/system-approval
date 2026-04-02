<?php

namespace App\Http\Controllers\STSKRequest;

use App\Entities\FormType;
use App\Entities\STSKRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class STSKRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $formType = FormType::select('id');
        dd(123);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\STSKRequest  $sTSKRequest
     * @return \Illuminate\Http\Response
     */
    public function show(STSKRequest $sTSKRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\STSKRequest  $sTSKRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(STSKRequest $sTSKRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\STSKRequest  $sTSKRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, STSKRequest $sTSKRequest)
    {
        //
    }

    /**
     * @param STSKRequest $sTSKRequest
     */
    public function destroy(STSKRequest $sTSKRequest)
    {
        //
    }

    public function approve() {

    }

    public function reject()
    {

    }

    public function reSubmit()
    {

    }
}
