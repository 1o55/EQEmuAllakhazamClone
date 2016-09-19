<?php

$name = mysql_real_escape_string($_GET['q']);

$page_title = "Global Search :: " . $_GET['q'];

if($_GET['get_data']){
    if($_GET['fetch_type'] == "global_mobs"){
        $name = mysql_real_escape_string($_GET['get_data']);
        $query = "SELECT * FROM `npc_types` WHERE `name` LIKE '%" . $name . "%' ORDER BY `name`";
        $result = db_mysql_query($query);
        echo '<ul>';
        while ($row = mysql_fetch_array($result)) {
            echo '<a href="?a=npc&id=' . $row['id'] . '">' . get_npc_name_human_readable($row['name']) . '</a><br>';
        }
        echo '</ul>';
    }
    if($_GET['fetch_type'] == "global_items") {
        $name = mysql_real_escape_string($_GET['get_data']);
        $query = "SELECT * FROM `items` WHERE `name` LIKE '%" . $name . "%' ORDER BY `name`";
        $result = db_mysql_query($query);
        echo '<ul>';
        while ($row = mysql_fetch_array($result)) {
            echo '<a href="?a=item&id=' . $row['id'] . '">' . $row['Name'] . '</a><br>';
        }
        echo '</ul>';
    }

    exit;
}

$tab_title = "";

/* Zones */
$query = "SELECT COUNT(*) as found_count FROM `zone` WHERE `short_name` LIKE '%" . $name . "%' OR `long_name` = '%" . $name . "%'";
$result = db_mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    if($row['found_count'] > 0)
        $tab_title = "<li onclick='tablistview(this.childNodes[0]);' id='global_zones'><a  href='javascript:;' onclick='fetch_global_data(\"global_zones\")'>Zones (" . $row['found_count'] . ")<b></b></a></li>";
}

/* NPCS */
$query = "SELECT COUNT(*) as found_count FROM `npc_types` WHERE `name` LIKE '%" . $name . "%'";
$result = db_mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    if($row['found_count'] > 0)
        $tab_title .= "<li onclick='tablistview(this.childNodes[0]);' id='global_mobs'><a href='javascript:;'  onclick='fetch_global_data(\"global_mobs\")'>Mobs (" . $row['found_count'] . ")<b></b></a></li>";
}

/* Items */
$query = "SELECT COUNT(*) as found_count FROM `items` WHERE `Name` LIKE '%" . $name . "%'";
$result = db_mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    if($row['found_count'] > 0)
        $tab_title .= "<li onclick='tablistview(this.childNodes[0]);' id='global_items'><a href='javascript:;' onclick='fetch_global_data(\"global_items\")'>Items (" . $row['found_count'] . ")<b></b></a></li>";
}

/* Factions */
$query = "SELECT COUNT(*) as found_count FROM `faction_list` WHERE `name` LIKE '%" . $name . "%'";
$result = db_mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    if($row['found_count'] > 0)
        $tab_title .= "<li onclick='tablistview(this.childNodes[0]);'  id='global_factions'><a href='javascript:;' onclick='tablistview(this);'>Factions (" . $row['found_count'] . ")<b></b></a></li>";
}

/* Tradeskills */
$query = "SELECT COUNT(*) as found_count FROM `tradeskill_recipe`  WHERE `name` LIKE '%" . $name . "%'";
$result = db_mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    if($row['found_count'] > 0)
        $tab_title .= "<li onclick='tablistview(this.childNodes[0]);' id='global_tradeskill'><a href='javascript:;' onclick='tablistview(this);'>Tradeskills (" . $row['found_count'] . ")<b></b></a></li>";
}

/* Forage */
$query = "
    SELECT
    COUNT(*) as found_count
    FROM
    forage
    INNER JOIN items ON forage.Itemid = items.id
    WHERE `name` LIKE '%" . $name . "%'
";
$result = db_mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    if($row['found_count'] > 0)
        $tab_title .= "<li onclick='tablistview(this.childNodes[0]);'><a href='javascript:;' onclick='tablistview(this);'>Foraging (" . $row['found_count'] . ")<b></b></a></li>";
}

echo '
    <div class="tabwrapper">
        <ul class="tablist">
            ' . $tab_title . '
            </li>
        </ul>
        <br>
        <br>
        <br>
        <div id="active_search_content"></div>
    </div>
';

echo '<script type="text/javascript">
    $(".tablist li").each(function(i) {
        u = "#active_search_content";
        $.get("?a=global_search&get_data=' . urlencode($_GET['q']) . '&fetch_type=" + $(this).attr("id") + "&v_ajax", function (data) {
            $(u).html(data);
        });
        $(this).addClass("current");
        return false;
    });
    function fetch_global_data(type){
        console.log(type);

        $(".tablist li").each(function(i) {
            $(this).removeClass("current");
        });

        $("#" + type).addClass("current");

        u = "#active_search_content";
        $.get("?a=global_search&get_data=' . urlencode($_GET['q']) . '&fetch_type=" + type + "&v_ajax", function (data) {
            $(u).html(data);
        });
    }
</script>';

?>
