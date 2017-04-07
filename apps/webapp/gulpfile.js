var gulp = require('gulp'),
	less = require('gulp-less'),
	cssmin = require('gulp-minify-css');
		

var paths = {
  less: './www/**/*.less'
};

gulp.task('less', function () {
    gulp.src('www/css/*.less') 
        .pipe(less())
        .pipe(cssmin())
        .pipe(gulp.dest('www/css/')); 
});

gulp.task('watch', function () {
    gulp.watch('www/css/*.less', ['less']);
});
gulp.task('default',['less'])
