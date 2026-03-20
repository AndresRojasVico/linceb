<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;

class ProjectController extends Controller
{

    /**
     * Lista todos los proyectos asignados al usuario autenticado,
     * incluyendo el estado y notas del pivot user_projects.
     */
    /**
     * Lista todos los proyectos de la tabla projects.
     */
    public function index()
    {
        $projects = Project::where('fecha_presentacion', '>=', now())
            ->orderBy('fecha_presentacion')
            ->paginate(6);

        return view('Projects', compact('projects'));
    }
    /**
     * Muestra los detalles de un proyecto junto con los datos
     * de participación del usuario autenticado (pivot user_projects).
     *
     * - Busca el proyecto por ID; devuelve 404 si no existe.
     * - Recupera el registro pivot del usuario actual para ese proyecto,
     *   incluyendo su estado y notas.
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

        return view('projectDetails', compact('proyecto', 'userProject', 'statuses'));
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
}
