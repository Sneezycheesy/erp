<div {{$attributes->merge(['class' => "w-full p-2"])}}>
    <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Marie Curie -->
    <x-table-header>
    {{ $header }}
    </x-table-header>

    <div id="table-body-container" {{$tbody->attributes->merge(['class' => "max-h-[32rem] overflow-y-auto scrollbar-hide"])}}>
    {{ $tbody }}
    </div>

    <i id="loading-indicator" class="htmx-indicator fas fa-spinner fa-spin text-5xl text-center text-black dark:text-white col-span-full"></i>
</div>