const { dependencies } = require('./package.json');

module.exports = {
  name: 'remote',
  exposes: {
    './LogPanel': './src/LogPanel',
  },
  filename: 'remoteEntry.js',
  shared: {
    // ...dependencies,
    react: {
      singleton: true,
      requiredVersion: dependencies['react'],
    },
    'react-dom': {
      singleton: true,
      requiredVersion: dependencies['react-dom'],
    },
    '@mui/material': {
      singleton: true,
      requiredVersion: dependencies['@mui/material'],
    },
    '@mui/icons-material': {
      singleton: true,
      requiredVersion: dependencies['@mui/icons-material'],
    },
  },
};
