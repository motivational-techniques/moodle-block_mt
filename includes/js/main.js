$(document).ready(function() {
    $("#highlight").css('background-color', 'yellow');
    $("#checkActive").button();
    $("#mainMenu").button();
    $("#gradeAssignmentMenu").button();
    $("#milestoneMenu").button();
    $("#gradeQuizMenu").button();
    $("#myTable").tablesorter({
        theme : 'blue',
        widgets : [ 'zebra' ]
    });
});
