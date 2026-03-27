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
        $projects = Project::where('fecha_presentacion', '>=', now())
            ->orderBy('fecha_presentacion')
            ->paginate(6);

        // IDs de proyectos ya iniciados por el usuario actual (una sola query).
        // Si no hay sesión activa, devuelve array vacío para evitar errores.
        $iniciados = Auth::check()
            ? Auth::user()->projects()->pluck('projects.id')->toArray()
            : [];

        return view('Projects', compact('projects', 'iniciados'));
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
}
