const path = require('path');
const webpack = require('webpack');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');

module.exports = {
  entry: {
    'layout/layout': './templates/layout/layout.js',
    'web/home': './templates/web/home.js',
    'layout/datatables': './templates/layout/datatables.js',
    'web/users': './templates/web/users.js',
    'web/customers': './templates/web/customers.js',
    'web/products': './templates/web/products.js',
    'web/lots': './templates/web/lots.js',
    'web/labels': './templates/web/labels.js',
    'web/labelsLot': './templates/web/labelsLot.js',
    'web/labelsSplit': './templates/web/labelsSplit.js',
    'web/labelsSelectMerge': './templates/web/labelsSelectMerge.js',
    'web/labelsMerge': './templates/web/labelsMerge.js',
    'web/labelVoidReasons': './templates/web/labelVoidReasons.js',
    'web/splitLabels': './templates/web/splitLabels.js',
    'web/lotDefects': './templates/web/lotDefects.js',
    'web/merges': './templates/web/merges.js',
    'web/mergeDetail': './templates/web/mergeDetail.js',
    'web/sells': './templates/web/sells.js',
    'web/cpoItem': './templates/web/cpoItem.js',
    'web/cpoItemSelects': './templates/web/cpoItemSelects.js',
    'web/selectLabelForSells': './templates/web/selectLabelForSells.js',
    'web/sellLabels': './templates/web/sellLabels.js',
    'web/scraps': './templates/web/scrap.js',
    'web/scrapDetails': './templates/web/scrapDetails.js',
    'web/defects': './templates/web/defects.js',
    'web/sections': './templates/web/sections.js',
  },
  output: {
    path: path.resolve(__dirname, 'public/assets'),
    publicPath: 'assets/',
  },
  optimization: {
    minimizer: [new TerserJSPlugin({}), new OptimizeCSSAssetsPlugin({})],
  },
  performance: {
    maxEntrypointSize: 1024000,
    maxAssetSize: 1024000
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader']
      },
      {
        test: /\.(ttf|eot|svg|woff|woff2)(\?[\s\S]+)?$/,
        include: path.resolve(__dirname, './node_modules/@fortawesome/fontawesome-free/webfonts'),
        use: {
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'webfonts',
            publicPath: '../webfonts',
          },
        }
      },
      {
        test: /\.js$/,
        exclude: path.resolve('node_modules'),
        use: [{
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env']
            ]
          }
        }]
      },
    ],
  },
  plugins: [
    new CleanWebpackPlugin(),
    new ManifestPlugin(),
    new MiniCssExtractPlugin({
      ignoreOrder: false
    }),
  ],
  watchOptions: {
    ignored: ['./node_modules/']
  },
  mode: "development"
};