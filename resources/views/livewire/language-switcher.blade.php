@php
$locale = session('locale', config('app.locale'));
@endphp

<div class="flex items-center space-x-2">
    @foreach (['en' => 'English', 'id' => 'Bahasa', 'de' => 'Deutsch'] as $code => $label)
    <button wire:click="switchLanguage('{{ $code }}')"
        class="px-4 py-1.5 text-sm rounded-xl font-semibold transition-all duration-200 shadow-sm
                {{ $locale === $code ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-indigo-100' }}">
        {{ strtoupper($code) }} | {{ $label }}
    </button>
    @endforeach
</div>
