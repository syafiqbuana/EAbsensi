<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // (student_id, date) → SKIP, sudah ada
            $table->index(['tpq_profile_id', 'date', 'status'], 'idx_attendances_tpq_date_status');
            $table->index(['schedule_id', 'date'], 'idx_attendances_schedule_date');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index(['tpq_profile_id', 'status'], 'idx_students_tpq_status');
            $table->index(['class_id', 'status'], 'idx_students_class_status');
        });

        Schema::table('student_user', function (Blueprint $table) {
            // (student_id) tunggal → SKIP, sudah ada sebagai FK index
            $table->unique(['user_id', 'student_id'], 'unique_student_user');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            // (student_id, status) → SKIP, sudah ada
            $table->index(['tpq_profile_id', 'status'], 'idx_leave_requests_tpq_status');
            $table->index(['start_date', 'end_date'], 'idx_leave_requests_dates');
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->unique('user_id', 'unique_user_profiles_user_id');
        });

        Schema::table('tpq_registrations', function (Blueprint $table) {
            $table->index(['applicant_id', 'status'], 'idx_tpq_reg_applicant_status');
        });

        Schema::table('study_records', function (Blueprint $table) {
            // Kolom `study_date`, BUKAN `date`
            $table->index(['student_id', 'study_date'], 'idx_study_records_student_date');
            $table->index(['class_id', 'study_date'], 'idx_study_records_class_date');
        });

        // Tidak ada di plan awal — untuk job otomatis cek hari libur
        Schema::table('holidays', function (Blueprint $table) {
            $table->index(['tpq_profile_id', 'start_date', 'end_date'], 'idx_holidays_tpq_dates');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_tpq_date_status');
            $table->dropIndex('idx_attendances_schedule_date');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_tpq_status');
            $table->dropIndex('idx_students_class_status');
        });

        Schema::table('student_user', function (Blueprint $table) {
            $table->dropUnique('unique_student_user');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex('idx_leave_requests_tpq_status');
            $table->dropIndex('idx_leave_requests_dates');
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropUnique('unique_user_profiles_user_id');
        });

        Schema::table('tpq_registrations', function (Blueprint $table) {
            $table->dropIndex('idx_tpq_reg_applicant_status');
        });

        Schema::table('study_records', function (Blueprint $table) {
            $table->dropIndex('idx_study_records_student_date');
            $table->dropIndex('idx_study_records_class_date');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->dropIndex('idx_holidays_tpq_dates');
        });
    }
};