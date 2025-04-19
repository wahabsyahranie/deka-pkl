import tailwindcss from "tailwindcss";
import nesting from "postcss-nesting";
import autoprefixer from "autoprefixer";

export default {
    plugins: [nesting(), tailwindcss(), autoprefixer()],
};
