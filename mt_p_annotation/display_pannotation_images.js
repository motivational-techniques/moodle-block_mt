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
 * This renders the user selectable icons with the activity URLs.
 *
 * @package block_mt
 * @category blocks
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var annotationSessionKey, annotationLangStrings;

/**
 * Called to render the icons when the course page loads.
 *
 * @param object Y Some Moodle thing that gets passed
 * @param number course The course id
 * @param number userId The user id is passed, but not used
 * @param object selects The user selections from DB table
 * @param array langStrings The text to display with the images
 */
function getSessionInfo(Y, course, userId, selects, sessKey, langStrings) {

    var $imgDir = "../blocks/mt/mt_p_annotation/images/";
    var $courseId = course,
        $objectId;
    var iconSet = {}; // Used for ensuring only one icon set per activity.

    annotationLangStrings = langStrings;
    annotationSessionKey = sessKey;

    // Add z class to activity instances.
    $('.activityinstance a').attr("href", function() {
        var $href = $(this).attr("href");
        $objectId = $href.split('?id=')[1];

        if (iconSet[$objectId] || $objectId === undefined) {
            return;
        }
        iconSet[$objectId] = 1;

        $(this).addClass('z' + $objectId);
    });

    var $selections = JSON.parse(selects);

    // For each activity instance, add the icons, possibly user selected.
    $('.activityinstance a')
        .attr("class", function() {

            // Get activity.
            var $class = $(this).attr("class");
            if (!$class || $class.indexOf('quickeditlink') != -1) {
                // Duplicate activity id, don't want icons.
                return;
            }
            $objectId = $class.split('z')[1];

            var $currentSelection = findSelection($selections, $objectId);

            // Set up the tick image.
            var $tickImage = "tick.png";
            var $tickTitle = annotationLangStrings.done;
            var $tickOnClick = " onclick='changeImage(\"" + $objectId +
                "-1\", \"" + $objectId + "-2\", \"" +
                $objectId + "-3\" ,1," + $courseId + ")'";
            var $tickValue = "value='1'";

            // Set up the cross image.
            var $crossImage = "cross.png";
            var $crossTitle = annotationLangStrings.not;
            var $crossOnClick = " onclick='changeImage(\"" + $objectId +
                "-2\", \"" + $objectId + "-1\", \"" +
                $objectId + "-3\" ,2," + $courseId + ")'";
            var $crossValue = "value='2'";

            // Set up the in progress image.
            var $inProgImage = "inprog.png";
            var $inProgTitle = annotationLangStrings.ip;
            var $inProgOnClick = " onclick='changeImage(\"" + $objectId +
                "-3\", \"" + $objectId + "-1\", \"" +
                $objectId + "-2\" ,3," + $courseId + "," + ")'";
            var $inProgValue = "value='3'";

            // If activity has user selection, use coloured icon.
            switch ($currentSelection) {
                // Activity is selected as done.
                case "1":
                    $crossImage  = "grey" + $crossImage;
                    $crossTitle  = annotationLangStrings.click + " " + $crossTitle;
                    $inProgImage = "grey" + $inProgImage;
                    $inProgTitle = annotationLangStrings.click + " " + $inProgTitle;
                    break;

                // Activity is selected as in progress.
                case "3":
                    $tickImage  = "grey" + $tickImage;
                    $tickTitle  = annotationLangStrings.click + " " + $tickTitle;
                    $crossImage = "grey" + $crossImage;
                    $crossTitle = annotationLangStrings.click + " " + $crossTitle;
                    break;

                // Activity is selected as not done or no selection has been made.
                case "2":
                default:
                    $tickImage   = "grey" + $tickImage;
                    $tickTitle   = annotationLangStrings.click + " " + $tickTitle;
                    $inProgImage = "grey" + $inProgImage;
                    $inProgTitle = annotationLangStrings.click + " " + $inProgTitle;
                    break;
            }

            // Make the image icons for display.
            var $tickImageString = "  <img src='" + $imgDir + $tickImage +
                "' title='" + $tickTitle + "' id='" + $objectId + "-1' " +
                $tickValue + " height='20' width='20'" + $tickOnClick + ">";

            var $crossImageString = "  <img src='" + $imgDir + $crossImage +
                "' title='" + $crossTitle + "' id='" + $objectId + "-2' " +
                $crossValue + " height='20' width='20'" + $crossOnClick + ">";

            var $inProgImageString = "  <img src='" + $imgDir + $inProgImage +
                "' title='" + $inProgTitle + "' id='" + $objectId + "-3' " +
                $inProgValue + " height='20' width='20'" + $inProgOnClick + ">";

            // Add them to the activity URL.
            var $imageString = $tickImageString + $crossImageString + $inProgImageString;

            $class = "." + $class;
            $($class).parent().append($imageString);
        });
}

