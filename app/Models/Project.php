<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'expediente',
        'link',
        'sumario',
        'fecha_updated',
        'vigente_anulada_archivada',
        'estado',
        'objeto_contratacion',
        'presupuesto_sin_impuestos',
        'presupuesto_con_impuestos',
        'valor_estimado_total',
        'cpv',
        'tipo_contrato',
        'subtipo_contrato',
        'duracion_contrato',
        'unidad_duracion',
        'lugar_ejecucion',
        'codigo_nuts',
        'organo_contratacion',
        'id_organo_contratacion',
        'nif_organo_contratacion',
        'enlace_perfil_contratante',
        'plataforma_origen',
        'tipo_administracion',
        'sistema_contratacion',
        'procedimiento',
        'sobre_umbral',
        'tramitacion',
        'forma_presentacion',
        'fecha_presentacion',
        'fecha_solicitud',
        'fecha_publicacion',
        'directiva_aplicacion',
        'financiacion_europea',
        'descripcion_financiacion',
        'url_ppt',
        'subcontratacion_permitido',
        'subcontratacion_porcentaje',
        // Adjudicación
        'fecha_adjudicacion',
        'empresa_adjudicataria',
        'nif_adjudicatario',
        'importe_adjudicacion_sin_iva',
        'importe_adjudicacion_con_iva',
        'num_ofertas',
        'num_ofertas_pyme',
        'adjudicado_a_pyme',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_projects')
            ->using(UserProject::class)
            ->withPivot('project_status_id', 'notes')
            ->withTimestamps();
    }
}
