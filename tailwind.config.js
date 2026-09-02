import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Geist', ...defaultTheme.fontFamily.sans],
                mono: ['"Geist Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Superfícies e texto
                ground: 'var(--bg)',
                surface: 'var(--surface)',
                'surface-2': 'var(--surface-2)',
                'surface-3': 'var(--surface-3)',
                line: 'var(--border)',
                'line-strong': 'var(--border-strong)',
                ink: 'var(--text)',
                'ink-2': 'var(--text-2)',
                'ink-muted': 'var(--text-muted)',
                // Ação / marca
                primary: 'var(--primary)',
                'primary-hover': 'var(--primary-hover)',
                'primary-soft': 'var(--primary-soft)',
                accent: 'var(--accent)',
                'accent-soft': 'var(--accent-soft)',
                onprimary: 'var(--onPrimary)',
                // Destaque (âmbar)
                hl: 'var(--hl)',
                'hl-soft': 'var(--hl-soft)',
                'hl-ink': 'var(--hl-ink)',
                // Severidade / status
                crit: 'var(--crit)',
                'crit-soft': 'var(--crit-soft)',
                high: 'var(--high)',
                'high-soft': 'var(--high-soft)',
                med: 'var(--med)',
                'med-soft': 'var(--med-soft)',
                low: 'var(--low)',
                'low-soft': 'var(--low-soft)',
                ok: 'var(--ok)',
                'ok-soft': 'var(--ok-soft)',
            },
            boxShadow: {
                card: 'var(--shadow-sm)',
                float: 'var(--shadow-md)',
            },
            borderRadius: {
                btn: '10px',
                card: '16px',
            },
            ringColor: {
                focus: 'var(--ring)',
            },
        },
    },

    plugins: [forms],
};
