<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanMaster extends Model
{
    protected $fillable = [
        'refno','userid','name','age','datefiling', 'loanstatus', 'loanterms', 'loantype', 'signaturekey1', 'signaturekey2','coid','coname','copassbookno','cosharecapital','cleavecredits','csalaryamount','sharecapital','totalregularloan','totalpettycashloan','totalcommodityloan','totalemergencyloan','totalotherstloan','totalloans','totalloanable','loanamount','priorloan','servicefee','retentionfee','insurancefee','totaldeductions','netproceeds','interestpercent','interestamount','totalinterest','totalamount','monthlyamort','termagreement','loanofficerremarks','loanofficeraction','loanofficername','crecomremarks','crecomaction','chairmanremarks','chairmanaction','processtagno','overallaction','recstat'
    ];
}