const gulp = require("gulp");
const ftp = require("vinyl-ftp");

//Colours:
var reset = "\x1b[0m";
var green = "\x1b[32m";
var magenta = "\x1b[35m";
var blue = "\x1b[34m";

var localFiles = ["**/*.php", "*.php"];

var host = "oliverrichman.uk";
var port = 21;
var user = process.env.u + "@oliverrichman.uk";
var password = process.env.p;
const remoteLocation = "/";

//Command: u=[username] p=[password] gulp ftp

function getFtpConnection() {
	return ftp.create({
		host: host,
		port: port,
		user: user,
		password: password
	});
}

gulp.task("ftp", function() {
	var conn = getFtpConnection();

	console.log("Connected to: ", green, host + ":" + port, reset);
	console.log(magenta + "FTP Uploading Started!", reset);
	console.log(blue + "Watching...", reset);

	gulp.watch("api").on("change", function(event) {
		console.log("Uploaded: " + green + event, reset);

		return gulp
			.src(localFiles, {base: ".", buffer: false})
			.pipe(conn.newer(remoteLocation))
			.pipe(conn.dest(remoteLocation));
	});
});
