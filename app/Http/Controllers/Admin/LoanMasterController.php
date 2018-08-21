<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\LoanMaster;
use App\Admin;
use Illuminate\Http\Request;
use App\Mail\WelcomeNewAdmin;
use App\Http\Controllers\Controller;
use App\Traits\FileManipulationTrait;
use App\Transformers\AdminTransformer;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class LoanMasterController extends Controller
{
    use FileManipulationTrait;

    protected $loanmaster;
    protected $adminTransformer;

    public function __construct(Admin $admin,LoanMaster $loanmaster, AdminTransformer $adminTransformer)
    {
        $this->loanmaster            = $loanmaster;
        $this->adminTransformer = $adminTransformer;
        $this->admin = $admin;
    }

    public function postsdata(){

        $loanhistory = LoanMaster::where('recstat','0')->where('userid',\Auth::user()->id);

        return DataTables::of($loanhistory)
                ->addcolumn("action",function($loanhistory){
                    return '<div class="action-btns">
                                  <button class="btn-floating warning-bg edit" data-target="modal1"><i class="material-icons">person</i></button>
                            </div>';
                })
                ->make(true);
    }

    public function loanlistpostsdata(){

        $loanhistory = LoanMaster::where('recstat','0')->orderBy("id","ASC");

        return DataTables::of($loanhistory)
                ->addcolumn("action",function($loanhistory){
                    return '<div class="action-btns">
                                  <button class="btn-floating warning-bg edit" data-target="modal1"><i class="material-icons">person</i></button>
                            </div>';
                })
                ->make(true);
    }

    public function store(Request $request){
    	
        $prcount = LoanMaster::where("refno","like","%".date("Ymd")."%")->count();
        $prid = date("Ymd")."-".($prcount+1);

        $input                 = $request->all();
        $input['name']         = \Auth::user()->name;
        $input['userid']	   = \Auth::user()->id;
        $input['age']		   = Carbon::parse(\Auth::user()->bdate)->age;
        $input['refno']        = $prid;
        $input['overallaction']= "For Loan Officer Approval";
        $input['processtagno'] ="1";
        $input['termagreement'] ="Agree";
        $input['sharecapital'] = $request->sharecapital;
        $loanmaster = $this->loanmaster->create($input);
        return response([
            'message'     => trans('messages.message-add'),
            'status_code' => 201
        ], 201);
    }

    public function loanupdate(Request $request, $refno){
    	if (Gate::denies('developerOnly') && Gate::denies('admin.create')) { 
            return response(['error' => trans('messages.unauthorized-access')], 401);
        }
        $overallaction = "Loan Officer - ".$request->loanofficeraction." the Loan";
        LoanMaster::where("refno",$refno)->update(["loanofficername"=>$request->loanofficername,
            "loanofficerremarks"=>$request->loanofficerremarks,
            "loanofficeraction"=>$request->loanofficeraction,
            "cleavecredits" =>  $request->cleavecredits,
            "csalaryamount" =>  $request->csalaryamount,
            "processtagno"=>$request->loanofficerptag,
            "overallaction" => $overallaction, 
            ]);      

        return response([
            'message'     => trans("messages.message-add"),
            'status_code' => 201
        ], 201);
    }

    public function crecomupdate(Request $request, $refno){
    	if (Gate::denies('developerOnly') && Gate::denies('admin.create')) { 
            return response(['error' => trans('messages.unauthorized-access')], 401);
        }
        $overallaction = "CreCom - ".$request->crecomaction." the Loan";
        LoanMaster::where("refno",$refno)->update(["crecomremarks"=>$request->crecomremarks,
            "crecomaction"=>$request->crecomaction, 
            "processtagno"=>$request->crecomptag,
            "overallaction" => $overallaction, 
            ]);      

        return response([
            'message'     => trans("messages.message-add"),
            'status_code' => 201
        ], 201);
    }

    public function bodupdate(Request $request, $refno){
    	if (Gate::denies('developerOnly') && Gate::denies('admin.create')) { 
            return response(['error' => trans('messages.unauthorized-access')], 401);
        }
        $overallaction = "BOD - ".$request->bodaction." the Loan";
        LoanMaster::where("refno",$refno)->update(["chairmanremarks"=>$request->bodremarks,
            "chairmanaction"=>$request->bodaction, 
            "processtagno"=>$request->bodptag,
            "overallaction" => $overallaction, 
            ]);      

        return response([
            'message'     => trans("messages.message-add"),
            'status_code' => 201
        ], 201);
    }

    public function getcalculate($id,$loantype,$loanamount){
    	$servicefee=0;
    	$insurancefee=0;
    	$input = array();
    	$info = $this->admin->where("id",\Auth::user()->id)->where("signaturekey",$id)->first();
    	if($loantype=="Petty Cash"){
    		$input["priorloan"]=$info->totalpettycashloan;
    	}
    	elseif($loantype=="Emergency"){
    		$input["priorloan"]=$info->totalemergencyloan;
    	}
    	elseif($loantype=="Travel"){
    		$input["priorloan"]=$info->totalotherstloan;
    	}
    	elseif($loantype=="Commodity"){
    		$input["priorloan"]=$info->totalcommodityloan;
    	}
    	elseif($loantype=="Regular"){
    		$input["priorloan"]=$info->totalregularloan;
    	}
    	else{
    		$input["priorloan"]="0.00";
    	}

    	if($loanamount < 50000){
    		$servicefee = $loanamount * 0.03;
    	}
    	elseif($loanamount >= 50000 && $loanamount < 100000){
    		$servicefee = $loanamount * 0.02;
    	}
    	elseif($loanamount >= 100000){
    		$servicefee = $loanamount * 0.01;
    	}
    	$input["totaldeductions"] = ($servicefee * 2) + $input["priorloan"] + $insurancefee;
    	$input["netproceeds"] = $loanamount - $input["totaldeductions"];
    	$input["servicefee"] = $servicefee;
    	$input["retentionfee"] = $servicefee;
    	$input["loantype"] = $loantype;
    	$input["loanamount"] = $loanamount;
    	return response()->json($input);
    }

    public function getloanhistory($id){
    	$info = $this->loanmaster->where("refno",$id)->first();
    	return response()->json($info);
    }
}
