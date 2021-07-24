const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');

module.exports = {
    entry: {
      'layout/layout': './templates/layout/layout.js',
      'web/home': './templates/web/home.js',
      'layout/datatables': './templates/layout/datatables.js', // <-- add this line
      'web/users': './templates/web/users.js', // <-- add this line
      'web/customers': './templates/web/customers.js', // <-- add this line
      'web/vendors': './templates/web/vendors.js', // <-- add this line
      'web/spare_parts': './templates/web/spare_parts.js', // <-- add this line
      'web/products': './templates/web/products.js', // <-- add this line
      'web/categories': './templates/web/categories.js', // <-- add this line
      'web/models': './templates/web/models.js', // <-- add this line
      'web/machines': './templates/web/machines.js', // <-- add this line
      'web/tool_layout_standards': './templates/web/tool_layout_standards.js', // <-- add this line
      'web/tool_layout_standard_details': './templates/web/tool_layout_standard_details.js', // <-- add this line
      'web/spare_part_issues': './templates/web/spare_part_issues.js', // <-- add this line
      'web/spare_part_issue_details': './templates/web/spare_part_issue_details.js', // <-- add this line
      'web/edit_spare_part_issue_detail': './templates/web/edit_spare_part_issue_detail.js', // <-- add this line
      'web/transfer_stores': './templates/web/transfer_stores.js', // <-- add this line
      'web/transfer_store_details': './templates/web/transfer_store_details.js', // <-- add this line
      'web/report_spare_part_usage': './templates/web/report_spare_part_usage.js', // <-- add this line
      'web/report_spare_part_movement': './templates/web/report_spare_part_movement.js', // <-- add this line
      'web/down_prices': './templates/web/down_prices.js', // <-- add this line
      'web/down_price_details': './templates/web/down_price_details.js', // <-- add this line    
      'web/master_cost': './templates/web/master_cost.js', // <-- add this line
      'web/count_stocks': './templates/web/count_stocks.js', // <-- add this line
      'web/count_stock_details': './templates/web/count_stock_details.js', // <-- add this line
      // here you can add more entries for each page or global assets like jQuery and bootstrap
      // 'layout/layout': './templates/layout/layout.js'
      
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