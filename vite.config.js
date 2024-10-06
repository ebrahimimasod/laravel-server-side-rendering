import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import {fileURLToPath, URL} from 'node:url'
import * as path from "path";
import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";


const publicPath = "public/js/client";
const serverBuildPath = "public/js/server";


export default defineConfig({
    plugins: [
        laravel({
            input: './resources/js/app.js',
            ssr: './resources/js/server.js',
            refresh: true,
        }),
        vue({
            define: {
                __VUE_PROD_DEVTOOLS__: true
            },
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vueJsx(),

    ],

    // ssr: {
    //     noExternal: ['vue', '@vue/server-renderer']
    //   }
});
