'use strict';

// Generic
const gulp			= require( 'gulp' );
const concat		= require( 'gulp-concat' );
const sourcemaps	= require( 'gulp-sourcemaps' );

// Stylesheets
const sass			= require( 'gulp-sass' );
const autoprefixer	= require( 'gulp-autoprefixer' );

// Javascripts
const babel			= require( 'gulp-babel' );
const minify		= require( 'gulp-babel-minify' );

// Image processing
const cache				= require( 'gulp-cache' );
const imagemin			= require( 'gulp-imagemin' );
const imageminJpegtran	= require( 'imagemin-jpegtran' );
const imageminPngquant	= require( 'imagemin-pngquant' );
const imageminZopfli	= require( 'imagemin-zopfli' );
const imageminMozjpeg	= require( 'imagemin-mozjpeg' ); // Might need to run 'brew install libpng'
const imageminGiflossy	= require( 'imagemin-giflossy' );

// Input / output paths
const paths = {
	input: {
		stylesheets_include: './source/scss',
		stylesheets: {
			'backend.css': [ './source/scss/backend/index.scss' ],
		},
		javascripts: {
			'backend.js': [ './source/js/backend/**/*.js' ],
		},
		images: [ './source/images/**/*' ]
	},
	output: {
		stylesheets: './assets/css',
		javascripts: './assets/js',
		images: './assets/images'
	}
};

/**
 * Register multiple tasks running the same action
 *
 * @param	{String|Array}	taskNames
 * @param	{Callable}		action
 * @return	{Void}
 */
function gulpTasker( taskNames, action ) {

	var tasks = Array.isArray( taskNames ) ? taskNames : taskNames.replace( ', ', ',' ).split( ',' );

	for ( let i = 0; i < tasks.length; i++ ) {
		const task = tasks[ i ];

		gulp.task( task, () => {
			const input	= paths.input[ task ];
			let output	= paths.output[ task ];

			if ( Array.isArray( input ) ) {
				return action( input, output );
			}

			let promises = [];
			for ( let name in input ) {
				promises.push( action( input[ name ], output, name ) );
			}
			return Promise.all( promises );

		} );
	}
}

/**
 * Watch a task and files related
 *
 * @param	{String}	task
 * @return	{Void}
 */
function gulpWatcher( task ) {
	const input	= paths.input[ task ];

	if ( Array.isArray( input ) ) {
		gulp.watch( input, [ task ] );
	} else {
		for ( let name in input ) {
			gulp.watch( input[ name ], [ task ] );
		}
	}
}


/**
 * Process stylesheet inputs
 */
gulpTasker( 'stylesheets', ( source, path, file ) => {
	return gulp.src( source )

		// Process Sass compiler
		.pipe( sass( {
			includePaths: [ paths.input.stylesheets_include ],
			outputStyle: 'compressed'
		} ).on( 'error', sass.logError ) )

		// Run autoprefixer for older browser support
		.pipe( autoprefixer() )

		// Combine files
		.pipe( concat( file ) )

		// Save output to path
		.pipe( gulp.dest( path ) );
} );

/**
 * Process javascript inputs
 */
gulpTasker( 'javascripts', ( source, path, file ) => {
	return gulp.src( source )

		// Run Babel for older browser support
		.pipe( babel( {
			presets: [
				[ '@babel/preset-env' ]
			]
		} ) ).on( 'error', function( error ) {
			console.error( error.toString(), '\n\b', error.codeFrame );
			this.emit( 'end' );
		} )

		// Combine files
		.pipe( concat( file ) )

		// Minify
		.pipe( minify() )

		// Save output to path
		.pipe( gulp.dest( path ) );
} );

/**
 * Process image inputs
 */
gulpTasker( 'images', ( source, path, file ) => {
	return gulp.src( source )
		.pipe(
			cache(
				imagemin( [
					// png
					imageminPngquant( {
						speed: 1,
						quality: [ 0.95, 1 ] // Lossy settings
					} ),
					imageminZopfli( {
						more: true
						// iterations: 50 // Very slow but more effective
					} ),
					// gif - very light lossy, use only one of gifsicle or Giflossy
					// imagemin.gifsicle( {
					// 	interlaced: true,
					// 	optimizationLevel: 3
					// } ),
					imageminGiflossy( {
						optimizationLevel: 3,
						optimize: 3, // Keep-empty: Preserve empty transparent frames
						lossy: 2
					} ),
					// svg
					imagemin.svgo( {
						plugins: [ {
							removeViewBox: false
						} ]
					} ),
					// jpg - lossless
					imageminJpegtran( {
						progressive: true
					} ),
					//jpg - very light lossy, use vs jpegtran
					imageminMozjpeg( {
						quality: 90
					} )
				] )
			)
		)

		// Save output to path
		.pipe( gulp.dest( path ) );
} );

/**
 * Watch paths and process on change
 */
gulp.task( 'watch', [ 'default' ], () => {
	gulp.watch( [ paths.input.stylesheets_include + '/**/*.scss' ], [ 'stylesheets' ]);
	gulpWatcher( 'stylesheets' );
	gulpWatcher( 'javascripts' );
} );

/**
 * Handle the default gulp task
 */
gulp.task( 'default', [ 'stylesheets', 'javascripts', 'images' ] );


