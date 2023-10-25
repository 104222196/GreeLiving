<?php

    echo '<pre>';
    print_r($session->user);
    echo '</pre>';

    echo '<p>You have logged in as an applicant</p>';

    echo '<p>You can now <a href="/applicants/logout">log out</a>.</p>';
?>