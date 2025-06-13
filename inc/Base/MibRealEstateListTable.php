<?php

namespace Inc\Base;

use WP_List_Table;
use Inc\Base\MibBaseController;
use Inc\Base\MibAuthController;

class MibRealEstateListTable extends WP_List_Table {

    
    public function prepare_items() {

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort($data, array(&$this, 'sort_data'));

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));

        $data = array_slice($data, (($currentPage-1)*$perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    public function get_columns() {

        $columns = array(
	        'id' => 'ID',
	        'name' => 'Név',
	        'numberOfRooms' => 'Szobák száma',
	        'price' => 'Ár',
	        'bruttoFloorArea' => 'Alapterület (m²)',
	        'floor' => 'Emelet',
	        'balcony' => 'Erkély (m²)',
	        'orientation' => 'Tájolás',
	        'view' => 'Kilátás',
	        'airConditioning' => 'Légkondicionáló'
	    );

        return $columns;
    }

    protected function column_default( $item, $column_name ) {

	    switch( $column_name ) {
	        case 'id':
	        case 'name':
	        case 'numberOfRooms':
	        case 'price':
	        case 'bruttoFloorArea':
	        case 'floor':
	        case 'balcony':
	        case 'orientation':
	        case 'view':
	            return $item[ $column_name ];
	        case 'airConditioning':
	            return $item[ $column_name ] ? 'Van' : 'Nincs';
	        default:
	            return print_r( $item, true ); //Show the whole array for troubleshooting purposes
	    }
	}


    public function get_hidden_columns() {

        return array();
    }

    public function get_sortable_columns() {

        //return array('price' => array('price', false));
        return [];
    }
 
    private function table_data() {

        $data = []; // Az adatok tömbje
        $table_data = [];

        $mibAuth = new MibAuthController();
        $options = $mibAuth->getOptionDatas();

        if (!empty($options)) {
        	
        	$expired = $mibAuth->checkExpireToken($options['expiry']);

	        if ($expired) {
	        	$mibAuth->loginToMib();
	        	$options = $mibAuth->getOptionDatas();
	        }

	        $data = $mibAuth->getApartments();

	        foreach ($data as $item) {

	            $table_data[] = array(
	                'id' => $item->id,
		            'name' => $item->name,
		            'numberOfRooms' => $item->numberOfRooms,
		            'price' => number_format($item->price, 2) . ' Ft', // Formázott ár
		            'bruttoFloorArea' => $item->bruttoFloorArea . ' m²',
		            'floor' => $item->floor,
		            'balcony' => $item->bruttoFloorArea . ' m²',
		            'orientation' => $this->format_orientation($item->orientation), // Tájolás formázása
		            'view' => $this->format_view($item->view), // Kilátás formázása
		            'airConditioning' => $item->airConditioning ? 'Van' : 'Nincs', // Légkondi állapotának formázása
	            );
	        }
        }
        

        return $table_data;
    }

}