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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('industry', 100);
            $table->string('services_provided', 255)->nullable();
            $table->string('ccn', 12)->nullable();
            $table->string('npi', 12)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('state_code', 3)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('website_url', 255)->nullable();
            $table->timestamp('created_on')->useCurrent();
            $table->unsignedInteger('created_by')->default(1);
            $table->timestamp('updated_on')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('updated_by')->nullable();
            $table->string('active', 12)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
