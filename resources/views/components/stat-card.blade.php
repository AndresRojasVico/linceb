@props([
    'title',
    'value',
    'icon',
    'color',
    'nboton',
])
    
<flux:card class="p-4">
        <flux:text >{{ $title }}</flux:text>
        <div class='flex items-center' >
            <flux:icon class="mr-2 opacity-80" name="{{ $icon }}" size="lg" >
            </flux:icon>
        <flux:text class="font-extrabold text-2xl text-green-500">
            {{ $value }}
        </flux:text>

        </div>
            <flux:button size="xs" variant="{{ $color }}">{{ $nboton }}</flux:button>
 </flux:card>
