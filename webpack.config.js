var Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning()
    .enableLessLoader()

    .addEntry('js-css/main', './assets/js/main.js')
    .addEntry('js-css/shoot', './assets/js/shoot.js')
    .addEntry('js-css/watch', './assets/js/watch.js')
    .addEntry('js-css/browse', './assets/js/browse.js')
    .addEntry('js-css/subscription-form', './assets/js/subscription-form.js')

    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/images', to: 'images' },
        { from: './assets/js/resemble.js', to: 'js-css/resemble.js' }
    ]))
;

module.exports = Encore.getWebpackConfig();
