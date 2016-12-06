<h1>Welcome</h1>
<p>
    ACES Workshop Intro Text
    organized by University of Zurich
    and takes place at the <?=$confLocation?>
    in <?=$confDatetime?>
</p>

<p>
  Main topics:
</p>

<ul>
  <li>To Work...</li>
  <li>... and Shop</li>
</ul>

<h2>Important Dates (CEST)</h2>
<ul>
    <li>
        <span class='small datetime'>
            <?=$dateRegistrationOpens->format($datetime_fstr)?>
        </span>
        Registration Opens
    </li>
    <li>
        <span class='small datetime'>
            <?=$dateAbstractSubmissionDeadline->format($datetime_fstr)?>
        </span>
        Deadline for abstract submission
    </li>
    <li>
        <span class='small datetime'>
            <?=$dateReducedFeeDeadline->format($datetime_fstr)?>
        </span>
        Deadline for early registration
    </li>
    <li>
        <span class='small datetime'>
            <?=$dateRegistrationDeadline->format($datetime_fstr)?>
        </span>
        Registration closes
    </li>

    <li>
        <span class='small datetime'>
            <?=$dateConferenceStarts->format($datetime_fstr)?>
        </span>
        Conference Starts
    </li>
    <li>
        <span class='small datetime'>
            <?=$dateConferenceDinner->format($datetime_fstr)?>
        </span>
        Conference Dinner
    </li>
    <li>
        <span class='small datetime'>
            <?=$dateConferenceEnds->format($datetime_fstr)?>
        </span>
        Conference Closes
    </li>
</ul>
