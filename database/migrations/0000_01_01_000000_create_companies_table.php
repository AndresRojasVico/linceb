<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('nif', 20)->unique();
            $table->string('contact_person', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 20)->nullable(); //Si en el futuro planeas enviar notificaciones por SMS o WhatsApp (muy útil en licitaciones para avisar de plazos que vencen), te recomiendo guardar el número siempre en formato internacional puro (ej: +34600000000).
            $table->string('email')->nullable()->unique();
            $table->timestamps();
            $table->boolean('is_active')->default(true);
            $table->string('image_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('companies');
    }
};
