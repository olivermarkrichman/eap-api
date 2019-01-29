const gulp = require("gulp");
const ftp = require("vinyl-ftp");

var localFiles = ["**/*.php", "*.php"];
var user = process.env.u + "@oliverrichman.uk";
var password = process.env.p;
const remoteLocation = "/";

function getFtpConnection() {
	return ftp.create({
		host: "oliverrichman.uk",
		port: 21,
		user: user,
		password: password
	});
}

gulp.task("ftp", function() {
	var conn = getFtpConnection();
	gulp.watch("**/*.php").on("change", function(event) {
		console.log("Uploaded: ", event);

		return gulp
			.src(localFiles, {base: ".", buffer: false})
			.pipe(conn.newer(remoteLocation))
			.pipe(conn.dest(remoteLocation));
	});
});
