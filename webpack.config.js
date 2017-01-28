module.exports = [
    {
        entry: {
            "settings": "./app/views/admin/settings",
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