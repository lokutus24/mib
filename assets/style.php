<?php
// Fejléc beállítása, hogy a kimenet CSS-ként jelenjen meg
header("Content-Type: text/css");

// WordPress környezet betöltése
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Színopciók lekérése az adatbázisból
$options = maybe_unserialize( get_option('mib_color_settings') ); // Ezt cseréld le a megfelelő opciónevedre

$primary_color = isset($options['mib-first-color']) ? $options['mib-first-color'] : '#000000';
$secondary_color = isset($options['mib-second-color']) ? $options['mib-second-color'] : '#FFFFFF';
$third_color = isset($options['mib-third-color']) ? $options['mib-third-color'] : '#FFFFFF';
$slider_color = isset($options['mib-apartment-slider-active-color']) ? $options['mib-apartment-slider-active-color'] : '#FFFFFF';
$slider_inactive_color = isset($options['mib-apartment-slider-inactive-color']) ? $options['mib-apartment-slider-inactive-color'] : '#FFFFFF';

// Dinamikus CSS kimenet generálása
echo "
.slider-color {
    background-color: {$slider_color};
}
.catalog-dropdown-menu.show{
   background-color: {$secondary_color}; 
}
.noUi-connects{
    background: {$slider_color};
}
.noUi-base{
    background: {$slider_color};
}

.noUi-connect{
    background: {$slider_inactive_color} !important;
}
.noUi-touch-area{
    background-color: {$secondary_color};
    border-radius: 15px;
}
.ui-slider-range{
    background: {$slider_color};
}
.ui-slider-handle.ui-state-default,
.ui-slider-handle.ui-corner-all {
    background: {$secondary_color} !important;
}
.slider-inactive-color {
    background-color: {$slider_inactive_color};
}
.primary-color {
    background-color: {$primary_color};
}
.secondary-fill{
    fill:{$secondary_color};
}
.secondary-color {
    background-color: {$secondary_color};
}
.secondary-text-color {
    color: {$secondary_color};
}
.third-color {
    background-color: {$third_color};
}
.third-text-color {
    color: {$third_color};
}
.primary-border-color{
    border-color: {$primary_color};
}
.primary-box-shadow{
    box-shadow: 0px 0px 15px 0px {$primary_color};
}
.primary-wp-floating-chat-input{
    box-shadow: 0px 0px 5px 0px {$primary_color};
}

.secondary-border-color{
    border-color: {$secondary_color};
    box-shadow: 0px 0px 15px 0px {$secondary_color};
}
.secondary-box-shadow{
    box-shadow: 0px 0px 15px 0px {$secondary_color};
}
.secondary-wp-floating-chat-input{
    box-shadow: 0px 0px 5px 0px {$secondary_color};
}

.third-border-color{
    border-color: {$third_color};
    box-shadow: 0px 0px 15px 0px {$third_color};
}
.third-box-shadow{
    box-shadow: 0px 0px 15px 0px {$third_color};
}
.third-wp-floating-chat-input{
    box-shadow: 0px 0px 5px 0px {$third_color};
}
.dropdown-item:hover, .dropdown-item:focus {
    background-color: {$secondary_color} !important;
}
select{
    background-color: {$primary_color};
}
.btn:hover{
    background-color: {$primary_color};
}
.btn:focus {
    background-color: {$primary_color};
}
#slider-range, #price-slider-range {
  background-color: {$primary_color};
}

.btn-dark {
  
    --bs-btn-active-bg: {$primary_color};
}
#mibtd{
    color: {$third_color};
}
#mibtd a{
    color: {$third_color};
}
";
