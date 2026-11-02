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
        Schema::create('email_templates', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('type')->index(); // CONTACT_FORM, etc.
            $blueprint->string('status')->default('ACTIVE');
            $blueprint->integer('position')->nullable();
            $blueprint->timestamps();
            $blueprint->softDeletes();
        });

        Schema::create('email_template_translations', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('email_template_id')->constrained()->cascadeOnDelete();
            $blueprint->string('locale')->index();
            $blueprint->string('subject')->nullable();
            $blueprint->text('content')->nullable();

            $blueprint->unique(['email_template_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_template_translations');
        Schema::dropIfExists('email_templates');
    }
};
