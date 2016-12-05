<?php
require_once "lib/app.php";
$db = open_db($db_address_abs);

require "data/events.php";


$fmt = 'Y-m-d\TH:i:s';


?>


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
    ], color: '#ff8888', textColor: 'black'};

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

    ], color:'#88ff88', textColor:'black', borderColor:'#008800' };

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

        ], color:'#88ff88', textColor:'black', borderColor:'#008800' };


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
            if (event.ptitle) {
                element.find(".fc-content").append("<div class='fc-ptitle'>"+event.ptitle+"</div>");
            }
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
