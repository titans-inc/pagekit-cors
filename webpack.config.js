module.exports = [
    {
        entry: {
            "quote": "./app/views/admin/quote",
            "dashboard": "./app/components/dashboard-quote.vue",
            "toolbar": "./app/views/admin/editor-toolbar",
            "widget": "./app/components/widget-quote.vue",
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }
];