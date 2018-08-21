<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('refno',222)->index()->unique();
            $table->string('userid',222)->index();
            $table->string('name')->nullable();
            $table->string('age')->nullable();
            $table->string('datefiling')->nullable();
            $table->string('loanstatus')->nullable();
            $table->string('loantype')->nullable();
            $table->string('signaturekey1',255)->nullable();
            $table->string('signaturekey2',255)->nullable();
            $table->string('coid',222)->index();
            $table->string('coname')->nullable();
            $table->string('copassbookno')->nullable();
            $table->string('cosharecapital')->nullable();
            $table->string('csalaryamount')->nullable("yes");
            $table->string('cleavecredits')->nullable("yes");
            $table->string('sharecapital',255)->nullable("yes");
            $table->string('totalregularloan')->nullable("yes");
            $table->string('totalpettycashloan')->nullable("yes");
            $table->string('totalemergencyloan')->nullable("yes");
            $table->string('totalcommodityloan')->nullable("yes");
            $table->string('totalotherstloan')->nullable("yes");
            $table->string('totalloans')->nullable();
            $table->string('totalloanable')->nullable();
            $table->string('loanamount')->nullable();
            $table->string('priorloan')->nullable();
            $table->string('servicefee')->nullable();
            $table->string('retentionfee')->nullable();
            $table->string('insurancefee')->nullable();
            $table->string('totaldeductions')->nullable();
            $table->string('netproceeds')->nullable();
            $table->string('interestpercent')->nullable();
            $table->string('interestamount')->nullable();
            $table->string('totalinterest')->nullable();
            $table->string('totalamount')->nullable();
            $table->string('monthlyamort')->nullable();
            $table->string('termagreement')->nullable();
            $table->string('loanofficerremarks')->nullable();
            $table->string('loanofficeraction')->nullable();
            $table->string('loanofficername')->nullable();
            $table->string('crecomremarks')->nullable();
            $table->string('crecomaction')->nullable();
            $table->string('chairmanremarks')->nullable();
            $table->string('chairmanaction')->nullable();
            $table->string('processtagno')->nullable();
            $table->string('overallaction')->nullable();
            $table->enum('status',['approved','disapproved'])->default('approved');
            $table->string('recstat')->nullable();
            $table->_token()->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan_masters');
    }
}
