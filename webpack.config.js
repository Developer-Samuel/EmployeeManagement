const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    mode: "development",
    entry: '/www/scss/app.scss', // Cesta k vašemu hlavnému SCSS súboru
    output: {
        path: __dirname + '/www/css', // Cesta, kam sa uloží výstupné súbory
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    "sass-loader",
                ],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "app.css", // Názov výstupného CSS súboru
        }),
    ],
};
