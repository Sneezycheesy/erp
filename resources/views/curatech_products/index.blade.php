<x-app-layout>
    <div class="py-6">
        <div class="grid gap-2 grid-cols-10 grid-flow-cols grid-rows-1 mt-5 py-7 px-7 dark:text-white dark:bg-gray-700 w-3/4 mx-auto h-full rounded">
            <div class="grid col-1 col-span-8">
                <x-searchbar route="{{route('curatech_products')}}"
                    target="#curatech_products_container"
                    swap="outerHTML">    
                </x-searchbar>
            </div>
            
            <x-new-button hx-get="{{route('curatech_products.create')}}"
                class="text-center bg-green-700 w-full h-full hover:bg-green-900 hover:cursor-pointer align-center col-span-2">
                <!-- <input type="button" value="Creëer" class="w-full h-full"> -->
                +
            </x-new-button>
            <!-- TODO: Add vendors.delete route -->
            <!-- <input type="button" value="Verwijder" class="disabled text-center bg-red-700 hover:bg-red-900 hover:cursor-default"> -->
        </div>

        @include('curatech_products.partials.curatech_products')
    </div>
</x-app-layout>