<?php

require_once "lib/headerphp.php";
require "../data/events.php";
require_once "../lib/app.php";

$fmt = 'Y-m-d\TH:i:s';


?>
<html>
<head>
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.min.css">
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="../css/fullcalendar.min.css">
    <script src="../js/jquery-1.12.1.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/moment.min.js"></script>
    <script src="../js/fullcalendar.min.js"></script>

    <script src="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js"></script>
    <link rel="stylesheet" href="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css">

<style>

.data {
    display: inline-block;
}

.pid {
    font-size: 80%;
    color: #800;
    width: 3.5em;
    display: inline-block;
    text-align:center;
}

.time {
    font-size: 80%;
    color: #888;
}

.title {
    font-style: italic;
}

.notalk {
    color: #888;
}

ul {
    list-style-type: none;
}

h3.chair {
    margin: 0;
    padding: 1em 0 0.5em 0;
    font-size: 100%;
}


</style>


</head>
<body>
    <h1>Programme</h1>
    <div id='calendar'></div>

<script>
$(document).ready(function() {

    // special events
    var evtSrcSpcl = { events: [
    <?php
    foreach ($specialevents as $se) {
        print <<<EOT
        {
            start: '{$se->start->format($fmt)}',
            end:   '{$se->end->format($fmt)}',
            title: '{$se->name}',
            description: '{$se->description}'
        },
EOT;
    }
?>
    ], color: '#ffff88', textColor: 'black' , borderColor:'#aaaa00' };

    // Enter recurring breaks here (PHP code following)
    var evtSrcsBrks = { events: [
    <?php
    foreach ($breaks as $b) {
        print <<<EOT
        {
            start: '{$b->start->format($fmt)}',
            end:   '{$b->end->format($fmt)}',
            title: '{$b->name}'
        },
EOT;
    }
    ?>
], color: '#88ff88', textColor: 'black', borderColor:'#000088'};

    // Enter Plenary Talks
    var evtSrcsCTalks = { events: [
    <?php
    foreach($presentations as $p) {
        #print "//{$p->id}";
        if ($p->is_plenary) {
            print "        {
                start: '{$p->start->format($fmt)}',
                end:   '{$p->end->format($fmt)}',
                title: '{$p->name}',
                ptitle: '{$p->presentationTitle}',
                name: '{$p->name}',
                description: " . json_encode($p->abstract) . "
            },\n";
        }
    }
    ?>

    ], color:'#ff8888', textColor:'black', borderColor:'#008800' };

    // Enter Parallelsessions
    var evtSrcsPTalks = { events: [
    <?php
    $fmt = 'Y-m-d\TH:i:s';
    foreach($presentations as $p) {
        #print "//{$p->id}";
        if (!$p->is_plenary) {
            print "        {
                start: '{$p->start->format($fmt)}',
                end:   '{$p->end->format($fmt)}',
                title: '{$p->name}',
                ptitle: '{$p->presentationTitle}',
                name: '{$p->name}',
                description: " . json_encode($p->abstract) . "
            },\n";
        }
    }
    ?>

        ], color:'#ff8888', textColor:'black', borderColor:'#008800' };


    $('#calendar').fullCalendar({
        weekends: false, // will hide Saturdays and Sundays

        height: 'auto',

        defaultDate: '2016-09-05',
        defaultView: 'agendaWeek',
        editable: false,

        header: {
            left: '',
            center: '',
            right: ''
        },

        views: {
            agenda: {
                scrollTime: '08:00:00',
                slotLabelFormat: 'H:mm',
                minTime: "08:00:00",
                maxTime: "19:30:00",
                columnFormat: "ddd D",
                slotDuration: '00:20:00'
            }
        },

        eventRender: function(event, element) {
/*            if (event.ptitle) {
                element.find(".fc-content").append("<div class='fc-ptitle'>"+event.ptitle+"</div>");
            }*/
            if (event.ptitle) {
                var txt = "<b>" + event.ptitle + "</b><br>\n";
                txt += "<i>" + event.name + "</i><br><br>\n";
                txt += event.description;
            }
            else if (event.description) {
                var txt = event.description;
            }
            else { var txt="";}
            element.qtip({
                content: {
                    text: txt
                },
                position: {
                    viewport: $(window)
                }
            });
        },

        allDaySlot: false,
        eventSources: [evtSrcsBrks, evtSrcsCTalks, evtSrcsPTalks, evtSrcSpcl],
        timeFormat: 'H:mm',
    })
});
</script>
