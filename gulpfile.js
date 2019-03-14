const gulp = require("gulp");
const ftp = require("vinyl-ftp");

//Colours:
var reset = "\x1b[0m";
var green = "\x1b[32m";
var magenta = "\x1b[35m";
var blue = "\x1b[34m";

// var localFiles = ["**/*.php","*.php","."];
var localFiles = ["api/**/*.php","api/.*","vendor/**/*.*","composer.json","composer.lock"];

var host = "oliverrichman.uk";
var port = 21;
var user = process.env.u + "@oliverrichman.uk";
var password = process.env.p;
const remoteLocation = "/";

//COMMAND: u=[username] p=[password] gulp ftp

function getFtpConnection() {
	return ftp.create({
		host: host,
		port: port,
		user: user,
		password: password
	});
}
// scp -i ~/Desktop/amazon.pem ~/Desktop/MS115.fa  ubuntu@ec2-54-166-128-20.compute-1.amazonaws.com:~/data/
// scp -i ~/eap/api/index.php ec2-user@ec2-18-130-241-198.eu-west-2.compute.amazonaws.com:~/var/www/html/

gulp.task("ftp", function() {
	var conn = getFtpConnection();

	console.log("Connected to: ", green, host + ":" + port, reset);
	console.log(magenta + "FTP Uploading Started!", reset);
	console.log(blue + "Watching...", reset);

	gulp.watch("api").on("change", function(event) {
			console.log("Changed: " + green + event, reset);
			return gulp
				.src(localFiles, {base: "./", buffer: false})
				.pipe(conn.newer(remoteLocation))
				.pipe(conn.dest(remoteLocation));
	});
});

gulp.task('reload',function (cb){
	var conn = getFtpConnection();
	conn.rmdir(remoteLocation, function(err){
		cb();
		console.log("Cleaned...");
		console.log("Reloaded");
		return gulp
			.src(localFiles, {base: "./", buffer: false})
			.pipe(conn.newer(remoteLocation))
			.pipe(conn.dest(remoteLocation));
	});
})
gulp.task("default", gulp.series('ftp'));
