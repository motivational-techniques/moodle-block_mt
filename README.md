# Motivational Techniques #

This block plugin provides 5 domain and content independent features that are
designed to provide students more motivation in a course.

The plugin consists of 5 separate features. Each feature is independent of the
others, allowing them to be used individually or in conjunction with one another.
Which features are available through the block is configurable through the
administrator options.

The first feature is Awards, which gives students gold, silver, and bronze
awards based on their performance in certain areas of the course such as forum
posts and assignment grades. The thresholds for the different award categories
is adjustable from the administrator options.

The second feature is Goals, which provides student with the ability to set goals
and monitor their progress toward those goals. Goals that can be set for
assignments and quizzes include the grade the student wishes to achieve and the
date by which to complete the assignment. Goals can also be set for overall
grade, for rankings against other students, and for achieving awards. Course
progress against the goals can be monitored by comparing against actual results
on a line graph.

The third feature is Progress Annotation, which tracks the completion status of
the activities in the course. There are 3 icons added to each activity
link on the course page that are selectable to indicate the completion
status of that activity. There is also a pie chart that shows the percentages of
activities that are complete, in progress, and not completed. Clicking a slice
of the chart shows a list of the activities with that completion status.

There is also a Progress Timeline feature, which shows the student progress
through the course as a line chart. The feature considers gradable milestones
in the course such as assignments and quizzes. An ideal timeline is set by the
instructor and shown on the chart. Each student can view how their progress
timeline compares against the ideal timeline and the average student timeline.

Finally there is a Rankings feature, which ranks the students according to their
performance in certain areas of the course such as forum posts and assignment
grades. The rank is derived from a comparison with other students, where
students are ranked higher for achieving higher grades.


### Installation ###

The plugin can be installed from the Moodle plugin repository. When logged in as
admin, go to Site administration > Plugins > Install plugins and click the button
labeled "Install plugins from the Moodle plugins directory."
OR
The plugin can be installed from its zip file archive. First, download the
archive from {need URL}. Then, when logged in as admin, go to Site
administration > Plugins > Install plugins and use the file picker to upload the
zip file.
OR
The plugin can be installed manually at the server. All the plugin files will
need to be placed into the /path/to/moodle/blocks/mt directory.

See https://docs.moodle.org/37/en/Installing_plugins#Installing_a_plugin for
more details about these installation methods.

After installing, (as admin) navigate to Settings > Site administration >
Notifications, which will show a message saying the plugin is to be installed.


### Adding the block to a course. ###

As an admin, click on the gear icon at the top right of the course page.
Click Turn editing on.

On the bottom left of the course page click on Add a block
Select MT - Motivational Techniques
click on the gear icon at the top right of the course page.
Click Turn editing off.

To select specific Motivational Techniques for the course, click on the Administration link within the Motivational Techniques block.
Select the Motivational Technique to be enabled for the course.
Click Save Changes


### Report a bug ###

If you find a bug in the program or want to request a feature be added to the
program, create an issue at
https://github.com/motivational-techniques/moodle-block_mt/issues.


### Scheduled Tasks/Cron Job ###

Three of the five features in this block require that the Moodle 'cron' process
is set up. The Awards, Goals, and Rankings features use a Moodle scheduled task
to generate the necessary data for the features. Moodle recommends that the cron
process is set up for all installations, as Moodle itself relies on the process,
so your Moodle installation likely already has this process enabled. However, if
you need to set up cron, see https://docs.moodle.org/37/en/Cron. Alternatively,
the cron script can be run manually from the command line.


### Plugin options and management ###

#### Plugin global settings ####

The Motivational Techniques plugin has a few global settings that can be
adjusted by the administrator. When the plugin is first installed, these settings
are shown and can be adjusted, but also have default values that can be used.
After the plugin is installed, these settings can be accessed from
Site administration > Plugins > Plugins overview, which shows a long list of all
the installed plugins. Search for the MT - Motivational Techniques plugin and
click settings. 

There are 4 settings that can be altered. The first is online cutoff time, which
is the maximum time value used when calculating a student's time spent online for
the Rankings feature. The second also affects the Rankings feature and determines
the number of days without activity before a student is considered inactive.

The third and fourth settings are for regenerating the Rankings and Awards data.
Having these values checked causes the ranking and award goals to be wiped out
every time the scheduled task is run. Unless the data needs to be regenerated, it
is best to leave these settings at their default (unchecked) value.

#### Feature specific settings ####

The Awards and Timeline features have settings that can be altered. These
settings are available to teachers and administrators, but not students. The
Awards settings all come with default values and do not necessarily need to be
adjusted. The Timeline settings are for the ideal timeline through the course and
need to be manually entered as there are no default values.

The Awards and Rankings features have milestone related sub-features that require
that the Timeline settings page be viewed and saved. The Timeline feature itself
does not need to be enabled, but its administration page must be viewed and saved
in order for the course milestones to show up in milestone related sub-features
of the Awards and Rankings features.

#### Scheduled task run frequency ####

The Awards, Rankings, and Goals features of the plugin also require the Moodle
'cron' process is set up. By default, the plugin's scheduled task will run every
minute to update the data for those 3 features. The frequency for running the
update can be changed by an administrator through Moodle's scheduled task
interface, which can be found at Site administration > Server > Scheduled tasks.
Clicking the settings icon will bring up the interface to change the schedule.
The task can also be run on demand by clicking the 'Run now' link.

#### Forum settings ####

