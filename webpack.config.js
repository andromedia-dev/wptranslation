"use strict";

const { VueLoaderPlugin } = require("vue-loader");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const path = require("path");

module.exports = (env = {}) => ({
    watch: env.production ? false : true,
    mode: env.production ? "production" : "development",
    devtool: env.production ? "source-map" : "eval-cheap-module-source-map",
    entry: [path.resolve("resources/js/app.js")],
    output: {
        path: path.resolve("public"),
        filename: "js/polytranslate.js",
    },
    resolve: {
        alias: {
            "@Layouts": path.resolve("resources/js/Layouts"),
            "@Pages": path.resolve("resources/js/Pages"),
            "@Components": path.resolve("resources/js/Components"),
            "@": path.resolve("resources/js"),
        },
        extensions: [".js", ".vue"],
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                use: "vue-loader",
            },
            {
                test: /\.css$/,
                use: [MiniCssExtractPlugin.loader, "css-loader", "postcss-loader"],
            },
        ],
    },
    plugins: [
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin({
            filename: "css/polytranslate.css",
        }),
    ],
});
