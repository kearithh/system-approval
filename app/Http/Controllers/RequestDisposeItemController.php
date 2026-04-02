<?php

namespace App\Http\Controllers;

use App\RequestDispose;
use App\RequestDisposeItem;
use App\RequestForm;
use App\RequestItem;
use App\RequestItemAttachment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestDisposeItemController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestId = $request->request_id;
        if (!$requestId) {
            $requestDispose = new RequestDispose();
            $requestDispose->save();
            $requestId = $requestDispose->id;
        }
        $requestItemData = $request->all();
        $requestItemData['request_id'] = $requestId;
        $requestItemData['purchase_date'] = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $requestItemData['broken_date'] = Carbon::createFromFormat('d-m-Y', $request->broken_date);

        $requestItem = new RequestDisposeItem($requestItemData);
        $requestItem->save();

        return redirect('request_dispose/create?request_token='.encrypt($requestId));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RequestItem  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function show(RequestItem $requestItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RequestItem  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestItem $requestItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RequestItem  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestItem $requestItem)
    {
        //
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        RequestDisposeItem::destroy($id);
        return redirect()->back();
    }
}
