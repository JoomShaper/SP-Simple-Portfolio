const { src, dest, series } = require('gulp');
const minifyCSS = require('gulp-minify-css');
const zip = require('gulp-zip');
const clean = require('gulp-clean');
var uglify = require('gulp-uglify-es').default;

// Create a build
function cleanBuild() {
    return src('./build', {read: false, allowEmpty: true})
        .pipe(clean());
}

function cleanZip() {
    return src('./com_spsimpleportfolio.zip', {read: false, allowEmpty: true})
        .pipe(clean());
}

function copySite() {
    return src(['./components/com_spsimpleportfolio/**/*.*', '!./components/com_spsimpleportfolio/assets/reactjs/**/*.*', '!./components/com_spsimpleportfolio/assets/js/engine.js.map'])
        .pipe(dest('build/site'));
}

function copyAdmin() {
    return src(['./administrator/components/com_spsimpleportfolio/**/*.*', '!./administrator/components/com_spsimpleportfolio/assets/reactjs/**/*.*', '!./administrator/components/com_spsimpleportfolio/installer.script.php', '!./administrator/components/com_spsimpleportfolio/sppagebuilder.xml', '!./administrator/components/com_spsimpleportfolio/assets/js/engine.js.map'])
        .pipe(dest('build/admin'));
}

function copy_lang_site() {
    return src('./language/en-GB/en-GB.com_spsimpleportfolio.ini')
        .pipe(dest('build/language/site/en-GB'));
}

function copy_lang_admin() {
    return src(['./administrator/language/en-GB/en-GB.com_spsimpleportfolio.ini', './administrator/language/en-GB/en-GB.com_spsimpleportfolio.sys.ini'])
        .pipe(dest('build/language/admin/en-GB'));
}

function copy_modules() {
    return src('./modules/mod_spsimpleportfolio/**/*.*')
        .pipe(dest('build/modules/mod_spsimpleportfolio'));
}

function copy_modules_lang() {
    return src('./language/en-GB/en-GB.mod_spsimpleportfolio.ini')
        .pipe(dest('build/modules/mod_spsimpleportfolio/language'));
}

function copy_installer() {
    return src(['./administrator/components/com_spsimpleportfolio/installer.script.php', './administrator/components/com_spsimpleportfolio/spsimpleportfolio.xml'])
        .pipe(dest('build'));
}

function minify_admin_css() {
    return src(['./build/admin/assets/css/*.css'])
        .pipe(minifyCSS())
        .pipe(dest('./build/admin/assets/css/'));
}

function minify_site_css() {
    return src(['./build/site/assets/css/*.css'])
        .pipe(minifyCSS())
        .pipe(dest('./build/site/assets/css/'));
}

function minify_site_js() {
    return src(['./build/site/assets/js/*.js', '!build/admin/assets/js/featherlight.min.js', '!build/admin/assets/js/jquery.shuffle.modernizr.min.js'])
        .pipe(uglify())
        .pipe(dest('./build/site/assets/js/'));
}

function minify_admin_js() {
    return src(['./build/admin/assets/js/*.js'])
        .pipe(uglify())
        .pipe(dest('./build/admin/assets/js/'));
}

function makeZip() {
    return src('./build/**/*.*')
      .pipe(zip('com_spsimpleportfolio.zip'))
      .pipe(dest('./'));
}

exports.copy = series(cleanBuild, cleanZip, copySite, copyAdmin, copy_lang_site, copy_lang_admin, copy_modules, copy_modules_lang, copy_installer);
exports.minify = series(minify_admin_css, minify_site_css, minify_site_js, minify_admin_js);
exports.default = series(exports.copy, exports.minify, makeZip, cleanBuild);