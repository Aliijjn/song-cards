<?php

use App\Enum\CurationTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('curations', function (Blueprint $table) {
            $table->string('type')->default(CurationTypeEnum::Personal->value)->after('system_generated');
        });

        DB::statement("UPDATE curations SET type = 'editorial' WHERE system_generated = 1");

        Schema::table('curations', function (Blueprint $table) {
            $table->dropColumn('system_generated');
        });
    }

    public function down(): void
    {
        Schema::table('curations', function (Blueprint $table) {
            $table->boolean('system_generated')->default(false)->after('type');
        });

        DB::statement("UPDATE curations SET system_generated = 1 WHERE type = 'editorial'");

        Schema::table('curations', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
