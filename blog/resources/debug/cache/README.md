# About

It is a simple example of how to use the remote panel.

# How to use

1. Run the server

```bash
npm start
```

2. Open the browser and go to `http://localhost:3002`

> Port is 3002 because the yii-dev-panel is running on port 3000
> 
> You can change the port in the `package.json` file

3. You may use the remote panel to debug your application in the browser.
4. When you are done, you need to build the application to prepare the production files.

```bash
npm run build
```

5. All is you need is to add the `dist` folder your application assets to be served by the web server.

6. More about the remote panel on the backend, please visit [yii-debug]