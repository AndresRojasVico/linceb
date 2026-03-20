{{--
    Componente: status-badge
    Uso: <x-status-badge :status="$estado" />

    Pasos:
    1. Recibe la prop 'status' con el nombre del estado del proyecto.
    2. Determina las clases de color mediante un match según el estado:
       - Pendiente  → ámbar (advertencia)
       - En Proceso → azul  (en curso)
       - Completada → verde (éxito)
       - default    → gris  (desconocido)
    3. Renderiza un <span> con estilo pill (borde, fondo y texto del mismo color).
--}}

@props(['status'])

{{-- Paso 2: asignar clases de color según el estado --}}
@php
    $classes = match($status) {
        'Pendiente'  => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'En Proceso' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'Completada' => 'bg-green-50 text-green-700 ring-green-600/20',
        default      => 'bg-gray-50 text-gray-700 ring-gray-600/20',
    };
@endphp

{{-- Paso 3: renderizar la etiqueta pill con las clases calculadas --}}
<span {{ $attributes->class(['inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset', $classes]) }}>
    {{ $status }}
</span>
