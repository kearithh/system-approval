<?php

namespace App\Http\Controllers;

use App\RequestPO;
use App\RequestItemPO;
use App\RequestItemAttachment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestItemPOController extends Controller
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

//        dd($request->files);

//        $signatureSrc = Storage::disk('local')->put('user', $request->file('signature'));
//        $avatarSrc = null;
//        if ($request->hasFile('avatar')) {
//            $avatarSrc = Storage::disk('local')->put('user', $request->file('avatar'));
//        }

        // Check position
//        $position = Position::find($request->position_id);
//        if (!$position) {
//            $position = new Position([ 'name' => $request->position_id]);
//            $position->save();
//            $position = $position->id;
//        }
//        else {
//            $position = $position->id;
//        }

//        $data = array_merge(
//            $request->all(),
//            [
//                'password' => Hash::make($request->get('password')),
//                'signature' => $signatureSrc,
//                'avatar' => $avatarSrc,
//                'position_id' => $position,
//
//            ]
//        );
//        $model->create($data);

        $requestItemPO = new RequestItemPO($request->all());
        $requestItemPO->request_id = decrypt($request->request_token);
        $requestItemPO->save();

        if ($request->hasFile('quote')) {
            foreach ($request->file('quote') as $item) {
                $src = Storage::disk('local')->put('quote', $item);
                RequestItemAttachment::create([
                   'request_item_id' => $requestItem->id,
                   'src' => $src
               ]);
            }
        }
        return redirect('request/create?request_token='.$request->request_token);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RequestItemPO  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function show(RequestItemPO $requestItemPO)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RequestItemPO  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestItemPO $requestItemPO)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RequestItemPO  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestItemPO $requestItemPO)
    {
        //
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        RequestItemPO::destroy($id);
        return redirect()->back();
    }
}
