<?php

$admin_modules = array(
    ["index.php", "Overview", ["guest", "admin", "finanz", "session_head", "chief_orga", "orga"]],
    ["add_admin.php", "Edit Admin Users", ["admin"]],
    ["edit.php", "Edit Entries", ["admin"]],
    ["add_user.php", "Add user", ["admin"]],
    ["payment_np.php", "Manage Payment", ["admin", "finanz"]],
    ["payment_all.php", "Manage Payment (all)", ["admin", "finanz"]],
    ["assign_sessions.php", "Create/Assign Sessions", ["admin", "chief_orga"]],
    ["categorize_presentations.php", "Categorize Presentations", ["admin", "chief_orga"]],
    ["assign_presentations.php", "Assign Presentations to Sessions", ["admin", "chief_orga", "orga"]],
    ["manage_session.php", "Manage My Session", ["admin", "chief_orga", "orga"]],
    ["view_plan.php", "-> Prelim. Program Plan", ["admin", "chief_orga", "orga"]],
    ["view_posters.php", "PosterSessions", ["admin"]],
    ["submission_report.php", "SubmissionReport", ["admin", "chief_orga", "orga"]],
    ["get_csv.php", "get CSV data", ["admin"]]

    #["test2.php", "testing", ["admin"]]

);

$special_power_roles = ["admin", "chief_orga"]; # those can see all the details about the session handling process

$get_acl = [];

foreach ($admin_modules as $value) {
    $site = $value[0];
    $name = $value[1];
    $acl  = $value[2];

    $get_acl[$site] = $acl;

    #print_r($acl);
    #print_r($USER->role);
    #print_r( in_array($USER->role, $acl)?"4":"0");
}

function printmenu() {
    global $admin_modules;
    global $USER;
    foreach ($admin_modules as $value) {
        $site = $value[0];
        $name = $value[1];
        $acl  = $value[2];
        if ( in_array($USER->role, $acl) ) {
            $act = ($site == basename($_SERVER["SCRIPT_NAME"]) ? " act'" : "'    " );
            print "        <a class='menu$act href=\"$site\">$name</a>\n";
        }

    }
}
?>
