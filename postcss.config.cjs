// Use the new PostCSS wrapper for Tailwind CSS. Install
// `@tailwindcss/postcss`, `postcss` and `autoprefixer` before building.
module.exports = {
    plugins: [
        require('@tailwindcss/postcss'),
        require('autoprefixer'),
    ],
};
