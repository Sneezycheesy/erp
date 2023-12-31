<x-app-layout>
    <x-slot name="header">
        {{ __('Leveranciers') }}
    </x-slot>
    <x-searchbar route="{{route('vendors')}}"
        target="#vendors_container"
        swap="outerHTML">    
        <x-new-button hx-get="{{route('vendors.create')}}" class="w-full h-full"/>
    </x-searchbar>
    
    @include('vendors.partials.Vendors')
</x-app-layout>