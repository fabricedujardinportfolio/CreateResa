<?php

/**
 * @package résa events plugin
 * @version 1.0
 */
/*
Plugin Name: resa events plugin
Description: These just a events resa plugin for ECF CCI in Nouméa
Author: Fabrice DUJARDIN
Version: 1.0
Author URI: https://fabricedujardinportfolio.github.io/
*/

// Premiére étape: Création de la database

function event_database()
{
	global $wpdb;

	$table_name = $wpdb->prefix . 'reservation_events';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		first_name varchar(55) NOT NULL,
		last_name varchar(55) NOT NULL,
		phone VARCHAR(20) NOT NULL,
		age mediumint(6) NOT NULL,
		cours VARCHAR(100) NOT NULL,
		horraire VARCHAR(100) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	add_option('event_database', '1.0');
}

register_activation_hook(__FILE__, 'event_database');

// Troiséme étap: Rajout du plugin sur admin
function add_plugin_to_admin_events()
{
	// Utilisation de la classe wordpress WP_List_Table
	function event_content()
	{
		echo "<h1>Events</h1>";
		echo "<div style='margin-right:20px'>";

		if (class_exists('WP_List_Table')) {
			require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
			require_once(plugin_dir_path(__FILE__) . 'events-list-table.php');
			$eventsListTable = new ReservationListTable();
			$eventsListTable->prepare_items();
			$eventsListTable->display();
		} else {
			echo "WP_List_Table n'est pas disponible.";
		}

		echo "</div>";
	}

	add_menu_page('Toutes les réservations', 'Toutes les réservations', 'manage_options', 'events-plugin', 'event_content');
}

add_action('admin_menu', 'add_plugin_to_admin_events');

function reservation_form()
{
	ob_start();
	// Récupération des données
	if (isset($_POST['fabreservations'])) {
		$first_name = sanitize_text_field($_POST["first_name"]);
		$last_name = sanitize_text_field($_POST["last_name"]);
		$phone = sanitize_text_field($_POST["phone"]);
		$age = esc_textarea($_POST["age"]);
		$cours = sanitize_text_field($_POST["cours-id"]);
		$horraire = sanitize_text_field($_POST["horraire"]);

		// Vérification des données
		if ($first_name != '' && $last_name != '' && $phone  != '' && $age  != '' && $cours  != '') {
			global $wpdb;

			$table_name = $wpdb->prefix . 'reservation_events';

			$wpdb->insert(
				$table_name,
				array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'phone' => $phone,
					'age' => $age,
					'cours' => $cours,
					'horraire' => $horraire,

				)
			);

			echo "<h4>Merci! Vous êtes inscrit à ce cours.</h4>";
		}
	}
	//Formulaire à intégrer avec un code court
	echo "<form class='col-8 mx-auto' method='POST'>";
	echo "<h5> <strong>Inscription au:</strong> " . get_the_title() . " </h5>";
	echo "<div class='input-group mb-3'>";
	echo "<span class='input-group-text'>Prénom</span>";
	echo "<input class='form-control' type='text' name='first_name' placeholder='Prénom' required>";
	echo "</div>";
	echo "<div class='input-group mb-3'>";
	echo "<select class='form-select p-2 mb-3' aria-label='Default select example' name='horraire'>
			<option selected>Choisie ton crénaux horaire</option>
			<option value='10h à 12h'>10h à 12h</option>
			<option value='13h à 15h'>13h à 15h</option>
			<option value='16h à 18h'>16h à 18h</option>
		</select>";
	echo "<input class='form-control'  type='text' name='last_name' placeholder='Nom de famille' required>";
	echo "<span class='input-group-text'>Nom</span>";
	echo "</div>";
	echo "<div class='input-group mb-3'>";
	echo "<span class='input-group-text'>Téléphone</span>";
	echo "<input class='form-control'  type='tel' name='phone' placeholder='' required>";
	echo "</div>";
	echo "<div class='input-group mb-3'>";
	echo "<input class='form-control'  name='age' placeholder='Âge' required>";
	echo "<span class='input-group-text'>Âge</span>";
	echo "</div>";
	echo "<input class='form-control'  type='hidden' name='cours-id' value='" . get_the_title() . "' required>";
	echo "<button class='btn btn-light' type='submit' name='fabreservations' value='Envoyez'>Envoyez</button>";
	echo "</form>";

	return ob_get_clean();
}

add_shortcode('fabreservations', 'reservation_form');
