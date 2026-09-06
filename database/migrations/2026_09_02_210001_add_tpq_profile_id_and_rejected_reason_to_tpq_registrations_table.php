<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tpq_registrations', function (Blueprint $table) {
            $table->foreignId('tpq_profile_id')->nullable()->after('applicant_id')->constrained('tpq_profiles')->nullOnDelete();
            $table->text('rejected_reason')->nullable()->after('notes');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('tpq_registrations', function (Blueprint $table) {
            $table->dropForeign(['tpq_profile_id']);
            $table->dropColumn(['tpq_profile_id', 'rejected_reason', 'reviewed_at']);
        });
    }
};
