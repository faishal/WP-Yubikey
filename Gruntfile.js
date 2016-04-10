module.exports = function( grunt ) {

    // Project configuration

    grunt.initConfig( {
        pkg: grunt.file.readJSON( 'package.json' ),
        makepot: {
            main: {
                options: {
                    cwd: './',
                    domainPath: 'languages',
                    mainFile: 'wp-yubikey.php',
                    potFilename: 'wp-yubikey.pot',
                    type: 'wp-plugin',
                    potHeaders: true
                }
            }
        }
    } );

    // Load tasks
    require( 'load-grunt-tasks' )( grunt );

    // Register tasks
    grunt.registerTask( 'default', ['makepot'] );

    grunt.registerTask( 'wp', ['makepot'] );

    grunt.util.linefeed = '\n';
};
