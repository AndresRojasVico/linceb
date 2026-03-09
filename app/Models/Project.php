<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'expediente',
        'sumario',
        'fecha_updated',
        'vigente_anulada_archivada',
        'estado',
        'objeto_contratacion',
        'presupuesto_sin_impuestos',
        'presupuesto_con_impuestos',
        'cpv',
        'tipo_contrato',
        'lugar_ejecucion',
        'organo_contratacion',
        'id_organo_contratacion',
        'nif_organo_contratacion',
        'enlace_perfil_contratante',
        'tipo_administracion',
        'sistema_contratacion',
        'tramitacion',
        'forma_presentacion',
        'fecha_presentacion',
        'fecha_solicitud',
        'fecha_publicacion',
        'directiva_aplicacion',
        'financiacion_europea',
        'descripcion_financiacion',
        'subcontratacion_permitido',
        'subcontratacion_porcentaje',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_projects')
            ->withPivot('project_status_id', 'notes')
            ->withTimestamps();
    }
}
