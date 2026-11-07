<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_approvals', function (Blueprint $table) {
            $table->dropForeign(['patron_applicant_id']);
        });

        Schema::rename(
            'patron_applicants',
            'membership_applications'
        );

        Schema::table('user_approvals', function (Blueprint $table) {

            $table->renameColumn(
                'patron_applicant_id',
                'membership_application_id'
            );
        });

        Schema::table('user_approvals', function (Blueprint $table) {
            $table->unique('membership_application_id');

            $table->foreign('membership_application_id')
                ->references('id')
                ->on('membership_applications')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_approvals', function (Blueprint $table) {
            $table->dropForeign(['membership_application_id']);
        });

        Schema::table('user_approvals', function (Blueprint $table) {
            $table->dropUnique(['membership_application_id']);
        });

        Schema::table('user_approvals', function (Blueprint $table) {
            $table->renameColumn(
                'membership_application_id',
                'patron_applicant_id'
            );
        });

        Schema::rename(
            'membership_applications',
            'patron_applicants'
        );

        Schema::table('user_approvals', function (Blueprint $table) {
            $table->unique('patron_applicant_id');

            $table->foreign('patron_applicant_id')
                ->references('id')
                ->on('patron_applicants')
                ->restrictOnDelete();
        });
    }
};
