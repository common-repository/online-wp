<?php
/*
Plugin Name: Online WP
Plugin URI: http://plugins.wp-themes.ws/online-wp
Description: Allows you to display the total amount of guests online.
Version: 1.0.6
Author: WP-Themes.ws
Author URI: http://wp-themes.ws
*/

/*  Copyright 2010 WP-Themes.ws - support@wp-themes.ws

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Hook for adding admin menus
add_action('admin_menu', 'online_wp_add_pages');
register_activation_hook(__FILE__,'online_install');

// action function for above hook
function online_wp_add_pages() {
    add_options_page('Online WP', 'Online WP', 'administrator', 'online_wp', 'online_wp_options_page');
}

$online_db_version = "1.1.0";

function online_install () {
   global $wpdb;
   global $online_db_version;

   $table_name = $wpdb->prefix . "onlinewp";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
	  $sql2 = "DROP TABLE ".$table_name;
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  ip text NOT NULL,
	  location text NOT NULL,
	  date text NOT NULL,
	  time text NOT NULL,
	  member text NOT NULL,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql2);
      dbDelta($sql);
 
      add_option("online_db_version", $online_db_version);

   }
}


// online_wp_options_page() displays the page content for the Test Options submenu
function online_wp_options_page() {

    // variables for the field and option names 
    $opt_name_5 = 'mt_online_plugin_support';
	$opt_name_6 = 'mt_online_header';
	$opt_name_7 = 'mt_online_label1';
	$opt_name_8 = 'mt_online_label2';
    $hidden_field_name = 'mt_online_submit_hidden';
    $data_field_name_5 = 'mt_online_plugin_support';
	$data_field_name_6 = 'mt_online_header';
	$data_field_name_7 = 'mt_online_label1';
	$data_field_name_8 = 'mt_online_label2';

    // Read in existing option value from database
    $opt_val_5 = get_option($opt_name_5);
	$opt_val_6 = get_option($opt_name_6);
	$opt_val_7 = get_option($opt_name_7);
	$opt_val_8 = get_option($opt_name_8);

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val_5 = $_POST[$data_field_name_5];
		$opt_val_6 = $_POST[$data_field_name_6];
		$opt_val_7 = $_POST[$data_field_name_7];
		$opt_val_8 = $_POST[$data_field_name_8];

        // Save the posted value in the database
        update_option( $opt_name_5, $opt_val_5 );
		update_option( $opt_name_6, $opt_val_6 );
		update_option( $opt_name_7, $opt_val_7 );
		update_option( $opt_name_8, $opt_val_8 );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Online WP Plugin Options', 'mt_trans_domain' ) . "</h2>";

    // options form
    
    $change3 = get_option("mt_online_plugin_support");
	$change4 = get_option("mt_online_header");


if ($change3=="Yes" || $change3=="") {
$change3="checked";
$change31="";
} else {
$change3="";
$change31="checked";
}

?>	
<form name="form3" method="post" action="">
<h3>Current Users</h3>

<?php
   global $wpdb;
   $table_name = $wpdb->prefix . "onlinewp";
   $date=date("Y-m-d");
   $time=time()-300;
   
   $rows = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE date='".$date."' AND time > ".$time);
echo "<ul>";
foreach ($rows as $rows) {
echo "<li>".$rows->ip."</li>";
}
echo "</ul>";
?>

<h3>Settings</h3>

<p>No other settings are required for this widget, all you need to do is go into Appearance --> Widgets and drag the widget into the sidebar.</p>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Widget Title", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_6; ?>" value="<?php echo stripslashes($change4); ?>" size="50">
</p><hr />

<p><?php _e("Give the link back?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="Yes" <?php echo $change3; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="No" <?php echo $change31; ?>>No
</p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p><hr />

</form>
</div>
<?php
 
}

function online_set_cookie() {

if (isset($_COOKIE["onlinewp"])) {
setcookie("onlinewp", "set", time()+300);
   global $wpdb;
   $table_name = $wpdb->prefix . "onlinewp"; 
   $ip=$_SERVER['REMOTE_ADDR'];
   $date=date("Y-m-d");
   $time=time();
   
$y=$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE ip='$ip';"));

if ($y==0) {
    $sql = 'INSERT INTO ' . $table_name . ' SET ';
	$sql .= 'ip = "'. $ip .'", ';
	$sql .= 'date = "'. $date .'", ';
	$sql .= 'time = "'. $time .'"';
	
    $wpdb->query( $sql );
	} else {
	$query = $wpdb->query("UPDATE " . $table_name . " SET date=".$date." WHERE ip=".$ip);
$query = $wpdb->query("UPDATE " . $table_name . " SET time=".$time." WHERE ip=".$ip);
}
   
} else {
setcookie("onlinewp", "set", time()+300);

   global $wpdb;
   $table_name = $wpdb->prefix . "onlinewp"; 
   $ip=$_SERVER['REMOTE_ADDR'];
   $date=date("Y-m-d");
   $time=time();
   $location=$_SERVER['REQUEST_URI'].$_SERVER['SERVER_NAME'];
   
$z=$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE ip='$ip';"));

if ($z>0) {
$query = $wpdb->query("UPDATE " . $table_name . " SET date=".$date." WHERE ip=".$ip);
$query = $wpdb->query("UPDATE " . $table_name . " SET time=".$time." WHERE ip=".$ip);
} else if ($z==0) {
   $table_name = $wpdb->prefix . "onlinewp";
   
   get_currentuserinfo();

global $user_ID;
if ($user_ID) {
$member=1;
} else {
$member=0;
}

    $sql = 'INSERT INTO ' . $table_name . ' SET ';
	$sql .= 'ip = "'. $ip .'", ';
	$sql .= 'date = "'. $date .'", ';
	$sql .= 'time = "'. $time .'", ';
	$sql .= 'member = "'. $member .'"';
	
    $wpdb->query( $sql );

}
}
}

function init_online_widget() {
register_sidebar_widget('Online WP', 'show_online');
}

function show_online($args) {
extract($args);
$online_header = get_option("mt_online_header");
$supportplugin = get_option("mt_online_plugin_support"); 
$label1 = get_option("mt_online_label1");
$label2 = get_option("mt_online_label2");
global $wpdb;

$table_name = $wpdb->prefix . "onlinewp";
$date=date("Y-m-d");
$time=time()-900;

   $rows = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE date='".$date."' AND time > ".$time." AND member=0");
   
foreach ($rows as $rows) {
$x ++;
}

$rows = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE date='".$date."' AND time > ".$time." AND member=1");

foreach ($rows as $rows) {
$y ++;
}

$query = $wpdb->query("DELETE FROM " . $table_name . " WHERE date !='".$date."'");
$query = $wpdb->query("DELETE FROM " . $table_name . " WHERE time < ".$time);

if ($y=="") {
$y=0;
}

if ($x=="") {
$x=0;
}

if ($y==1) {
$ytext="member";
} else {
$ytext="members";
}

if ($x==1) {
$xtext="guest";
} else {
$xtext="guests";
}

if ($online_header=="") {
$online_header="Who's Online";
}

echo $before_widget.$before_title.stripslashes($online_header).$after_title;
echo "<ul><li>$y $ytext & $x $xtext online.</li></ul>";

if ($supportplugin=="Yes" || $supportplugin=="") {
echo '<p style="font-size:x-small">Online Plugin made by <a href="http://www.vlcmediaplayer.org">Audacity Download</a></p>';
}

echo $after_widget;
}

add_action("plugins_loaded", "init_online_widget");
add_action("get_header", "online_set_cookie");

?>
