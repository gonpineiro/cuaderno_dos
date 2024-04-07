const Zip = require("adm-zip");

const zip = new Zip();

/* Agregamos carpetas */
zip.addLocalFolder("./app", '/app');
zip.addLocalFolder("./bootstrap", '/bootstrap');
zip.addLocalFolder("./config", '/config');
zip.addLocalFolder("./database", '/database');
zip.addLocalFolder("./public", '/public');
zip.addLocalFolder("./resources", '/resources');
zip.addLocalFolder("./routes", '/routes');
zip.addLocalFolder("./storage", '/storage');
zip.addLocalFolder("./tests", '/tests');

/* zip.addLocalFolder("./vendor", '/vendor'); */

/* Agregamos los archivos */
zip.addLocalFile('.styleci.yml');
zip.addLocalFile('artisan');
zip.addLocalFile('composer.json');
zip.addLocalFile('composer.lock');
zip.addLocalFile('package-lock.json');
zip.addLocalFile('package.json');
zip.addLocalFile('phpunit.xml');
zip.addLocalFile('README.md');
zip.addLocalFile('server.php');
zip.addLocalFile('webpack.mix.js');

zip.toBuffer();

const now = new Date();
const date = now.toISOString().slice(0, 10).replace(/-/g, "");

zip.writeZip(`./zips/${date}.zip`);
