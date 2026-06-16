<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->boolean('agreed_to_rules')->default(false)->after('status');
            $table->timestamp('agreed_at')->nullable()->after('agreed_to_rules');
            $table->string('agreed_ip')->nullable()->after('agreed_at');
        });
    }

    public function down()
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['agreed_to_rules', 'agreed_at', 'agreed_ip']);
        });
    }
};
