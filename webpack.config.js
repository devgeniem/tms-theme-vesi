const path = require( 'path' );
const webpack = require( 'webpack' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const ImageMinimizerPlugin = require( "image-minimizer-webpack-plugin" );
const { extendDefaultPlugins } = require( "svgo" );

// Check for production mode.
const isProduction = process.env.NODE_ENV === 'production';

// Local development server URL.
const wpProjectUrl = 'https://client-tms.test';

// Theme paths.
const wpThemePath = path.resolve( __dirname );
const themeName = path.basename( wpThemePath );
const themePath = `/app/themes/${ themeName }`;
const themePublicPath = `${ themePath }/assets/dist/`;
const themeOutput = `${ wpThemePath }/assets/dist`;

const entryPoints = {
    theme_vesi: [ `${ wpThemePath }/assets/scripts/theme-vesi.js` ],
};

// All loaders to use on assets.
const allModules = {
    rules: [
        {
            test: /\.js$/,
            exclude: /node_modules/,
            use: {
                loader: 'babel-loader',
                options: {

                    // Do not use the .babelrc configuration file.
                    babelrc: false,

                    // The loader will cache the results of the loader in node_modules/.cache/babel-loader.
                    cacheDirectory: true,

                    // Enable latest JavaScript features.
                    presets: [ '@babel/preset-env' ],

                    // Enable dynamic imports.
                    plugins: [ '@babel/plugin-syntax-dynamic-import' ]
                }
            }
        },
        {
            test: /\.scss$/,
            use: [
                MiniCssExtractPlugin.loader,
                {
                    loader: 'css-loader',
                    options: {
                        sourceMap: true
                    }
                },
                {
                    loader: 'postcss-loader',
                    options: {
                        sourceMap: true
                    }
                },
                {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: true
                    }
                }
            ]
        },
        {
            test: /\.(gif|jpe?g|png|svg)(\?[a-z0-9=\.]+)?$/,
            exclude: [ /assets\/fonts/, /assets\/icons/, /node_modules/ ],
            type: 'asset',
        },
        {
            test: /\.(eot|svg|ttf|otf|woff(2)?)(\?[a-z0-9=\.]+)?$/,
            exclude: [ /assets\/images/, /assets\/icons/, /node_modules/ ],
            type: 'asset/resource',
        },
        {
            test: /assets\/icons\/.*\.svg(\?[a-z0-9=\.]+)?$/,
            use: [
                {
                    loader: 'svg-sprite-loader',
                    options: {
                        symbolId: 'icon-[name]',
                        extract: true,
                        spriteFilename: 'icons.svg'
                    }
                },
                {
                    loader: 'svgo-loader',
                    options: {
                        plugins: [
                            { name: 'removeTitle' },
                            {
                                name: 'removeAttrs',
                                params: { attrs: [ 'path:fill', 'path:class' ] }
                            }
                        ]
                    }
                }
            ]
        }
    ],
};

// All optimizations to use.
const allOptimizations = {
    runtimeChunk: false,
    splitChunks: {
        cacheGroups: {
            vendor: {
                test: /[\\/]node_modules[\\/]/,
                name: 'vendor',
                chunks: 'all',
            },
        },
    },
};

// All plugins to use.
const allPlugins = [
    // Use BrowserSync.
    new BrowserSyncPlugin(
        {
            host: 'localhost',
            port: 3000,
            proxy: wpProjectUrl,
            files: [ { match: [ '**/*.php', '**/*.dust' ] } ],
            notify: true,
            open: false,
        },
        { reload: true }
    ),

    // Convert JS to CSS.
    new MiniCssExtractPlugin( { filename: '[name].css' } ),

    // Provide jQuery instance for all modules.
    new webpack.ProvidePlugin( { jQuery: 'jquery' } ),
];

// Use only for production build.
if ( isProduction ) {
    allOptimizations.minimizer = [

        // Optimize for production build.
        new TerserPlugin( {
            parallel: true,
            terserOptions: {
                output: {
                    comments: false
                },
                compress: {
                    warnings: false,
                    drop_console: true // eslint-disable-line camelcase
                }
            }
        } ),

        new ImageMinimizerPlugin( {
            minimizer: {
                implementation: ImageMinimizerPlugin.imageminMinify,
                options: {
                    // Lossless optimization with custom option
                    // Feel free to experiment with options for better result for you
                    plugins: [
                        [ "gifsicle", { interlaced: true } ],
                        [ "jpegtran", { progressive: true } ],
                        [ "optipng", { optimizationLevel: 5 } ],
                        // Svgo configuration here https://github.com/svg/svgo#configuration
                        [
                            "svgo",
                            {
                                plugins: extendDefaultPlugins( [
                                    {
                                        name: "removeViewBox",
                                        active: false,
                                    },
                                    {
                                        name: "addAttributesToSVGElement",
                                        params: {
                                            attributes: [ { xmlns: "http://www.w3.org/2000/svg" } ],
                                        },
                                    },
                                ] ),
                            },
                        ],
                    ],
                },
            },
        } ),
    ];
}

module.exports = [
    {
        resolve: {
            alias: {
                scripts: path.resolve( __dirname, 'assets', 'scripts' ),
                styles: path.resolve( __dirname, 'assets', 'styles' ),
            },
        },

        entry: entryPoints,

        output: {
            path: themeOutput,
            publicPath: themePublicPath,
            filename: '[name].js',
        },

        module: allModules,

        optimization: allOptimizations,

        plugins: allPlugins,

        externals: { jquery: 'jQuery' },

        // Disable source maps for production build.
        devtool: isProduction ? false : 'inline-source-map',
    },
];
