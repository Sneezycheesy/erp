<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-700 dark:bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-200 uppercase tracking-widest hover:bg-red-400 dark:hover:bg-red-500 focus:bg-red-400 dark:focus:bg-red-500 active:bg-red-300 dark:active:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150 justify-center']) }}>
    {{$slot}}
</button>