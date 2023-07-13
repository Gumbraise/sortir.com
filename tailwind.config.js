/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {
            keyframes: {
                pulsions: {
                    '0%, 100%': { opacity: 0.3 },
                    '50%': { opacity: 0.6 },
                }
            },
            animation: {
                pulsions: 'pulsions 10s ease-in-out infinite',
            },
        },
    },
    plugins: [],
}

