<?php declare(strict_types = 1);

/* returns a container for the project's card to be displayed on
 * the dashboard.
*/
function display_card(string $name) : string {
    $html = "";


    return $html;
}

function display_card_test(string $name) : string {
    $html = "";

    $div1 = "<div id=\"" . $name . "_card\" class=\"project_card\">";
    $closediv1 = "</div>";

    


    $html = $div1 . $closediv1;
    return $html;
}
?>