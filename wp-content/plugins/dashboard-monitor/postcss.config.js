const DEV = process.env.NODE_ENV !== 'production';

const plugins = [
  require('autoprefixer')
];

// Use only for production build
if (!DEV) {
  plugins.push(require('cssnano'));
}

module.exports = {plugins};
