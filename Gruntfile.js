module.exports = function(grunt) {
	grunt
			.initConfig({
				pkg : grunt.file.readJSON('package.json'),

				// Update copyright and version number for version.php
				'string-replace' : {
					dest : {
						files : {
							'dest/' : 'version.php'
						},
						options : {
							replacements : [
									{
										pattern : /version = [0-9]{10}/,
										replacement : 'version = <%= grunt.template.today(\'yyyymmddHH\') %>'
									},
									{
										pattern : /copyright [0-9]{4}/,
										replacement : 'copyright <%= grunt.template.today(\'yyyy\') %>'
									},
									{
										pattern : /release = \'v([a-z0-9.-]*)\'/,
										replacement : 'release = \'v3.4-r<%= pkg.version %>-<%= pkg.build %>\''
									} ]
						}
					}
				},
				// Copy updated version.php file to root
				copy : {
					// copy updated version file
					'update-version' : {
						files : [ {
							src : [ 'dest/version.php' ],
							dest : 'version.php',
							filter : 'isFile'
						} ]
					},
					'update-classes' : {
						files : [ {
							expand : true,
							src : [ 'classes/**' ],
							dest : '<%= pkg.copyDirectory %>',
						} ]
					},
					'update-lang' : {
						files : [ {
							expand : true,
							src : [ 'lang/**' ],
							dest : '<%= pkg.copyDirectory %>',
						} ]
					},
					'update-includes' : {
						files : [ {
							expand : true,
							src : [ 'includes/**' ],
							dest : '<%= pkg.copyDirectory %>',
						} ]
					},
					'update-tests' : {
						files : [ {
							expand : true,
							src : [ 'tests/**' ],
							dest : '<%= pkg.copyDirectory %>',
						} ]
					},
					'update-mt_awards' : {
						files : [ {
							expand : true,
							src : [ 'mt_awards/**' ],
							dest : '<%= pkg.copyDirectory %>',
						} ]
					},
					'update-mt_goals' : {
						files : [ {
							expand : true,
							src : [ 'mt_goals/**' ],
							dest : '<%= pkg.copyDirectory %>',
						} ]
					},
					'update-mt_rankings' : {
						files : [ {
							expand : true,
							src : [ 'mt_rankings/**' ],
							dest : '<%= pkg.copyDirectory %>',
						} ]
					},
					'update-travis' : {
						files : [ {
							expand : true,
							src : [ '*.php', '*.md', '*.yml', '*.json', '*.js', '.*.yml'],
							dest : '<%= pkg.testDirectory %>',
							filter : 'isFile'
						}, {
							src : [ '<%= mtTravisSource %>' ],
							dest : '<%= pkg.testDirectory %>'
						} ]
					},
					'update-dev' : {
						files : [ {
							expand : true,
							src : [ '*.php', '*.md', '!version.php' ],
							dest : '<%= pkg.copyDirectory %>',
							filter : 'isFile'
						}, {
							src : [ '<%= mtTravisSource %>' ],
							dest : '<%= pkg.copyDirectory %>'
						} ]
					}
				},
				// Increment buildnumber
				buildnumber : {
					options : {
						field : 'build',
						dontChangeIndentation : false
					},
					files : [ 'package.json' ]
				},
				// Increment version number
				bumpup : 'package.json',
				// Compress files into mt folder for deployment as Moodle block
				// plugin
				compress : {
					main : {
						options : {
							archive : 'mt.zip'
						},
						files : [ {
							src : [ '*.php', '*.md' ],
							dest : 'mt/',
							filter : 'isFile'
						}, {
							src : [ '<%= mtSource %>' ],
							dest : 'mt/'
						} ]
					}
				},
				// Clean up the temporary files
				clean : {
					endBuild : [ 'dest/' ],
					startBuild : [ 'mt.zip' ]
				},
				// Source files and directories to copy and compress
				mtSource : [ 'classes/**', 'db/**', 'generate/**', 'includes/**',
					'lang/**', 'mt_awards/**', 'mt_goals/**',
					'mt_p_annotation/**', '!mt_p_annotation/testing.txt',
					'mt_p_timeline/**', '!mt_p_timeline/testing.txt',
					'mt_rankings/**'
					 ],
				mtTravisSource : [ 'classes/**', 'db/**', 'generate/**', 'includes/**',
					'lang/**', 'mt_awards/**', 'mt_goals/**',
					'mt_p_annotation/**', 
					'mt_p_timeline/**', 
					'mt_rankings/**', 'tests/**'
					 ]

			});

	grunt.loadNpmTasks('grunt-string-replace');
	grunt.loadNpmTasks('grunt-better-build-number');
	grunt.loadNpmTasks('grunt-bumpup');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-clean');

	grunt.registerTask('default', [ 'clean:startBuild', 'buildnumber',
			'string-replace', 'copy:update-version', 'compress:main',
			'clean:endBuild' ]);
}