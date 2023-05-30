// require modules
const fs = require("fs");
const fse = require("fs-extra");
const archiver = require("archiver");

const args = process.argv.slice(2);
let settings = {
    name: "polytranslate",
    output: "polytranslate",
};

for (let i = 0; i < args.length; i++) {
    let values = args[i].split("=");
    settings[values[0]] = values[1];
}

// create a file to stream archive data to.
if (!fs.existsSync(__dirname + "/build")) {
    fs.mkdirSync(__dirname + "/build");
}

const destDir = __dirname + "/build/" + settings.name;
const decryptedDir = destDir + "-decrypted";
const output = fs.createWriteStream(__dirname + "/build/" + settings.output + ".zip");
const archive = archiver("zip", {
    zlib: {
        level: 9,
    }, // Sets the compression level.
});

// listen for all archive data to be written
// 'close' event is fired only when a file descriptor is involved
output.on("close", function () {
    fse.removeSync(destDir);
    fse.removeSync(decryptedDir);
    console.log(archive.pointer() + " total bytes");
    console.log("archiver has been finalized and the output file descriptor has closed.");
});

// This event is fired when the data source is drained no matter what was the data source.
// It is not part of this library but rather from the NodeJS Stream API.
// @see: https://nodejs.org/api/stream.html#stream_event_end
output.on("end", function () {
    console.log("Data has been drained");
});

// good practice to catch warnings (ie stat failures and other non-blocking errors)
archive.on("warning", function (err) {
    throw err;
});

// good practice to catch this error explicitly
archive.on("error", function (err) {
    throw err;
});

// pipe archive data to the file
archive.pipe(output);

// create directory
archive.directory("app", settings.name + "/app");
archive.directory("freemius", settings.name + "/freemius");
archive.directory("languages", settings.name + "/languages");
archive.directory("public", settings.name + "/public");
archive.directory("routes", settings.name + "/routes");
archive.directory("vendor_prefixed", settings.name + "/vendor_prefixed");
archive.file("polytranslate.php", { name: settings.name + "/polytranslate.php" });
archive.file("index.php", { name: settings.name + "/index.php" });
archive.file("readme.txt", { name: settings.name + "/readme.txt" });

archive.finalize();
