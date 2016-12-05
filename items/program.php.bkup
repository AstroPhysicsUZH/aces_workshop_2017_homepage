
<script>
$(document).ready(function() {

    // Enter recurring breaks here (PHP code following)
    var evtSrcsBrks = { events: [

<?php
    for ($i = 5; $i < 9; $i++) {
        if ($i==7){continue;}
        echo <<<EOT
        { start:'2016-09-0{$i}T10:30:00', end:'2016-09-0{$i}T11:00:00', title:'Break' },
        { start:'2016-09-0{$i}T13:00:00', end:'2016-09-0{$i}T14:30:00', title:'Lunch Break' },
        { start:'2016-09-0{$i}T16:30:00', end:'2016-09-0{$i}T17:00:00', title:'Break' },
EOT;
    };
?>
        { start:'2016-09-07T10:30:00', end:'2016-09-07T11:00:00', title:'Break' },
        { start:'2016-09-07T13:00:00', end:'2016-09-07T14:00:00', title:'Lunch Break' },

        // Enter singular break events here
        { start:'2016-09-09T10:30:00', end:'2016-09-09T11:00:00', title:'Break' },
        { start:'2016-09-09T13:00:00', end:'2016-09-09T14:00:00', title:'Lunch Break' },

        { start:'2016-09-07T18:00:00', end:'2016-09-07T23:00:00', title:'Apero and Conference Dinner' },
    ], color: '#ff8888', textColor: 'black'};

    // Enter Plenary Talks
    var evtSrcsCTalks = { events: [
        // MO
        { start:'2016-09-05T09:00:00', end:'2016-09-05T09:15:00', title:'Opening' },
        { start:'2016-09-05T09:15:00', end:'2016-09-05T13:00:00', title:'Plenary Talks' },
        // Di
        { start:'2016-09-06T09:00:00', end:'2016-09-06T13:00:00', title:'Plenary Talks' },
        // Mi
        { start:'2016-09-07T09:00:00', end:'2016-09-07T13:00:00', title:'Plenary Talks' },
        { start:'2016-09-07T14:00:00', end:'2016-09-07T17:00:00', title:'Joint eLISA and L3ST consortium meeting' },
        // Do
        { start:'2016-09-08T09:00:00', end:'2016-09-08T13:00:00', title:'Plenary Talks' },
        // Fr
        { start:'2016-09-09T09:00:00', end:'2016-09-09T13:00:00', title:'Plenary Talks' },
    ], color:'#88ff88', textColor:'black', borderColor:'#008800' };

    // Enter Parallelsessions
    var evtSrcsPTalks = { events: [
        { start:'2016-09-05T14:30:00', end:'2016-09-05T19:00:00', title:'Contributed Talks' },

        { start:'2016-09-06T14:30:00', end:'2016-09-06T19:00:00', title:'Contributed Talks' },

        { start:'2016-09-08T14:30:00', end:'2016-09-08T19:00:00', title:'Contributed Talks' },

        ], color:'#ffff88', textColor:'black', borderColor:'#aaaa00' };


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
                scrollTime: '08:30:00',
                slotLabelFormat: 'H',
                minTime: "08:30:00",
                maxTime: "20:30:00",
                columnFormat: "ddd D"
            }
        },

        allDaySlot: false,
        eventSources: [evtSrcsBrks, evtSrcsCTalks, evtSrcsPTalks],
        timeFormat: 'H(:mm)',
    })
});
</script>

<!-- Empty div for the calendar to be used -->
<div id='calendar'></div>
