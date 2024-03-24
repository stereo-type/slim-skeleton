const Encore = require("@symfony/webpack-encore")
const fs = require('fs');
const path = require('path');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev")
}

const appResourcesPath = path.join(__dirname, 'app');
const projectResourcesPath = path.join(__dirname, '_assets', 'js');

// Функция для добавления файлов в сборку
//TODO мб ускорить как то
const addCoreFilesToBuild = (directoryPath) => {
    fs.readdirSync(directoryPath).forEach(file => {
        const filePath = path.join(directoryPath, file);
        if (fs.statSync(filePath).isFile() && (file.endsWith('.js') || file.endsWith('.ts'))) {
            // Добавляем только файлы JavaScript в качестве точек входа
            const entryName = path.basename(file, path.extname(file)); // Имя файла без расширения
            Encore.addEntry(entryName, filePath);
        } else if (fs.statSync(filePath).isDirectory()) {
            // Если это директория, рекурсивно добавляем ее содержимое
            addCoreFilesToBuild(filePath);
        }
    });
};

// Добавляем файлы из app/Core/_assets в сборку
addCoreFilesToBuild(appResourcesPath);
addCoreFilesToBuild(projectResourcesPath);
Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/")

    // public path used by the web server to access the output path
    .setPublicPath("/build")
    // .enableTypeScriptLoader()
    .enableTypeScriptLoader(function (tsConfig) {
        // You can use this callback function to adjust ts-loader settings
        // https://github.com/TypeStrong/ts-loader/blob/master/README.md#loader-options
        // For example:
        tsConfig.silent = false
        tsConfig.transpileOnly = true
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    /**Включен auto-wiring,см addCoreFilesToBuild*/

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

    .configureBabel((config) => {
        config.plugins.push("@babel/plugin-proposal-class-properties")
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage"
        config.corejs = 3
    })

    .copyFiles({
        from: "./app/Core/_assets/images",
        to: "images/[path][name].[hash:8].[ext]",
        pattern: /\.(png|jpg|jpeg|gif)$/
    })

    // enables Sass/SCSS support
    .enableSassLoader()

module.exports = Encore.getWebpackConfig()
