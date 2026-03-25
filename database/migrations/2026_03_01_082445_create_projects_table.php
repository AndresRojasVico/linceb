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
            $table->text('link')->nullable();
            $table->text('sumario')->nullable();
            $table->dateTime('fecha_updated')->nullable();
            $table->string('vigente_anulada_archivada', 255)->nullable();
            $table->string('estado', 100)->nullable();
            $table->text('objeto_contratacion')->nullable();
            $table->decimal('presupuesto_sin_impuestos', 15, 2)->nullable();
            $table->decimal('presupuesto_con_impuestos', 15, 2)->nullable();
            $table->decimal('valor_estimado_total', 15, 2)->nullable();
            $table->text('cpv')->nullable();
            $table->string('tipo_contrato', 100)->nullable();
            $table->string('subtipo_contrato', 50)->nullable();
            $table->string('duracion_contrato', 50)->nullable();
            $table->string('unidad_duracion', 10)->nullable(); // ANN | MON | DAY
            $table->string('lugar_ejecucion', 100)->nullable();
            $table->string('codigo_nuts', 20)->nullable();
            $table->string('organo_contratacion', 255)->nullable();
            $table->string('id_organo_contratacion', 50)->nullable();
            $table->string('nif_organo_contratacion', 50)->nullable();
            $table->text('enlace_perfil_contratante')->nullable();
            $table->string('plataforma_origen', 100)->nullable();
            $table->string('tipo_administracion', 100)->nullable();
            $table->string('sistema_contratacion', 255)->nullable();
            $table->string('procedimiento', 100)->nullable();
            $table->boolean('sobre_umbral')->nullable();
            $table->string('tramitacion', 255)->nullable();
            $table->string('forma_presentacion', 255)->nullable();
            $table->dateTime('fecha_presentacion')->nullable();
            $table->dateTime('fecha_solicitud')->nullable();
            $table->dateTime('fecha_publicacion')->nullable();
            $table->string('directiva_aplicacion', 255)->nullable();
            $table->string('financiacion_europea', 100)->nullable();
            $table->text('descripcion_financiacion')->nullable();
            $table->string('url_ppt', 500)->nullable();
            $table->boolean('subcontratacion_permitido')->nullable();
            $table->decimal('subcontratacion_porcentaje', 5, 2)->nullable();
            // Adjudicación
            $table->date('fecha_adjudicacion')->nullable();
            $table->string('empresa_adjudicataria', 255)->nullable();
            $table->string('nif_adjudicatario', 50)->nullable();
            $table->decimal('importe_adjudicacion_sin_iva', 15, 2)->nullable();
            $table->decimal('importe_adjudicacion_con_iva', 15, 2)->nullable();
            $table->unsignedSmallInteger('num_ofertas')->nullable();
            $table->unsignedSmallInteger('num_ofertas_pyme')->nullable();
            $table->boolean('adjudicado_a_pyme')->nullable();
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
