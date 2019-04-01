const deployer = require('../../Desktop/web/npm_modules/ec2-deploy/index.js');
deployer.setConfig({
	host: '52.56.40.181',
	username: 'deploy-api',
	password: 'Smoke+Mirrors55',
	remotePath: '../../var/www/html/eapi'
});

deployer.autoDeploy('api/', true);