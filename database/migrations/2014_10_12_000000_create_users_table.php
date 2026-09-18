<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', [
                'pi',           // Principal Investigator
                'tm',           // Team Member / Co-Researcher
                'reviewer',     // Blind Examiner / Reviewer
                'dh',           // Department Head
                'coordinator',  // College Research Coordinator
                'dean',         // School / College Dean
                'irerc',        // Institutional Research Ethics Review Committee
                'vparttcs',     // Vice President Office (ARTTCS)
                'rcsc',         // Research & Community Service Committee / President
                'finance',      // Finance Office (SDD SCR-14)
                'admin'         // System Administrator
            ])->default('pi');
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->string('status')->default('active');
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
        Schema::dropIfExists('users');
    }
};
