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
        Schema::create('totp_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Service/Account name
            $table->text('secret'); // Encrypted TOTP secret
            $table->string('issuer')->nullable(); // Service issuer (e.g., Google, GitHub)
            $table->string('algorithm')->default('sha1'); // sha1, sha256, sha512
            $table->integer('digits')->default(6); // 6 or 8 digits
            $table->integer('period')->default(30); // Time period in seconds
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('totp_entries');
    }
};
