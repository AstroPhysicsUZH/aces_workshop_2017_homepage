
<nav>
  <ul>
      <li><a href="index.php">Index</a></li>
      <li><a href="edit.php">Edit My Details</a></li>
      <li><a href="submission.php">Edit My Submission</a></li>
<!--
      <li><a href="invoice_.php">Get Invoice</a></li>
      <li><a href="invitation.php">Get Invitation Letter</a></li>
-->
      <li><a href="upload.php">Upload Talk/Poster</a></li>
      <li><a href="downloads.php">Downloads</a></li>
      <li><a href="../">&lt;&mdash; back</a></li>
  </ul>

<?php if ($_SESSION['loggedin']) { ?>
  <p class="menuaddition">
      <a href="logout.php">logout</a>
  </p>
<?php } ?>
  <p class="menuaddition">
    In case of problems:<br>
    <a href='mailto:relativityUZH@gmail.com'>relativityUZH@gmail.com</a>
  </p>
</nav>
