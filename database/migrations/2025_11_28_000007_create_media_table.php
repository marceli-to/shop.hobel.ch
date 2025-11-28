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
		Schema::create('media', function (Blueprint $table) {
			$table->id();
			$table->morphs('mediable'); // Creates mediable_type, mediable_id, and index
			$table->string('file_name');
			$table->string('file_path');
			$table->string('mime_type')->nullable();
			$table->unsignedBigInteger('size')->nullable();
			$table->unsignedInteger('width')->nullable();
			$table->unsignedInteger('height')->nullable();
			$table->text('caption')->nullable();
			$table->integer('order')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('media');
	}
};
