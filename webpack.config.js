const path = require("path");
const TerserPlugin = require("terser-webpack-plugin");

module.exports = {
    mode: "development",
    entry: {
        home: path.resolve(__dirname, "src/home/js/index.js"),
        admin: path.resolve(__dirname, "src/admin/js/index.js"),
    },
    output: {
        path: path.resolve(__dirname),
        filename: (pathData) => {
            return pathData.chunk.name === "admin"
                ? "assets/js/admin-build.js"
                : "assets/js/build.js";
        },
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@babel/preset-env"],
                    },
                },
            },
            {
                test: /\.(woff|woff2|svg|png|jpg|gif)$/,
                type: "asset/resource",
                generator: {
                    filename: "assets/static/[name][ext]",
                },
            },
        ],
    },
    optimization: {
        minimize: true,
        minimizer: [new TerserPlugin()],
    },
    devtool: "source-map",
    watch: true,
    watchOptions: {
        ignored: /node_modules/,
        aggregateTimeout: 10,
        poll: 50,
    },
    stats: {
        errorDetails: true,
    },
};
