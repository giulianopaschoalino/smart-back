<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_access_tokens', 'deleted_at')) {
                $table->softDeletes()->after('last_used_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('personal_access_tokens', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
