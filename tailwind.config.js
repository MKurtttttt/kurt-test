import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        // Text colors
        'text-red-700', 'text-red-800', 'text-red-600', 'text-gray-900', 'text-gray-600', 'text-gray-400', 'text-gray-700', 'text-white', 'text-xs', 'text-sm', 'text-lg', 'text-xl', 'text-3xl', 'text-purple-700', 'text-blue-700', 'text-green-700', 'text-purple-800', 'text-blue-800', 'text-green-800', 'text-yellow-800',
        // Backgrounds
        'bg-white', 'bg-gray-50', 'bg-red-50', 'bg-red-100', 'bg-red-200', 'bg-green-100', 'bg-green-200', 'bg-blue-100', 'bg-blue-200', 'bg-purple-100', 'bg-purple-200', 'bg-yellow-100','bg-yellow-200', 'bg-yellow-500',
        'bg-red-700', 'bg-red-600', 'bg-blue-700', 'bg-blue-600', 'bg-green-700', 'bg-green-600', 'bg-purple-500', 'bg-purple-700', 'bg-purple-600', 'bg-blue-500',
        // Borders
        'border', 'border-red-100', 'border-red-300', 'border-red-400', 'border-green-100', 'border-green-400', 'border-blue-100', 'border-blue-200', 'border-purple-100', 'border-purple-200', 'border-gray-200', 'border-gray-700', 'border-purple-500',
        // Sizing and spacing
        'rounded', 'rounded-lg', 'rounded-xl', 'rounded-r-lg', 'shadow', 'shadow-lg', 'shadow-xl', 'p-4', 'p-6', 'p-8', 'px-1', 'px-2', 'px-3', 'px-4', 'px-7', 'py-1', 'py-2', 'py-3', 'py-4', 'py-6', 'mb-1', 'mb-2', 'mb-4', 'mb-6', 'mb-8', 'mt-2', 'mt-8', 'gap-2', 'gap-4', 'gap-6', 'min-h-[60vh]', 'min-h-[70vh]', 'max-w-md', 'max-w-xl', 'max-w-5xl', 'w-20', 'w-full', 'h-12', 'h-24', 'h-80',
        // Flex and grid
        'flex', 'flex-1', 'flex-col', 'flex-row', 'flex-wrap', 'items-center', 'items-end', 'items-start', 'justify-between', 'justify-center', 'overflow-hidden', 'overflow-x-hidden', 'overflow-y-auto',
        // Font and weight
        'font-bold', 'font-semibold', 'font-medium', 'font-thin',
        // Utility
        'cursor-pointer', 'hover:shadow-lg', 'hover:shadow-xl', 'hover:bg-red-600', 'hover:bg-red-200', 'hover:bg-blue-600', 'hover:bg-blue-200', 'hover:bg-green-600', 'hover:bg-green-200', 'hover:bg-purple-600', 'hover:bg-purple-200', 'hover:bg-yellow-600','hover:underline', 'transition', 'transition-colors', 'truncate', 'line-clamp-2', 'drop-shadow', 'sticky', 'top-0', 'z-20', 'hover:bg-gray-600', 'hover:bg-gray-50'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
