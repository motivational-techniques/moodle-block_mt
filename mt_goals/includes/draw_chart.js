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
 * This draws the line chart.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
var chartTitle, // The chart title.
    chartVAxis, // The vertical axis label.
    chartHAxis, // The horizontal axis label.
    chartData;  // The data table for chart.

// Set up the Google api.
google.load('visualization', '1', {packages: ['corechart']});
google.setOnLoadCallback(drawVisualization);

/**
 * Called by the server to initialize the necessary variables.
 *
 * @param object
 *            Y Some Moodle thing that gets passed by default
 * @param array
 *            incoming The data from the server
 */
function init(Y, incoming) {
    chartTitle = incoming.title;
    chartVAxis = incoming.vaxis;
    chartHAxis = incoming.haxis;
    chartData  = incoming.chartdata;
}

/**
 * Called when Google api is ready to draw the chart.
 */
function drawVisualization() {
    var data = new google.visualization.DataTable(chartData);

    var options = {
        title: chartTitle,
        colors: [ 'blue', 'red', 'yellow'],
        vAxis: {
            title: chartVAxis,
            format: '#',
            gridlines: { count: -1 },
            baseline: 0,
            titleTextStyle: {color: '#333'}
        },
        hAxis: {
            title: chartHAxis,
            baseline: 0,
            slantedText: true,
            slantedTextAngle: 30,
            titleTextStyle: {color: '#333'}
        },
        pointSize: 2
    };
    // Create and draw the visualization.
    var chart = new google.visualization.AreaChart(document.getElementById('visualization'))
    chart.draw(data, options);
}
