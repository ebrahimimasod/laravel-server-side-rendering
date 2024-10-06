import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";


createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name)=>resolvePageComponent(
    `../pages/${name}.vue`,
    import.meta.glob("../pages/**/*.vue")
),
    setup({el, App, props, plugin}) {

    return createApp({render: () => h(App, props)})
        .use(plugin)
        .mount(el);
},
progress: {
    delay: 250,
        color: "#29d",
        includeCSS: true,
        showSpinner: true,
},
});


