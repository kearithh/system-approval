<?php

namespace App\Http\Controllers;

use App\RequestPR;
use App\RequestItemPR;
use App\RequestItemAttachment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestItemPRController extends Controller
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

        $requestItemPR = new RequestItemPR($request->all());
        $requestItemPR->request_id = decrypt($request->request_token);
        $requestItemPR->save();

        if ($request->hasFile('quote')) {
            foreach ($request->file('quote') as $item) {
                $src = Storage::disk('local')->put('quote', $item);
                RequestItemAttachment::create([
                   'request_item_id' => $requestItemPR->id,
                   'src' => $src
               ]);
            }
        }
        return redirect('request_pr/create?request_token='.$request->request_token);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RequestItemPR  $requestItemPR
     * @return \Illuminate\Http\Response
     */
    public function show(RequestItemPR $requestItemPR)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RequestItemPR  $requestItemPR
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestItemPR $requestItemPR)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RequestItemPR  $requestItemPR
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestItemPR $requestItemPR)
    {
        //
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        RequestItemPR::destroy($id);
        return redirect()->back();
    }
}
