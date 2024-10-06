import {createSSRApp, h} from 'vue';
import createServer from '@inertiajs/vue3/server';
import {createInertiaApp} from '@inertiajs/vue3';
import {renderToString} from '@vue/server-renderer';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title}`,
        resolve: (name) => resolvePageComponent(`../pages/${name}.vue`, import.meta.glob('../pages/**/*.vue')),
        setup({App, props, plugin}) {
            return createSSRApp({render: () => h(App, props)}).use(plugin);
        },
    })
);
