<?php

class ReservationListTable extends WP_List_Table
{
	// Préparation de l'affichage
	function prepare_items()
	{
		$data = $this->get_data();
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$perPage = 4;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args(array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		));

		$data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

		usort($data, array($this, 'usort_reorder'));
		$this->_column_headers = array($columns, array(), $sortable);


		$this->items = $data;
	}

	//Récupération des data dans la base de données
	function get_data()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'reservation_events';
		return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
	}

	//Mapping des nom de collomn avec les labels de tableau
	function get_columns()
	{
		$columns = array(
			'id' => 'ID',
			'first_name' => 'Prénom',
			'last_name' => 'Nom de famille',
			'phone' => 'Numéro de téléphone',
			'age' => 'Age',
			'cours' => 'Objet',
			'horraire' => 'Tranche horraire',
		);
		return $columns;
	}
	// Configuration du trie en fonction des nom de row de la database
	function get_sortable_columns()
	{
		return array(
			'id' => array('id', false),
			'cours' => array('cours', false),
			'horraire' => array('horraire', false),
		);
	}
	// Fonction de trie 
	function usort_reorder($a, $b)
	{
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
		$result = strcmp($a[$orderby], $b[$orderby]);
		return ($order === 'asc') ? $result : -$result;
	}

	function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'id':
			case 'first_name':
			case 'last_name':
			case 'phone':
			case 'age':
			case 'cours':
			case 'horraire':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}
}
