<?php
require_once "lib/headerphp.php";

$authorised = FALSE;
$sid_is_set = FALSE;
$sid = -1;

if (in_array($USER->role, $special_power_roles)) {
    $authorised = TRUE;
}

if (array_key_exists('sid', $_GET)) {
    $sid = $_GET['sid'];
    $sid_is_set = TRUE;

    $stmt = $db->prepare( "SELECT * FROM {$sessionsTable} WHERE id=:sid");
    $stmt->bindParam(':sid', $sid , PDO::PARAM_INT);
    $stmt->execute();
    $s = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$s) {
        print "invalid sid";
        die();
    }

    if ( in_array($USER->username, explode(";", $s->orgas)) ) {
        $authorised = TRUE;
    }
}


if (!empty($_POST)) {
    #print_r($_POST);

    if (array_key_exists("action", $_POST)) {
        $action = $_POST["action"];
        unset($_POST["action"]);
        #print_r($action);

        if ($action==
"get_data") {
            print "{'msg':'success'}";
            exit();
        }
        elseif ($action==
"save_entry") {

            $id  = $_POST['id']; #TODO we should actually check this id before using
            $atp = $_POST['acceptedType'];
            $dat = None;
            $dur = None;
            $ppl = None;

            if ($atp == PRESENTATION_TYPE_REJECTED) {
                $dat = "";
                $dat = "";
                $ppl = "";
            }
            elseif ($atp == PRESENTATION_TYPE_TALK) {
                $dat = $_POST['startdt'];
                $dat = $_POST['enddt'];
                $ppl = "";

            }
            elseif ($atp == PRESENTATION_TYPE_POSTER) {
                $dat = "";
                $dat = "";
                $ppl = $_POST['posterPlace'];
            }
            else {
                die();
            }
            $dat = $_POST['startdt'];
            $dat = $_POST['enddt'];
            $ppl = $_POST['posterPlace'];

            # check if strings valid
            if ($atp == PRESENTATION_TYPE_TALK) {
                echo "\ndtcheck >".$_POST['time']."<\n\n";
                if (empty($_POST['time'])) {
                    $dat = $start_dt->format($datetime_db_fstr);
                }
                if (empty($_POST['duration'])) {
                    $dur = 10; #default to 10minutes
                }
                try {
                    $dat = (
                        new DateTime(
                            $_POST['date'] . " " . $_POST['time']
                            )
                        )->format($datetime_db_fstr);
                    $tmp = new DateInterval('PT'.$_POST['duration'].'M');
                    $dur = $_POST['duration'];
                }
                catch (Exception $e) {
                    echo "\n\nunable to parse datetime and duration strings\nnot saving anything!";
                    die();
                }


                $stmtstr = "UPDATE {$tableName} SET
                    acceptedType = :atp,
                    presentationSlot = :dat,
                    presentationDuration = :dur,
                    posterPlace = :ppl
                    WHERE id = :id;";
                $stmt = $db->prepare($stmtstr);
                $stmt->bindParam(':id', $id , PDO::PARAM_INT);
                $stmt->bindParam(':atp', $atp , PDO::PARAM_INT);
                $stmt->bindParam(':dat', $dat , PDO::PARAM_STR);
                $stmt->bindParam(':dur', $dur , PDO::PARAM_INT);
                $stmt->bindParam(':ppl', $ppl , PDO::PARAM_STR);

                $res = $stmt->execute();

                print "{'msg':'success'}";
                exit();
            }
        }
    }
}


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
<style>

	body {
		margin-top: 40px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
	}


	#wrap {
		width: 100%;
        position: relative;
		margin: 0 auto;
        border: black solid 1px;
	}

	#leftlists {
	    position: absolute;
        left:0;
        width: 175px;
		padding: 0 10px;
		border: 1px solid #ccc;
		background: #eee;
		text-align: left;
	}

    #calendarwrap {
        position: absolute;
        left: 200px;
	    border: 1px solid black;
	}
        /*
        	#wrap {
        		width: 1100px;
        		margin: 0 auto;
                border: black solid 1px;
        	}

        	#leftlists {
        		float: left;
        		width: 175px;
        		padding: 0 10px;
        		border: 1px solid #ccc;
        		background: #eee;
        		text-align: left;
        	}
*/
    .list {
        padding: 5px;
        margin: 0;
        border: 1px solid black;
        min-height: 5em;
        list-style-type: none;

    }

    .list li {
        margin: 0;
        padding: 5px;
        border: 1px solid black;
        background-color: #ccc;
        cursor: move;
    }

/*
	#external-events h4 {
		font-size: 16px;
		margin-top: 0;
		padding-top: 1em;
	}

	#external-events .fc-event {
		margin: 10px 0;
		cursor: pointer;
	}

	#external-events p {
		margin: 1.5em 0;
		font-size: 11px;
		color: #666;
	}

	#external-events p input {
		margin: 0;
		vertical-align: middle;
	}
    #calendarwrap {
	    margin: 0 0 0 200px;
	    border: 1px solid black;
	}
*/

</style>
<script>
    $(function(){
        $('.list').sortable({
            connectWith: '.list',
            receive: function( event, ui ) {
                console.log("received", event, ui);
            }
        // })
        // .droppable({
        //     drop: function(event, ui){
        //         console.log("event in droppable", event, ui);
        //     }
        });

        /*
        $('.submission').draggable({
            cursor: 'move',
            revert: 'invalid',
            helper: 'clone',
            distance: 20
        });
        */

        $('.submission').each(function(){
            $(this).data('event', {
				title: $.trim($(this).text()), // use the element's text as the event title
				stick: true // maintain when user navigates (see docs on the renderEvent method)
			});

            // $(this).draggable({
            //     zIndex: 999
            // });
        });

        $('#calendar').fullCalendar({
            weekends: false, // will hide Saturdays and Sundays

            height: 'auto',

            defaultDate: '2016-09-05',
            defaultView: 'agendaWeek',
            editable: true,
            droppable: true,

            drop: function(){$(this).remove();},

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
            eventSources: [],
            timeFormat: 'H:mm',
        });


    });
</script>
</head>
<body>
<div id='wrap'>
    <h1>Planning of session</h1>
    <div id="leftlists">
        <p>drag and drop items to calendar or into lists</p>
        <h3>not classified yet...</h3>
        <ul id='submissions' class="list">
            <li class='submission'>submission</li>
        </ul>
        <h3>Posters</h3>
        <ul id='posters' class="list">
            <li class='submission'>poster</li>
        </ul>
        <h3>Rejected</h3>
        <ul id='rejected' class="list">
            <li class='submission'>rej</li>
        </ul>
    </div>
    <div id='calendarwrap'>
        <h3>Talks</h3>

        <div id='calendar'></div>
    </div>
    <div style='clear:both'></div>
</div>
<div id="overlay"></div>
</body>
</html>
