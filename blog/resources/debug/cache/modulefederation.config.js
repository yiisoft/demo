// TODO: get from sdk

const sharedModules = {
    '@emotion/react': {singleton: true},
    '@emotion/styled': {singleton: true},
    '@hookform/resolvers': {singleton: true},
    '@mui/icons-material': {singleton: true},
    '@mui/lab': {singleton: true},
    '@mui/material': {singleton: true},
    '@mui/x-data-grid': {singleton: true},
    '@reduxjs/toolkit': {singleton: true},
    '@textea/json-viewer': {singleton: true},
    'clipboard-copy': {singleton: true},
    'date-fns': {singleton: true},
    immupdate: {singleton: true},
    'mui-nested-menu': {singleton: true},
    react: {singleton: true},
    'react-dom': {singleton: true},
    'react-error-boundary': {singleton: true},
    'react-hook-form': {singleton: true},
    'react-redux': {singleton: true},
    'react-router': {singleton: true},
    'react-router-dom': {singleton: true},
    'react-scripts': {singleton: true},
    'react-syntax-highlighter': {singleton: true},
    'redux-persist': {singleton: true},
    'swagger-ui-react': {singleton: true},
    yup: {singleton: true},
};

module.exports = {
    name: 'remote',
    exposes: {
        './LogPanel': './src/LogPanel',
        './CachePanel': './src/CachePanel',
    },
    filename: 'external.js',
    shared: sharedModules,
};
