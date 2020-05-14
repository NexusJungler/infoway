var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')
    .copyFiles({
        from: './assets/images',

        // copying without versioning
        // target path, relative to the output dir
        to: 'images/[path][name].[ext]',

        // copying with versioning
        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name]_[hash:8].[ext]',

        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg)$/
    })

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('permission', './assets/js/permission.js')
    // create-user
    .addEntry('create-user', './assets/js/create/create-user.js')
    .addEntry('user_login', './assets/js/user_login.js')
    .addEntry('user_password_forget', './assets/js/user_password_forget.js')
    .addEntry('user_password_reset', './assets/js/user_password_reset.js')
    
    //settings
        //Enseigne
        .addEntry('enseigne', './assets/js/settings/enseigne.js')

    .addEntry('settings-user', './assets/js/settings/settings_user.js')
    // .addEntry('criterion', './assets/js/settings/criterion.js')

    // Product
    .addEntry('product', './assets/js/product.js')
    .addEntry('product_price', './assets/js/product/product_price.js')
    .addEntry('date', './assets/js/date.js')



    .addEntry('show_factories', './assets/js/show_factories.js')

    // TAGS
    .addEntry('tags', './assets/js/tags.js')

    .addEntry('criterion', './assets/js/criterion.js')


    


    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    .enableSassLoader()

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
