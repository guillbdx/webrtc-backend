var Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning()

    .addEntry('js/shoot', './assets/js/shoot.js')
    .addEntry('js/watch', './assets/js/watch.js')
    .addEntry('js/browse', './assets/js/browse.js')
    .addEntry('js/subscription-form', './assets/js/subscription-form.js')

    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/images', to: 'images' },
        { from: './assets/js/resemble.js', to: 'js/resemble.js' }
    ]))
;

module.exports = Encore.getWebpackConfig();
