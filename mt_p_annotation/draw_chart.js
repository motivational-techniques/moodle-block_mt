// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This script is called to render the pie chart.
 *
 * @package block_mt
 * @category blocks
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var chartTitle,   // The chart title.
    chartTooltip, // The chart tool tip.
    wwwroot,      // The server web root for URLs.
    userId,       // The user id for URLs.
    courseId,     // The course id for URLs.
    chartData;    // The data table for chart.

// Set up the Google api.
google.load('visualization', '1', { packages: ['corechart'] });
google.setOnLoadCallback(drawVisualization);

/**
 * Called by the server to initialize the necessary variables.
 *
 * @param object Y Some Moodle thing that gets passed by default
 * @param array incoming The data from the server
 */
function init(Y, incoming) {

    chartTitle   = incoming.title;
    chartTooltip = incoming.tooltip;
    wwwroot      = incoming.wwwroot;
    userId       = incoming.userid;
    courseId     = incoming.courseid;
    chartData    = incoming.chartdata;
}

/**
 * Called when Google api is ready to draw the chart.
 */
function drawVisualization() {

    var url = '/blocks/mt/mt_p_annotation/get_activities.php?courseid=';

    // Create and populate the data table.
    var data = new google.visualization.DataTable(chartData);

    // Create and draw the visualization.
    var chart = new google.visualization.PieChart(document.getElementById('visualization'));
    var options = {
        title: chartTitle,
        slices: [ { color: 'green' },
                  { color: 'red' },
                  { color: 'yellow' } ],
        tooltip: { trigger: 'selection' }
    };

    chart.setAction({
        id: 'sample',          // An id is mandatory for all actions.
        text: chartTooltip,    // The text displayed in the tooltip.
        action: function() {   // When clicked, the following runs.

            selection = chart.getSelection();

            // Change the page to show the list of learning objects.
            switch (selection[0].row) {
                // Completed activities.
                case 0:
                    window.location.href = wwwroot + url + courseId + '&value=1';
                    break;

                // Not completed activities.
                case 1:
                    window.location.href = wwwroot + url + courseId + '&value=2';
                    break;

                // In progress activities.
                case 2:
                    window.location.href = wwwroot + url + courseId + '&value=3';
                    break;
            }
        }
    });

    chart.draw(data, options);
}
