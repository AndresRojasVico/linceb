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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('expediente', 50)->nullable();
            $table->text('sumario')->nullable();
            $table->dateTime('fecha_updated')->nullable();
            $table->string('vigente_anulada_archivada', 255)->nullable();
            $table->string('estado', 100)->nullable();
            $table->text('objeto_contratacion')->nullable();
            $table->decimal('presupuesto_sin_impuestos', 10, 2)->nullable();
            $table->decimal('presupuesto_con_impuestos', 10, 2)->nullable();
            $table->text('cpv')->nullable();
            $table->string('tipo_contrato', 100)->nullable();
            $table->string('lugar_ejecucion', 100)->nullable();
            $table->string('organo_contratacion', 255)->nullable();
            $table->string('id_organo_contratacion', 50)->nullable();
            $table->string('nif_organo_contratacion', 12)->nullable();
            $table->text('enlace_perfil_contratante')->nullable();
            $table->string('tipo_administracion', 100)->nullable();
            $table->string('sistema_contratacion', 255)->nullable();
            $table->string('tramitacion', 255)->nullable();
            $table->string('forma_presentacion', 255)->nullable();
            $table->dateTime('fecha_presentacion')->nullable();
            $table->dateTime('fecha_solicitud')->nullable();
            $table->dateTime('fecha_publicacion')->nullable();
            $table->string('directiva_aplicacion', 255)->nullable();
            $table->string('financiacion_europea', 100)->nullable();
            $table->text('descripcion_financiacion')->nullable();
            $table->boolean('subcontratacion_permitido')->nullable();
            $table->decimal('subcontratacion_porcentaje', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
