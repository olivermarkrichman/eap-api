const deployer = require('../../Desktop/web/npm_modules/ec2-deploy/index.js');
deployer.setConfig({
	host: '54.224.84.234',
	username: 'nginx',
	password: '4n*E5cBzqYuTrT#',
	remotePath: './eapi.mezaria.com'
});

deployer.autoDeploy('v1/');