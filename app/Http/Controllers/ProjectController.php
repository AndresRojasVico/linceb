<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProjectFavorite;

class ProjectController extends Controller
{

    /**
     * Lista los proyectos con fecha de presentación futura, paginados de 6 en 6.
     * También obtiene en una sola query los IDs de proyectos que el usuario
     * autenticado ya tiene iniciados (registros en user_projects), para que
     * la vista pueda marcar visualmente cada tarjeta sin hacer queries adicionales.
     */
    public function index()
    {
        return view('Projects');
    }
    /**
     * Muestra los detalles de un proyecto junto con los datos
     * de participación del usuario autenticado (pivot user_projects).
     *
     * - Busca el proyecto por ID; devuelve 404 si no existe.
     * - Recupera el registro pivot del usuario actual para ese proyecto,
     *   incluyendo su estado de trabajo de usuario.
     * - Carga todos los estados posibles para el desplegable de la vista.
     */
    public function project_details($id)
    {
        $proyecto = Project::find($id);
        if (!$proyecto) {
            abort(404);
        }

        // Registro pivot del usuario autenticado para este proyecto
        $userProject = $proyecto->users()
            ->where('user_id', auth()->id())
            ->first();

        // Estados disponibles para el selector de la vista
        $statuses = ProjectStatus::all();

        // Badge estado
        $estado = $proyecto->estado ?? 'ABIERTA';
        $estadoClass = match (true) {
            str_contains(strtolower($estado), 'adjudic') => 'bg-blue-100 text-blue-700',
            str_contains(strtolower($estado), 'urgente') => 'bg-amber-100 text-amber-700',
            default => 'bg-green-100 text-green-700',
        };

        // Badge tipo contrato
        $tipo = strtolower($proyecto->tipo_contrato ?? '');
        [$tipoColor, $tipoIcon] = match (true) {
            str_contains($tipo, 'servicio')   => ['bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300', 'briefcase'],
            str_contains($tipo, 'suministro') => ['bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300', 'cube'],
            str_contains($tipo, 'obra')       => ['bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300', 'wrench-screwdriver'],
            str_contains($tipo, 'concesion') || str_contains($tipo, 'concesión') => ['bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300', 'building-library'],
            default => ['bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-300', 'tag'],
        };

        // Duración legible
        $duracionTexto = null;
        if ($proyecto->duracion_contrato) {
            $unidad = match ($proyecto->unidad_duracion ?? '') {
                'ANN' => 'años',
                'MON' => 'meses',
                'DAY' => 'días',
                default => $proyecto->unidad_duracion ?? '',
            };
            $duracionTexto = $proyecto->duracion_contrato . ' ' . $unidad;
        }

        // Contador de documentos disponibles
        $docsCount = collect([
            $proyecto->enlace_perfil_contratante,
            $proyecto->url_ppt,
            $proyecto->link,
            $proyecto->plataforma_origen,
        ])->filter()->count();

        return view('projectDetails', compact(
            'proyecto',
            'userProject',
            'statuses',
            'estado',
            'estadoClass',
            'tipoColor',
            'tipoIcon',
            'duracionTexto',
            'docsCount'
        ));
    }

    /**
     * Actualiza el estado y las notas del usuario autenticado
     * para un proyecto concreto (tabla pivot user_projects).
     *
     * - Valida que el estado enviado exista en la tabla project_status.
     * - Usa updateExistingPivot para modificar solo la fila del usuario
     *   actual sin afectar a otros usuarios vinculados al mismo proyecto.
     * - Redirige de vuelta con un mensaje de confirmación.
     */
    public function update_status(Request $request, $id)
    {
        $request->validate([
            'project_status_id' => 'required|exists:project_status,id',
            'notes' => 'nullable|string',
        ]);

        $proyecto = Project::findOrFail($id);

        // Actualiza solo las columnas del pivot sin tocar el resto de relaciones
        $proyecto->users()->updateExistingPivot(auth()->id(), [
            'project_status_id' => $request->project_status_id,
            'notes' => $request->notes,
        ]);

        return back()->with('status_updated', 'Estado actualizado correctamente.');
    }


    /**
     * Inicia un proyecto para el usuario autenticado creando un registro
     * en la tabla pivot user_projects con estado por defecto "Pendiente" (id 1).
     *
     * - Evita duplicados: si el usuario ya tiene el proyecto, no crea otro registro.
     * - Redirige a los detalles del proyecto con un mensaje de confirmación.
     */
    public function project_create($id)
    {
        $proyecto = Project::findOrFail($id);

        // Solo insertar si el usuario aún no tiene este proyecto iniciado
        if (!$proyecto->users()->where('user_id', Auth::id())->exists()) {
            $proyecto->users()->attach(Auth::id(), [
                'project_status_id' => 2, // Estado inicial: en proceso 
            ]);
        }

        // Al iniciar el proyecto, lo quitamos de favoritos automáticamente
        UserProjectFavorite::where('user_id', Auth::id())
            ->where('project_id', $id)
            ->delete();

        return redirect()->route('project_details', $id)
            ->with('status_updated', 'Proyecto añadido correctamente.');
    }


    public function project_drop($id)
    {
        $proyecto = Project::findOrFail($id);

        // Eliminar la relación del usuario con el proyecto
        $proyecto->users()->detach(Auth::id());

        return redirect()->route('project_details', $id)
            ->with('status_updated', 'Has soltado el proyecto correctamente.');
    }




    /**
     * Muestra los proyectos marcados como favoritos por el usuario autenticado.
     *
     * - Filtra los registros de user_project_favorites del usuario actual.
     * - Carga el Project asociado en la misma query (eager loading) para evitar N+1.
     * - Extrae solo los objetos Project de la colección de favoritos.
     * - Pasa la colección a la vista 'favorites'.
     */
    public function favorites()
    {
        return view('ProjectFavorites');
    }
}
