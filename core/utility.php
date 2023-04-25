<?php

/* Misc. Helper Function */
/* Postgres function to get result as a list of objects */
function pg_fetch_all_objects($resource) {
    $return = array();
    while ($row = pg_fetch_object($resource)) {
        $return[] = $row;
    }
    return $return;
}