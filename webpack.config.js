const path = require('path');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

require('dotenv').config();

const config = {
	entry: {
		functions: './resources/js/initialize.js',
		style: './resources/scss/style.scss',
	},
	output: {
		filename: 'assets/js/[name].min.js?[contenthash]',
		path: path.resolve(__dirname, 'public'),
		publicPath: '/',
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: 'assets/css/[name].min.css?[contenthash]',
		}),
		new WebpackManifestPlugin({
			map: (f) => {
				f.name = f.path.replace(/\?.+$/, '');
				return f;
			},
			fileName: 'mix-manifest.json',
		}),
	],
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: ['babel-loader'],
			},
			{
				test: /\.scss$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							url: false,
						},
					},
					{
						loader: 'postcss-loader',
						options: {
							postcssOptions: {
								plugins: [
									'autoprefixer',
									'cssnano',
								],
							},
						},
					},
					'sass-loader',
				],
			},
		],
	},
	optimization: {
		minimizer: [
			new TerserPlugin({
				extractComments: false,
			}),
		],
	},
};

module.exports = (env, argv) => ({
	...config,
	plugins: [
		...config.plugins,
		new BrowserSyncPlugin({
			proxy: process.env.APP_URL,
			files: [
				'public/assets/js/*.js',
				'public/assets/css/*.css',
			],
		}, {
			reload: false,
		}),
	],
	devtool: argv.mode === 'production' ? false : 'eval',
});
