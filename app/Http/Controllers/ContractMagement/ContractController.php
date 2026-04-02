<?php

namespace App\Http\Controllers\ContractMagement;

use App\User;
use Constants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\SendMailContract;
use App\Http\Controllers\Controller;
use App\Traits\TelegramNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\Model\ContractMagement\Contracts;
use App\Model\ContractMagement\Properties;
use App\Model\ContractMagement\PaymentHistory;
use App\Model\ContractMagement\ConstractHistory;
use App\Model\ContractMagement\NotificationChannel;

class ContractController extends Controller
{
    use TelegramNotification;
    private function _convertString2Float($value)
    {
        $output =  floatval(preg_replace("/[^-0-9.]/", "", $value));
        $output = round($output, 2);
        return $output;
    }
    public function index(Request $request)
    {
        $contracts = Contracts::whereNotNull('data->contract_type');
        $keyword = $request->keyword;
        if ($keyword)
        {
            $contracts = Contracts::orWhere('data->contract_type', 'like', "%$keyword%")
                        ->orWhere('data->full_amount', 'like', "%$keyword%")
                        ->orWhere('data->remaining_amount', 'like', "%$keyword%")
                        ->orWhere('data->effective_date', 'like', "%$keyword%")
                        ->orWhere('data->due_date', 'like', "%$keyword%")
                        ->orWhere('data->attachfile', 'like', "%$keyword%")
                        ->orWhereHas('propertiesName', function($query) use ($keyword) {
                            $query->where('data->name','like', "%$keyword%");
                            $query->orwhereHas('proOwner', function($qu) use ($keyword) {
                                $qu->where('name','like', "%$keyword%");
                            });
                        });
        }
        $contracts = $contracts->orderBy('id', 'DESC')->paginate(30);
        $properties = Properties::select('id', 'data')->get();
        return view('contractMagement.contract.Contract', compact('contracts','properties'));
    }
    public function store(Request $request)
    {
        $oldPro = Contracts::where('id',@$request->id)->first();
        $obj = json_decode(@$oldPro->data);
        try {
            if($request->file('attachfile')){
                $image = $request->file('attachfile');
                $file_name = $image->getClientOriginalName();
                $image->move(public_path('uploads'), $file_name);
            }
            $contract = Contracts::updateOrCreate(
                [
                    'id' => $request->id,
                ],[
                    'property_id'   => $request->properties_id,
                    'updated_by'    => $request->id ? Auth::id() :null,
                    'created_by'    =>  $oldPro->created_by ?? Auth::id(),
                    'data' => json_encode([
                        'contract_type'     => $request->contract_type,
                        'contract_cycle'    => @$request->contract_cycle,
                        'attachfile'        => @$file_name ?? @$obj->attachfile,
                        'effective_date'    => date('d-m-Y', strtotime(@$request->effective_date)),
                        'due_date'          => date('d-m-Y', strtotime(@$request->due_date)),
                        'full_amount'       => $this->_convertString2Float(@$request->full_amount),
                        'remaining_amount'  => $this->_convertString2Float(@$request->full_amount),
                        'description'       => @$request->description,
                    ]),

                ]);
            ConstractHistory::create([
                'contract_id' => $contract->id,
                'property_id'   => $request->properties_id,
                'data'        => $contract->data,
                'created_by'  => $oldPro->created_by ?? Auth::id(),
                'updated_by'  =>  $request->id ? Auth::id() : null,

            ]);
            return redirect()->back()->with('success','Contracts  Successfully');
        } catch (\Exception $e) {
            return response()->json(["Eerror" => $e]);
        }
    }
    public function destroy($id)
    {
        $Contracts = Contracts::find($id);
        $Contracts->update([
            'deleted_by' => Auth::id(),
        ]);
       if ($Contracts->delete()) {
        return Redirect::back()->with(['Successfully']);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }
    public function paymentContract(Request $request)
    {
        try {
            if($request->file('attachfile')){
                $image = $request->file('attachfile');
                $file_name = $image->getClientOriginalName();
                $image->move(public_path('uploads'), $file_name);
            }
            Contracts::where('id', $request->id)->update(['data->remaining_amount' => $this->_convertString2Float(@$request->remaining_amount)]);
            PaymentHistory::create([
                'contract_id' => $request->id,
                'data'  => json_encode([
                        'paid_date'             => date('Y-m-d', strtotime(@$request->paid_date)),
                        'paid_by'               => Auth::id(),
                        'paid_amount'           => $this->_convertString2Float(@$request->paid_amount),
                        'payment_receipt_file'  => @$file_name,
                        'description'           => @$request->description,
                        'remaining_amount'      => $this->_convertString2Float(@$request->remaining_amount),
                ]),
                'created_by'  => Auth::id(),
            ]);
            return redirect()->back()->with('success','Pay  Successfully');
        }catch (\Exception $e) {
            return response()->json(["Eerror" => $e]);
        }
    }
    public function showPayment()
    {
        $paymentHistory = PaymentHistory::where('contract_id',request()->id)->orderBy('id','DESC')->get();
        return view('contractMagement.contract.Payment', compact('paymentHistory'));

    }
    public function showContractHistory()
    {
        $contractHistory = ConstractHistory::where('contract_id',request()->id)->orderBy('id','DESC')->get();
        return view('contractMagement.contract.ContractHistory', compact('contractHistory'));

    }

}
