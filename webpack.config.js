module.exports = [
    {
        entry: {
            "settings": "./app/views/admin/settings",
            "path-index": "./app/views/admin/path-index",
            "path-edit": "./app/views/admin/path-edit"
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