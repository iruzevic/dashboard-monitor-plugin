const DEV = process.env.NODE_ENV !== 'production';

const path = require('path');

const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

const appPath = `${path.resolve(__dirname)}`;

// HPB Services
const pluginPath = '/skin';
const pluginFullPath = `${appPath}${pluginPath}`;
const pluginPublicPath = `${pluginPath}/public/`;
const pluginEntry = `${pluginFullPath}/assets/application.js`;
const pluginOutput = `${pluginFullPath}/public`;


// Outputs
const outputJs = 'scripts/[name].js';
const outputCss = 'styles/[name].css';
const outputFile = '[name].[ext]';
const outputImages = `images/${outputFile}`;
const outputFonts = `fonts/${outputFile}`;

const allModules = {
  rules: [
    {
      test: /\.(js|jsx)$/,
      use: 'babel-loader',
      exclude: /node_modules/
    },
    {
      test: /\.json$/,
      use: 'file-loader'
    },
    {
      test: /\.(png|svg|jpg|jpeg|gif|ico)$/,
      use: `file-loader?name=${outputImages}`
    },
    {
      test: /\.(eot|otf|ttf|woff|woff2)$/,
      use: `file-loader?name=${outputFonts}`
    },
    {
      test: /\.scss$/,
      use: ExtractTextPlugin.extract({
        fallback: 'style-loader',
        use: ['css-loader', 'postcss-loader', 'sass-loader']
      })
    }
  ]
};

const allPlugins = [
  new CleanWebpackPlugin([pluginOutput]),
  new ExtractTextPlugin(outputCss),
];

// Use only for production build
if (!DEV) {
  allPlugins.push(
    new UglifyJSPlugin({
      comments: false,
      sourceMap: true
    })
  );
}

module.exports = [

  // HPB Services plugin
  {
    cache: false,
    context: path.join(__dirname),
    entry: {
      'dashboard-monitor-application': [pluginEntry]
    },
    output: {
      path: pluginOutput,
      publicPath: pluginPublicPath,
      filename: outputJs
    },

    module: allModules,

    plugins: allPlugins,

    devtool: DEV ? '#inline-source-map' : '',
  }
];