To track read posts, it is necessary that the global forum settings allow posts
to be tracked as read or unread. The Moodle defaults work fine, but these may get
changed. The global forum settings must be accessed as administrator and can be
found under Site administration > Plugins > Forum. The "Track unread posts"
option must be selected (default). It is also possible to select "Manual read
message marking" (not default), which changes the way posts are marked as being
read. The "Read after days" option will also affect when posts are considered
read.

The Awards and Rankings features make use of forum posts, including how many
posts a student makes, how many posts a student reads, and the rating of the
posts. The features that use read posts require that the student have forum
tracking enabled in their preferences. Students can enable forum tracking by
clicking their name in the top right corner of any Moodle page and going to
Preferences > Forum preferences, and ensuring that forum tracking is set to Yes.

When adding a forum activity module to a course, there are some options that
need to be set correctly for certain plugin features to work. Under Subscription
and tracking, the Read tracking option must be enabled for read and unread posts
to be tracked in that forum. For posts to be rated, the Ratings options need to
be set up. There are different aggregate types and different rating scales, any
of which will allow posts to be rated and give the post rating related features
data.


### Details Of And How To Use Each Feature ###

#### Awards Feature ####

The Awards feature enables students to view their awards in the course.
Clicking the Awards link in the block shows the interface for the Awards
feature, which contains several categories, each of which contains links to
further explore the awards for that category. The first category is Personal
Achievements, which lists a summary and details of the awards for the student.
The awards by grade category has 3 links to view awards for grades by
assignment, by quiz, and overall. The Milestones category contains a single link
for viewing awards for completing milestones within a certain time. The amount
of time online category has awards for the amount of time spent online in the
current month as well as the average time online over all the months enrolled
with a breakdown by month. There is also a participation category that allows
students to view their awards for forum posts, reading others posts, and post
ratings, each of which can be seen for the current month or an average over all
months enrolled. There is also a goals category, but this has not yet been
implemented.

The Moodle administrator can modify the default settings for the online cutoff
time in minutes and the time in days before a user is considered inactive. These
settings are available from the Site administration > Plugins > Plugins overview
page. Search through the list of plugins to find MT - Motivational Techniques
and click settings. These settings can also be altered when the plugin is
installed.

The teacher can set the values for the weighing of awards and for the values
needed to attain an award. Students can choose whether or not they want their
name displayed in the awards listing. These settings are accessible through the
administration/options link in the block.

#### Goals Feature ####

The Goals feature enables students to set goals and monitor their progress
towards those goals. The goals interface contains numerous links for setting
various goals and viewing progress. The All Goals link shows all the goals with
status a student has set. Students can also view their progress through the
course as a line chart, which will show their progress against their goals or
their progress against the class average. A student can set a goal for overall
course grade as well as for individual quiz and assignment grades. Assignments
and quizzes also have the option to set a goal for the date by which the
assessment should be completed. Quizzes also allow students to set goals for
start dates. The final two categories are ranking and awards, where a student
can set goals for their ranking against other students and for achieving certain
award levels.

#### Progress Annotation Feature ####

The Progress Annotation feature enables students to keep track of which learning
activities they have completed, are still working on, and have not completed.
The feature adds a set of three icons to each learning activity on the main
course page. The icons include a green check mark, a red X, and a yellow diamond.
If the check mark is clicked, the module is considered complete, while the X
indicates incomplete (default), and the diamond indicates in progress. These
icons are not changed automatically and must be clicked by the student. Along
with the activity icons on the course page, the student can click the link in
the block to view a pie chart of their completion status in the course. Each
category is represented by a slice of the chart. Clicking any slice of the pie
brings up a tip box showing the number of learning activities in that category.
The tip box also includes a link that will produce a list of the learning
activities in that category.

#### Progress Timeline Feature ####

The Progress Timeline feature enables students to view their progress in the
course as a line chart. The chart shows the time in weeks on one axis and
milestones (gradeable assessments) on the other axis. The teacher user must set
up the ideal week values (0-26) for each milestone in the course before
any milestones will show on the chart. This can be done by clicking the
Administration link in the block. The default value is 0, which excludes the
milestone from the chart. When viewed, the chart will show the ideal
timeline as defined by the teacher, an average timeline for all students who
have completed that milestone, and a timeline for the student who is viewing the
chart.

#### Rankings Feature ####

The Rankings feature enables students to view their rankings in the course.
Clicking the Rankings link in the block shows the interface for the Rankings
feature, which contains several categories, each of which contains link to
further explore the rankings for that category. The first category is all
rankings, which shows a list of all the rankings the student has achieved. Next
is the grades category, which has 3 links to view rankings for grades by
assignment, by quiz, and overall. The achievements category has a link that shows
all the awards each student has achieved so far in the course. The amount
of time online category has rankings for the amount of time spent online in the
current month as well as the average time online over all the months enrolled
with a breakdown by month. There is also a participation category that allows
students to view their rankings for forum posts, reading others posts, and post
ratings, each of which can be seen for the current month or an average over all
months enrolled. Finally, there is a milestones category that ranks students by
the time it took them to reach each milestone or the pace at which the milestones
are being completed.

The Moodle administrator can modify the default settings for the online cutoff
time in minutes and the time in days before a user is considered inactive. These
settings are available from the Site administration > Plugins > Plugins overview
page. Search through the list of plugins to find MT - Motivational Techniques
and click settings. These settings can also be altered when the plugin is
installed.

Students can choose whether or not they want their name displayed in the rankings.
This setting is accessible through the options link in the block.


### License ###

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
