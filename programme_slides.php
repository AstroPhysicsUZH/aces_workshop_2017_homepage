<?php
# scan $UPLOADS_DIR and see which files have been uploaded,
# but only choose pdf files
$all_files = [];
$base_dir = $UPLOADS_DIR;
$id_dirs = array_diff(scandir($base_dir), array('..', '.'));

foreach ($id_dirs as $key => $dir) {
    # print $dir;
    $subdir = $base_dir . DIRECTORY_SEPARATOR . $dir;
    if (is_dir($subdir)) {
        $files = glob($subdir . DIRECTORY_SEPARATOR . "*.pdf");
        if (count($files)>0) {
            $all_files[$dir] = $files[0]; # we just use the first file in the directory!
        }
    }
}
#print_r($pres_files);


# get all available videos
#
#$all_videos = [
#    "029" => "https://www.youtube.com/watch?v=JqwKEbUabG4"
#];
require_once "data/youtube_recordings.php";
$all_videos = $youtube_recordings;
?>

<script src='js/fullcalendar.min.js'></script>

<h1>Programme and Slides</h1>

<p>
    All the talks will take place in the room Y15 G20. Just <a href="http://www.physik.uzh.ch/~rafik/lisa2016/?page=transportation">follow the signs</a>.
    <br>
    Additionally, there will be a transmission into Y15 G19 and a <a href="http://tiny.uzh.ch/B1"> live stream on youtube</a>.
    <br>
    Please find also the slides and youtube recordings if already available in the list of talks / posters.
</p>

<ul class="submenu">
    <li><a href="#timetable">Timetable</a></li>
    <li><a href="#listoftalks">List of Talks and Slides</a></li>
    <li><a href="#listofposters">List of Posters and PDFs</a></li>
    <li><a href="#conses">Contributed Sessions and Organizers</a></li>
    <li><a href="#confdin">Conference Dinner</a></li>
</ul>

<h2 id="timetable">Timetable</h2>
<p>
    Mouse over for details
</p>

<?php require "items/program.php"; ?>

<p class="small">

<!--    (will be refined as soon as details are known) -->
    (might still slightly change)
</p>

<?php require "items/list_of_talks.php"; ?>
<?php require "items/list_of_posters.php"; ?>

<? /*php require "items/plenary_speakers.php";*/ ?>
<?php require "items/show_parallelsessions.php"; ?>
<?php require "items/conference_dinner.php"; ?>