/**
 * Called to find a particular value in the user selections object.
 *
 * @param object selections The user selections from the DB
 * @param number objectId The activity id
 * @returns number
 */
function findSelection(selections, objectId) {

    var $selectedValue = 0;

    $.each(selections, function(key, val) {
        if (val.object == objectId) {
            $selectedValue = val.value;
        }
    });

    return $selectedValue;
}

/**
 * The javascript to handle user selection for the learning objects.
 *
 * Original file and author:
 *
 * imageSwapper.js
 * @author Biswajeet Mishra 2013
 *
 * Supports progress annotation.
 *
 * Relocated to this file by Paul Maguire.
 *
 * First alteration:
 *
 * Added start *PHIL LACHANCE, OCT 2014*
 * changed hardcoded path in JavaScript file
 * END
 *
 * Second alteration:
 *
 * Removed window.open, replaced with ajax call, added comments, moved to
 * display_annotation_images.js.
 * Ted Krahn June 2019
 *
 * @param string img1 Image 1
 * @param string img2 Image 2
 * @param string img3 Image 3
 * @param number mode
 * @param number courseID The course id
 */
function changeImage(img1, img2, img3, mode, courseID) {

    var imgDir = '../blocks/mt/mt_p_annotation/images/', img;

    // Call the server to update the DB table.
    $.ajax({
        url : "../blocks/mt/mt_p_annotation/annotation_entry.php",
        type : "GET",
        data : {
            "item":    img1,
            "course":  courseID,
            "sesskey": annotationSessionKey
        },
        success : function(data) {
            console.log(data);
        },
        error : function(data) {
            console.log(data);
        }
    });

    // Swap the icon image.
    // Activity is complete.
    if (mode == 1) {
        img = document.getElementById(img1);
        img.src    = imgDir + 'tick.png';
        img.title  = annotationLangStrings.done;
        img.height = '20';
        img.width  = '20';
        img.value  = '10';

        img = document.getElementById(img2);
        img.src    = imgDir + 'greycross.png';
        img.title  = annotationLangStrings.click + ' ' + annotationLangStrings.not;
        img.height = '20';
        img.width  = '20';
        img.value  = '10';

        img = document.getElementById(img3);
        img.src    = imgDir + 'greyinprog.png';
        img.height = '20';
        img.width  = '20';
        img.value  = '10';
        img.title  = annotationLangStrings.click + ' ' + annotationLangStrings.ip;
    }
    // Activity is not complete.
    else if (mode == 2) {
        img = document.getElementById(img1);
        img.src    = imgDir + 'cross.png';
        img.title  = annotationLangStrings.not;
        img.height = '20';
        img.width  = '20';
        img.value  = '10';

        img = document.getElementById(img2);
        img.src    = imgDir + 'greytick.png';
        img.title  = annotationLangStrings.click + ' ' + annotationLangStrings.done;
        img.height = '20';
        img.width  = '20';
        img.value  = '10';

        img = document.getElementById(img3);
        img.src    = imgDir + 'greyinprog.png';
        img.height = '20';
        img.width  = '20';
        img.value  = '10';
        img.title  = annotationLangStrings.click + ' ' + annotationLangStrings.ip;
    }
    // Activity is in progress.
    else {
        img = document.getElementById(img1);
        img.src    = imgDir + 'inprog.png';
        img.title  = annotationLangStrings.ip;
        img.height = '20';
        img.width  = '20';
        img.value  = '10';

        img = document.getElementById(img2);
        img.src    = imgDir + 'greytick.png';
        img.title  = annotationLangStrings.click + ' ' + annotationLangStrings.done;
        img.height = '20';
        img.width  = '20';
        img.value  = '10';

        img = document.getElementById(img3);
        img.src    = imgDir + 'greycross.png';
        img.title  = annotationLangStrings.click + ' ' + annotationLangStrings.not;
        img.height = '20';
        img.width  = '20';
        img.value  = '10';
    }
}
