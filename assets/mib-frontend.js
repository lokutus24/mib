/* ============================================================================
 * Override jQuery UI slider initializers for custom filters with noUiSlider
 * ============================================================================ */
if (typeof noUiSlider !== 'undefined') {
    function initializeRoomSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {
        const sliderEl = document.getElementById('custom-room-slider');
        const output = document.querySelector('.custom-room-range-value');
        if (!sliderEl) return;
        if (sliderEl.noUiSlider) sliderEl.noUiSlider.destroy();
        noUiSlider.create(sliderEl, {
            start: [Number(minValue), Number(maxValue)],
            connect: true,
            range: { min: Number(defaultMin), max: Number(defaultMax) },
            step: 1
        });
        sliderEl.noUiSlider.on('update', function(values) {
            output.textContent = `${parseInt(values[0])} - ${parseInt(values[1])}`;
        });
        sliderEl.noUiSlider.on('change', function(values) {
            saveRoomSliderValues(parseInt(values[0]), parseInt(values[1]));
            checkIfAnyFilterIsActive();
        });
        output.textContent = `${minValue} - ${maxValue}`;
        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }

    function initializeCatalogPriceSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {
        const sliderEl = document.getElementById('custom-price-slider');
        const output = document.querySelector('.custom-price-range-value');
        if (!sliderEl) return;
        if (sliderEl.noUiSlider) sliderEl.noUiSlider.destroy();
        noUiSlider.create(sliderEl, {
            start: [Number(minValue), Number(maxValue)],
            connect: true,
            range: { min: Number(defaultMin), max: Number(defaultMax) },
            step: 1
        });
        sliderEl.noUiSlider.on('update', function(values) {
            output.textContent = `${formatCurrency(values[0])} - ${formatCurrency(values[1])}`;
        });
        sliderEl.noUiSlider.on('change', function(values) {
            savePriceCatalogSliderValues(parseFloat(values[0]), parseFloat(values[1]));
            checkIfAnyFilterIsActive();
        });
        output.textContent = `${formatCurrency(minValue)} - ${formatCurrency(maxValue)}`;
        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }

    function initializeCatalogSquareSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {
        const sliderEl = document.getElementById('custom-square-slider');
        const output = document.querySelector('.custom-square-range-value');
        if (!sliderEl) return;
        if (sliderEl.noUiSlider) sliderEl.noUiSlider.destroy();
        noUiSlider.create(sliderEl, {
            start: [Number(minValue), Number(maxValue)],
            connect: true,
            range: { min: Number(defaultMin), max: Number(defaultMax) },
            step: 1
        });
        sliderEl.noUiSlider.on('update', function(values) {
            output.textContent = `${formatSquareMeter(values[0])} - ${formatSquareMeter(values[1])}`;
        });
        sliderEl.noUiSlider.on('change', function(values) {
            saveSquareCatalogSliderValues(parseFloat(values[0]), parseFloat(values[1]));
            checkIfAnyFilterIsActive();
        });
        output.textContent = `${formatSquareMeter(minValue)} - ${formatSquareMeter(maxValue)}`;
        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }

    function initializeFloorSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {
        const sliderEl = document.getElementById('custom-floor-slider');
        const output = document.querySelector('.custom-floor-range-value');
        if (!sliderEl) return;
        if (sliderEl.noUiSlider) sliderEl.noUiSlider.destroy();
        noUiSlider.create(sliderEl, {
            start: [Number(minValue), Number(maxValue)],
            connect: true,
            range: { min: Number(defaultMin), max: Number(defaultMax) },
            step: 1
        });
        sliderEl.noUiSlider.on('update', function(values) {
            output.textContent = `${parseInt(values[0])} - ${parseInt(values[1])}`;
        });
        sliderEl.noUiSlider.on('change', function(values) {
            saveFloorSliderValues(parseInt(values[0]), parseInt(values[1]));
            checkIfAnyFilterIsActive();
        });
        output.textContent = `${minValue} - ${maxValue}`;
        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }
}
jQuery(function($) {
    // jQuery UI slider API shim: .slider() now uses noUiSlider under the hood
    if (typeof noUiSlider === 'undefined') return;
    $.fn.slider = function() {
        var args = Array.prototype.slice.call(arguments);
        // init with options object
        if (args.length === 0 || typeof args[0] === 'object') {
            var options = args[0] || {};
            return this.each(function() {
                var sliderEl = this;
                if (sliderEl.noUiSlider) sliderEl.noUiSlider.destroy();
                var connect = options.range === true;
                var start = options.values
                    ? options.values.map(Number)
                    : [Number(options.min) || 0, Number(options.max) || 0];
                var step = options.step || 1;
                var min = Number(options.min) || 0;
                var max = Number(options.max) || 0;
                noUiSlider.create(sliderEl, {
                    start: start,
                    connect: connect,
                    range: { min: min, max: max },
                    step: step
                });
                if (typeof options.slide === 'function') {
                    sliderEl.noUiSlider.on('update', function(vals) {
                        options.slide.call(sliderEl, null, { values: vals.map(Number) });
                    });
                }
                if (typeof options.change === 'function') {
                    sliderEl.noUiSlider.on('change', function(vals) {
                        options.change.call(sliderEl, null, { values: vals.map(Number) });
                    });
                }
            });
        }
        // method calls
        var method = args[0];
        if (method === 'destroy') {
            return this.each(function() {
                if (this.noUiSlider) this.noUiSlider.destroy();
            });
        }
        if (method === 'values') {
            var el = this[0];
            if (el && el.noUiSlider) {
                return el.noUiSlider.get().map(parseFloat);
            }
            return null;
        }
        if (method === 'option') {
            var opt = args[1];
            var el = this[0];
            if (!el || !el.noUiSlider) return;
            if (opt === 'values') {
                return el.noUiSlider.get().map(parseFloat);
            }
            if (opt === 'min') {
                return el.noUiSlider.options.range.min;
            }
            if (opt === 'max') {
                return el.noUiSlider.options.range.max;
            }
        }
        return this;
    };
});
jQuery(document).ready(function($) {

    var debounceTimer;

    function initializeSlider(minValue, maxValue, defaultMin = 0, defaultMax = 200) {

        $("#slider-range").slider({
            range: true,
            min: Number(defaultMin),
            max: Number(defaultMax),
            values: [Number(minValue), Number(maxValue)],
            slide: function(event, ui) {
                $("#range-value").text(ui.values[0] + " - " + ui.values[1]);
            },
            change: function(event, ui) {
                $("#range-value").text(ui.values[0] + " - " + ui.values[1]);
                
                saveSliderValues(ui.values[0], ui.values[1]);
            }
        });
        $("#range-value").text(minValue + " - " + maxValue);
    }

    function initializePriceSlider(minValue, maxValue, defaultMin = 0, defaultMax = 100000000) {
        
        $("#price-slider-range").slider({
            range: true,
            min: Number(defaultMin),
            max: Number(defaultMax),
            values: [Number(minValue), Number(maxValue)],
            //step: 100000000,
            slide: function(event, ui) {
                $("#price-range-value").text(formatCurrency(ui.values[0]) + " - " + formatCurrency(ui.values[1]));
            },
            change: function(event, ui) {

                $("#price-range-value").text(formatCurrency(ui.values[0]) + " - " + formatCurrency(ui.values[1]));
                savePriceSliderValues(ui.values[0], ui.values[1]);
            }
        });
        $("#price-range-value").text(formatCurrency(minValue) + " - " + formatCurrency(maxValue));
    }
        
    $.ajax({
        url: ajaxurl,
        data: {
            'action': 'get_slider_range_data'
        },
        type: 'POST',
        dataType: 'JSON',
        success: function(response) {

            if (response) {

                var minRange = $('#min-area').val(response.min);
                var maxRange = $('#max-area').val(response.max);

                $("#slider-range").slider({
                    range: true,
                    min: Number(response.min),
                    max: Number(response.max),
                    values: [response.min, response.max],
                    slide: function(event, ui) {
                        $("#range-value").text(ui.values[0] + " - " + ui.values[1]);
                    },
                    change: function(event, ui) {
                        
                        $("#range-value").text(ui.values[0] + " - " + ui.values[1]);
                        console.log("Az új értékek: " + ui.values[0] + " és " + ui.values[1]);
                        // Itt hívhatod meg az AJAX kérést, vagy más műveletet végrehajtani
                        saveSliderValues(ui.values[0], ui.values[1]);
                    }
                });

                $("#range-value").text(response.min + " - " + response.max);

                //availability checkbox
                jQuery('.availability-checkbox').prop('checked', response.availability);

            } else {
                console.error('Hiba: ' + response.data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX hiba: ' + error);
        }
    });


    $.ajax({
        url: ajaxurl,
        data: {
            'action': 'get_price_slider_range_data',
        },
        type: 'POST',
        dataType: 'JSON',
        success: function(response) {

            if (response) {
                //price input range
                var minPrice = $('#min-price').val(response.min);
                var maxPrice = $('#max-price').val(response.max);

                $("#price-slider-range").slider({
                    range: true,
                    min: Number(response.min),
                    max: Number(response.max),
                    values: [response.min, response.max],
                    //step: 100000000,
                    slide: function(event, ui) {
                        $("#price-range-value").text(formatCurrency(ui.values[0]) + " - " + formatCurrency(ui.values[1]));
                    },
                    change: function(event, ui) {
                        $("#price-range-value").text(formatCurrency(ui.values[0]) + " - " + formatCurrency(ui.values[1]));
                        savePriceSliderValues(ui.values[0], ui.values[1]);
                    }
                });

                $("#price-range-value").text(formatCurrency(response.min) + " - " + formatCurrency(response.max));

            } else {
                console.error('Hiba: ' + response.data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX hiba: ' + error);
        }
    });

    function formatCurrency(value) {
        return new Intl.NumberFormat('hu-HU', {
            style: 'currency',
            currency: 'HUF',
            minimumFractionDigits: 0
        }).format(value);
    }

    function formatSquareMeter(value) {
        return value + ' m²';
    }

    function saveSliderValues(minValue, maxValue) {
         
        $('#mib-spinner').show();

        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        var selectedFloor = $('.floor-checkbox:checked').map(function() {
            return this.value;
        }).get();
        // Összegyűjti az összes bejelölt 'floor-checkbox' értékét egy tömbbe
        var selectedRoom = $('.room-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.availability-checkbox:checked').map(function() {
            return this.value;
        }).get();


        var priceRange = $("#price-slider-range").slider("values");

        // Determine current sort settings
        var parts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = parts[0] || '';
        var sortType = parts[1] || 'ASC';
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'set_slider_values',
                slider_min_value: minValue,
                slider_max_value: maxValue,
                price_slider_min_value: priceRange[0],
                price_slider_max_value: priceRange[1],
                floor: selectedFloor,
                room: selectedRoom,
                orientation: selectOritentation,
                availability: selectAvailability,
                sort: sort,
                sortType: sortType,
                page_type: (cardContainer.length == 1) ? 'card' : 'table'
            },
            success: function(response) {
                //console.log("ajax után: "+minValue +" :"+ maxValue);
                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                }
                $('#mib-spinner').hide();
                hoverDropDown();
                updateFloorSelectionCount('floor', 'Emelet');
                updateFloorSelectionCount('room', 'Szobák');
                updateFloorSelectionCount('orientation', 'Tájolás');

                initializeSlider(minValue, maxValue, response.data.slider_min, response.data.slider_max);
                initializePriceSlider(priceRange[0], priceRange[1], response.data.price_slider_min, response.data.price_slider_max);

            },
            error: function(error) {
                console.log('Hiba történt az adatok betöltése közben.');
                $('#mib-spinner').hide();
                hoverDropDown();
                
                initializeSlider(minValue, maxValue, response.data.slider_min, response.data.slider_max);
                initializePriceSlider(priceRange[0], priceRange[1], response.data.price_slider_min, response.data.price_slider_max);
            }
        });
    }

    function savePriceSliderValues(minValue, maxValue) {
         
        $('#mib-spinner').show();

        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        var selectedFloor = $('.floor-checkbox:checked').map(function() {
            return this.value;
        }).get();
        // Összegyűjti az összes bejelölt 'floor-checkbox' értékét egy tömbbe
        var selectedRoom = $('.room-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        // Get current square slider values
        var squareRange = $("#slider-range").slider("values") || [0, 0];

        // Determine current sort settings
        var parts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = parts[0] || '';
        var sortType = parts[1] || 'ASC';
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'set_price_slider_values',
                price_slider_min_value: minValue,
                price_slider_max_value: maxValue,
                slider_min_value: squareRange[0],
                slider_max_value: squareRange[1],
                floor: selectedFloor,
                room: selectedRoom,
                orientation: selectOritentation,
                availability: selectAvailability,
                sort: sort,
                sortType: sortType,
                page_type: (cardContainer.length == 1) ? 'card' : 'table'
            },
            success: function(response) {
                //console.log("ajax után: "+minValue +" :"+ maxValue);
                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                }
                $('#mib-spinner').hide();
                hoverDropDown();
                updateFloorSelectionCount('floor', 'Emelet');
                updateFloorSelectionCount('room', 'Szobák');
                updateFloorSelectionCount('orientation', 'Tájolás');
                
                initializeSlider(squareRange[0], squareRange[1], response.data.slider_min, response.data.slider_max);
                initializePriceSlider(minValue, maxValue, response.data.price_slider_min, response.data.price_slider_max);
                
            },
            error: function(error) {
                console.log('Hiba történt az adatok betöltése közben.');
                $('#mib-spinner').hide();
                hoverDropDown();
                initializeSlider(squareRange[0], squareRange[1], response.data.slider_min, response.data.slider_max);
                initializePriceSlider(minValue, maxValue, response.data.price_slider_min, response.data.price_slider_max);
                
            }
        });
    }


    function updateFloorSelectionCount(element, name) {
        // Megszámolja a bejelölt checkboxokat
        var selectedCount = $('.'+element+'-checkbox:checked').length;
        // Frissíti a gomb szövegét
        var buttonText = selectedCount > 0 ? name+" (" + selectedCount + ")" : name;
        $('#'+element+'Dropdown').text(buttonText);
    }

    function hoverDropDown() {

        $('.dropdown-toggle').each(function() {
            var dropdown = $(this); 
            var dropdownMenu = dropdown.next('.dropdown-menu');

            dropdown.hover(
                function() {
                    dropdownMenu.addClass('show');
                }, function() {
                    setTimeout(function() {
                        if (!dropdownMenu.is(':hover')) {
                            dropdownMenu.removeClass('show');
                        }
                    }, 100);
                }
            );

            dropdown.click(function(e) {
                e.stopPropagation();
                dropdownMenu.toggleClass('show');
            });
        });

        $(document).click(function() {
            $('.dropdown-menu').removeClass('show');
        });

        $('.dropdown-menu').click(function(e) {
            e.stopPropagation();
        });
    }

    hoverDropDown();
    
    // Keresés kezelése
    $('#custom-search-input').on('keyup', function(e) {
        var searchTerm = $(this).val();
        console.log(searchTerm);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'search_data',
                searchTerm: searchTerm,
            },
            success: function(response) {
                $('.custom-list-table-container').html(response);
            }
        });
    });

    $(document).on('change', '.floor-checkbox', function(e) {

        e.preventDefault();
        $('#mib-spinner').show();
        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        // Összegyűjti az összes bejelölt 'floor-checkbox' értékét egy tömbbe
        var selectedFloors = $('.floor-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectedRoom = $('.room-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var slider_min = $("#slider-range").slider("option", "values")[0];
        var slider_max = $("#slider-range").slider("option", "values")[1];
        //input
        if (!slider_min && !slider_max) {
            var slider_min = $('#min-area').val();
            var slider_max = $('#max-area').val();
        }
        var price_slider_min_value = $("#price-slider-range").slider("option", "values")[0];
        var price_slider_max_value = $("#price-slider-range").slider("option", "values")[1];
        //input
        if (!price_slider_min_value && !price_slider_max_value) {
            var price_slider_min_value = $('#min-price').val();
            var price_slider_max_value = $('#max-price').val();
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'filter_data_by_floor',
                floor: selectedFloors, // Tömbben küldi az összes bejelölt emelet értékét
                room: selectedRoom,
                orientation: selectOritentation,
                availability: selectAvailability,
                slider_min_value: slider_min,
                slider_max_value: slider_max,
                price_slider_min_value: price_slider_min_value,
                price_slider_max_value: price_slider_max_value,
                page_type: (cardContainer.length == 1) ? 'card' : 'table'
            },
            success: function(response) {

                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                }
                $('#mib-spinner').hide();
                hoverDropDown();
                updateFloorSelectionCount('floor', 'Emelet');
                updateFloorSelectionCount('room', 'Szobák');
                updateFloorSelectionCount('orientation', 'Tájolás');
                
                if ( $("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                } else {
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);
                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
                // Reinitialize catalog sliders (card view)
                if ($('#custom-square-slider').length && typeof initializeCatalogSquareSlider === 'function') {
                    initializeCatalogSquareSlider(response.data.slider_min, response.data.slider_max, response.data.slider_min, response.data.slider_max);
                }
                if ($('#custom-price-slider').length && typeof initializeCatalogPriceSlider === 'function') {
                    initializeCatalogPriceSlider(response.data.price_slider_min, response.data.price_slider_max, response.data.price_slider_min, response.data.price_slider_max);
                }
            },
            error: function(error) {
                console.log(error);
                $('#mib-spinner').hide();
                hoverDropDown();
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            }
        });
    });

    $(document).on('change', '.room-checkbox', function(e) {

        e.preventDefault();
        $('#mib-spinner').show();
        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        var selectedFloor = $('.floor-checkbox:checked').map(function() {
            return this.value;
        }).get();
        // Összegyűjti az összes bejelölt 'floor-checkbox' értékét egy tömbbe
        var selectedRoom = $('.room-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.availability-checkbox:checked').map(function() {
            return this.value;
        }).get();


        var slider_min = $("#slider-range").slider("option", "values")[0];
        var slider_max = $("#slider-range").slider("option", "values")[1];
        //input
        if (!slider_min && !slider_max) {
            var slider_min = $('#min-area').val();
            var slider_max = $('#max-area').val();
        }
        var price_slider_min_value = $("#price-slider-range").slider("option", "values")[0];
        var price_slider_max_value = $("#price-slider-range").slider("option", "values")[1];
        //input
        if (!price_slider_min_value && !price_slider_max_value) {
            var price_slider_min_value = $('#min-price').val();
            var price_slider_max_value = $('#max-price').val();
        }


        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'filter_data_by_room',
                floor: selectedFloor, // Tömbben küldi az összes bejelölt emelet értékét
                room: selectedRoom,
                orientation: selectOritentation,
                availability: selectAvailability,
                slider_min_value: slider_min,
                slider_max_value: slider_max,
                price_slider_min_value: price_slider_min_value,
                price_slider_max_value: price_slider_max_value,
                page_type: (cardContainer.length == 1) ? 'card' : 'table'
            },
            success: function(response) {
                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                }
                $('#mib-spinner').hide();
                hoverDropDown();
                updateFloorSelectionCount('floor', 'Emelet');
                updateFloorSelectionCount('room', 'Szobák');
                updateFloorSelectionCount('orientation', 'Tájolás');
                
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            },
            error: function(error) {
                
                $('#mib-spinner').hide();
                hoverDropDown();
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            }
        });
    });

    $(document).on('change', '.orientation-checkbox', function(e) {

        e.preventDefault();
        $('#mib-spinner').show();
        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        var selectedFloor = $('.floor-checkbox:checked').map(function() {
            return this.value;
        }).get();
        // Összegyűjti az összes bejelölt 'floor-checkbox' értékét egy tömbbe
        var selectedRoom = $('.room-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var slider_min = $("#slider-range").slider("option", "values")[0];
        var slider_max = $("#slider-range").slider("option", "values")[1];
        //input
        if (!slider_min && !slider_max) {
            var slider_min = $('#min-area').val();
            var slider_max = $('#max-area').val();
        }
        var price_slider_min_value = $("#price-slider-range").slider("option", "values")[0];
        var price_slider_max_value = $("#price-slider-range").slider("option", "values")[1];
        //input
        if (!price_slider_min_value && !price_slider_max_value) {
            var price_slider_min_value = $('#min-price').val();
            var price_slider_max_value = $('#max-price').val();
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'filter_data_by_orientation',
                floor: selectedFloor,
                room: selectedRoom,
                orientation: selectOritentation,
                availability: selectAvailability,
                slider_min_value: slider_min,
                slider_max_value: slider_max,
                price_slider_min_value: price_slider_min_value,
                price_slider_max_value: price_slider_max_value,
                page_type: (cardContainer.length == 1) ? 'card' : 'table'
            },
            success: function(response) {
                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                }
                $('#mib-spinner').hide();
                hoverDropDown();
                updateFloorSelectionCount('floor', 'Emelet');
                updateFloorSelectionCount('room', 'Szobák');
                updateFloorSelectionCount('orientation', 'Tájolás');
                

                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            },
            error: function(error) {
                console.log(error);
                $('#mib-spinner').hide();
                hoverDropDown();
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            }
        });
    });

    $(document).on('change', '.availability-checkbox', function(e) {

        e.preventDefault();
        $('#mib-spinner').show();
        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        var selectedFloor = $('.floor-checkbox:checked').map(function() {
            return this.value;
        }).get();
        // Összegyűjti az összes bejelölt 'floor-checkbox' értékét egy tömbbe
        var selectedRoom = $('.room-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var slider_min = $("#slider-range").slider("option", "values")[0];
        var slider_max = $("#slider-range").slider("option", "values")[1];
        //input
        if (!slider_min && !slider_max) {
            var slider_min = $('#min-area').val();
            var slider_max = $('#max-area').val();
        }
        var price_slider_min_value = $("#price-slider-range").slider("option", "values")[0];
        var price_slider_max_value = $("#price-slider-range").slider("option", "values")[1];
        //input
        if (!price_slider_min_value && !price_slider_max_value) {
            var price_slider_min_value = $('#min-price').val();
            var price_slider_max_value = $('#max-price').val();
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'filter_data_by_availability',
                floor: selectedFloor,
                room: selectedRoom,
                orientation: selectOritentation,
                availability: selectAvailability,
                slider_min_value: slider_min,
                slider_max_value: slider_max,
                price_slider_min_value: price_slider_min_value,
                price_slider_max_value: price_slider_max_value,
                page_type: (cardContainer.length == 1) ? 'card' : 'table'
            },
            success: function(response) {
                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                }
                $('#mib-spinner').hide();
                hoverDropDown();
                updateFloorSelectionCount('floor', 'Emelet');
                updateFloorSelectionCount('room', 'Szobák');
                updateFloorSelectionCount('orientation', 'Tájolás');
            
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            },
            error: function(error) {
                console.log(error);
                $('#mib-spinner').hide();
                hoverDropDown();
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            }
        });
    });

    // Pagináció és keresés kezelése, ahogy korábban láttuk
    $(document).on('click', '#page-link', function(e) {

        e.preventDefault();

        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        var page = $(this).data('page');
        
        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        var selectedFloor = $('.floor-checkbox:checked').map(function() {
            return this.value;
        }).get();
        // Összegyűjti az összes bejelölt 'floor-checkbox' értékét egy tömbbe
        var selectedRoom = $('.room-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var slider_min = $("#slider-range").slider("option", "values")[0];
        var slider_max = $("#slider-range").slider("option", "values")[1];
        //input
        if (!slider_min && !slider_max) {
            var slider_min = $('#min-area').val();
            var slider_max = $('#max-area').val();
        }
        var price_slider_min_value = $("#price-slider-range").slider("option", "values")[0];
        var price_slider_max_value = $("#price-slider-range").slider("option", "values")[1];
        //input
        if (!price_slider_min_value && !price_slider_max_value) {
            var price_slider_min_value = $('#min-price').val();
            var price_slider_max_value = $('#max-price').val();
        }

        //catalogus nézet (safe slider values)
        var floorVals = $("#custom-floor-slider").slider("option", "values") || [0, 0];
        var minFloor = floorVals[0];
        var maxFloor = floorVals[1];

        var priceVals = $("#custom-price-slider").slider("option", "values") || [0, 0];
        var minPrice = priceVals[0];
        var maxPrice = priceVals[1];

        var roomVals = $("#custom-room-slider").slider("option", "values") || [0, 0];
        var minRoom = roomVals[0];
        var maxRoom = roomVals[1];

        var squareVals = $("#custom-square-slider").slider("option", "values") || [0, 0];
        var minSquare = squareVals[0];
        var maxSquare = squareVals[1];

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();
        //catalogus nézet idáig
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'load_paginated_data',
                page: page,
                floor: selectedFloor,
                room: selectedRoom,
                orientation: selectOritentation,
                availability: selectAvailability,
                slider_min_value: slider_min,
                slider_max_value: slider_max,
                price_slider_min_value: price_slider_min_value,
                price_slider_max_value: price_slider_max_value,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode:shortcode,
                apartman_number: apartman_number
            },
            success: function(response) {


                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                }

                $('#mib-spinner').hide();

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });//

                
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            },
            error: function(error) {

                console.log('Hiba történt az adatok betöltése közben.');
                $('#mib-spinner').hide();
                hoverDropDown();
                if ($("#slider-range").length ) {
                    initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            }
        });
    });

    $(document).on('click', '#mib-filter-deletefilters', function(e) {
        
        e.preventDefault();
        $('#mib-spinner').show();

        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'deletefilters',
                page_type: (cardContainer.length == 1) ? 'card' : 'table'
            },
            success: function(response) {
                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                }
                $('#mib-spinner').hide();
                hoverDropDown();
                // Az alapértelmezett értékek használata a slider inicializálásához
                if ($("#slider-range").length ) {
                    initializeSlider(response.data.slider_min, response.data.slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(response.data.price_slider_min, response.data.price_slider_max, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(response.data.price_slider_min);
                    $('#max-price').val(response.data.price_slider_max);

                    $('#min-area').val(response.data.slider_min);
                    $('#max-area').val(response.data.slider_max);
                }
            },
            error: function(error) {
                console.log('Hiba történt az adatok betöltése közben.');
                $('#mib-spinner').hide();
                hoverDropDown();
                if ($("#slider-range").length ) {
                    initializeSlider(response.data.slider_min, response.data.slider_max, response.data.slider_min, response.data.slider_max);
                    initializePriceSlider(response.data.price_slider_min, response.data.price_slider_max, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $('#min-price').val(price_slider_min_value);
                    $('#max-price').val(price_slider_max_value);

                    $('#min-area').val(slider_min);
                    $('#max-area').val(slider_max);
                }
            }
        });

    });

    var debounceTimer;

    $(document).on('input', '#min-price, #max-price', function(e) {
        clearTimeout(debounceTimer); // Minden input változásnál törli az előző időzítést

        debounceTimer = setTimeout(function() {
            // Lekérjük a mezők értékeit
            var minPrice = $('#min-price').val();
            var maxPrice = $('#max-price').val();

            var minArea = $('#min-area').val();
            var maxArea = $('#max-area').val();

            console.log(minPrice);

            $('#mib-spinner').show();

            var table = $('#custom-list-table-container');

            var cardContainer = $('#custom-card-container');
            
            var selectedFloor = $('.floor-checkbox:checked').map(function() {
                return this.value;
            }).get();

            var selectedRoom = $('.room-checkbox:checked').map(function() {
                return this.value;
            }).get();

            var selectOrientation = $('.orientation-checkbox:checked').map(function() {
                return this.value;
            }).get();

            var selectAvailability = $('.availability-checkbox:checked').map(function() {
                return this.value;
            }).get();

            var slider_min = $("#slider-range").slider("option", "values")[0];
            var slider_max = $("#slider-range").slider("option", "values")[1];

            if (!slider_min && !slider_max) {
                slider_min = $('#min-area').val();
                slider_max = $('#max-area').val();
            }

            var price_slider_min_value = $("#price-slider-range").slider("option", "values")[0];
            var price_slider_max_value = $("#price-slider-range").slider("option", "values")[1];

            if (!price_slider_min_value && !price_slider_max_value) {
                price_slider_min_value = $('#min-price').val();
                price_slider_max_value = $('#max-price').val();
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: "JSON",
                data: {
                    action: 'set_slider_values',
                    slider_min_value: minArea,
                    slider_max_value: maxArea,
                    price_slider_min_value: minPrice,
                    price_slider_max_value: maxPrice,
                    floor: selectedFloor,
                    room: selectedRoom,
                    orientation: selectOrientation,
                    availability: selectAvailability,
                    page_type: (cardContainer.length == 1) ? 'card' : 'table'
                },
                success: function(response) {
                    if (table.length>0) {
                      table.replaceWith(response.data.html);
                    }else{
                       cardContainer.replaceWith(response.data.html);
                       restoreViewMode();
                    }
                    $('#mib-spinner').hide();
                    hoverDropDown();
                    updateFloorSelectionCount('floor', 'Emelet');
                    updateFloorSelectionCount('room', 'Szobák');
                    updateFloorSelectionCount('orientation', 'Tájolás');

                    if ($("#slider-range").length ) {
                        initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                        initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                    } else {
                        $('#min-price').val(price_slider_min_value);
                        $('#max-price').val(price_slider_max_value);
                        $('#min-area').val(slider_min);
                        $('#max-area').val(slider_max);
                    }
                },
                error: function(error) {
                    console.log('Hiba történt az adatok betöltése közben.');
                    $('#mib-spinner').hide();
                    hoverDropDown();
                }
            });

        }, 500); // 500 ms-os késleltetés
    });

    $(document).on('input', '#min-area, #max-area', function(e) {

        clearTimeout(debounceTimer); // Törli az előző időzítőt

        debounceTimer = setTimeout(function() { // Késleltetett hívás
            // Lekérjük a mezők értékeit
            var minPrice = $('#min-price').val();
            var maxPrice = $('#max-price').val();

            var minArea = $('#min-area').val();
            var maxArea = $('#max-area').val();

            $('#mib-spinner').show();

            var table = $('#custom-list-table-container');

            var cardContainer = $('#custom-card-container');
            
            var selectedFloor = $('.floor-checkbox:checked').map(function() {
                return this.value;
            }).get();
            
            var selectedRoom = $('.room-checkbox:checked').map(function() {
                return this.value;
            }).get();

            var selectOrientation = $('.orientation-checkbox:checked').map(function() {
                return this.value;
            }).get();

            var selectAvailability = $('.availability-checkbox:checked').map(function() {
                return this.value;
            }).get();

            var slider_min = $("#slider-range").slider("option", "values")[0];
            var slider_max = $("#slider-range").slider("option", "values")[1];

            if (!slider_min && !slider_max) {
                slider_min = $('#min-area').val();
                slider_max = $('#max-area').val();
            }

            var price_slider_min_value = $("#price-slider-range").slider("option", "values")[0];
            var price_slider_max_value = $("#price-slider-range").slider("option", "values")[1];

            if (!price_slider_min_value && !price_slider_max_value) {
                price_slider_min_value = $('#min-price').val();
                price_slider_max_value = $('#max-price').val();
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: "JSON",
                data: {
                    action: 'set_slider_values',
                    slider_min_value: minArea,
                    slider_max_value: maxArea,
                    price_slider_min_value: minPrice,
                    price_slider_max_value: maxPrice,
                    floor: selectedFloor,
                    room: selectedRoom,
                    orientation: selectOrientation,
                    availability: selectAvailability,
                    page_type: (cardContainer.length == 1) ? 'card' : 'table'
                },
                success: function(response) {
                    if (table.length>0) {
                      table.replaceWith(response.data.html);
                    }else{
                       cardContainer.replaceWith(response.data.html);
                       restoreViewMode();
                    }
                    $('#mib-spinner').hide();
                    hoverDropDown();
                    updateFloorSelectionCount('floor', 'Emelet');
                    updateFloorSelectionCount('room', 'Szobák');
                    updateFloorSelectionCount('orientation', 'Tájolás');

                    if ($("#slider-range").length ) {
                        initializeSlider(slider_min, slider_max, response.data.slider_min, response.data.slider_max);
                        initializePriceSlider(price_slider_min_value, price_slider_max_value, response.data.price_slider_min, response.data.price_slider_max);
                    } else {
                        $('#min-price').val(price_slider_min_value);
                        $('#max-price').val(price_slider_max_value);

                        $('#min-area').val(slider_min);
                        $('#max-area').val(slider_max);
                    }
                },
                error: function(error) {
                    console.log('Hiba történt az adatok betöltése közben.');
                    $('#mib-spinner').hide();
                    hoverDropDown();
                }
            });

        }, 500); // 500 ms-os késleltetés
    });
    
    $(document).on('click', '#load-more-button', function(e) {

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        e.preventDefault();

        nextPage = parseInt($('#load-more-button').attr('data-page'));

        $('#load-more-button').html('<span class="loading-spinner"></span> Betöltés...');
        $('#load-more-button').prop('disabled', true);

        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');
        
        // Safe floor slider values
        var floorSliderVals = $("#custom-floor-slider").slider("option", "values") || [0,0];
        var minFloor = floorSliderVals[0];
        var maxFloor = floorSliderVals[1];

        var priceSliderVals = $("#custom-price-slider").slider("option", "values") || [0,0];
        var minPrice = priceSliderVals[0];
        var maxPrice = priceSliderVals[1];

        var roomSliderVals = $("#custom-room-slider").slider("option", "values") || [0,0];
        var minRoom = roomSliderVals[0];
        var maxRoom = roomSliderVals[1];

        var squareSliderVals = $("#custom-square-slider").slider("option", "values") || [0,0];
        var minSquare = squareSliderVals[0];
        var maxSquare = squareSliderVals[1];

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL-je
            type: 'POST',
            data: {
                action: 'load_more_items',
                page: nextPage,
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value:minSquare,//terület
                slider_max_value:maxSquare,//terület
                availability: selectAvailability,
                orientation: selectOritentation,
                garden_connection: selectGardenConnection,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode:shortcode,
                apartman_number: apartman_number,
                residental_park_id:selectedParkId
            },
            success: function(response) {

                $('#load-more-button').prop('disabled', false);

                if (response && response.data.count > 0) {
                    $('#load-more-button').html('Még több ingatlan');

                    const isListView = $('#list-view').hasClass('active');
                    let htmlToInsert = response.data.html;

                    if (isListView) {
                        // Osztálycsere rács → lista nézet
                        htmlToInsert = htmlToInsert
                            .replace(/col-md-4/g, 'col-md-12')
                            .replace(/card h-100 position-relative/g, 'card h-100 position-relative list-view');
                    }

                    const targetSelector = isListView 
                        ? '#custom-card-container .col-md-12.mb-3:last' 
                        : '#custom-card-container .col-md-4.mb-3:last';

                    $(targetSelector).after(htmlToInsert);

                    const actualPage = parseInt($('#load-more-button').attr('data-page'));
                    $('#load-more-button').attr('data-page', actualPage + 1);
                } else {
                    $('#load-more-button').hide();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

                checkFavorites();
            }, 
            error: function(error){
                console.log(error);
            }
        });
    });

    /** Catalog new **/

    restoreViewMode();

    $(document).on('change', '.catalog-availability-checkbox', function(e) {
        
        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const cardContainer = $('#custom-card-container');
        const table = $('#custom-list-table-container');
        
        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;


        var minRoom = $("#custom-room-slider").slider("option", "values")[0];
        var maxRoom = $("#custom-room-slider").slider("option", "values")[1];

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minPrice = $("#custom-price-slider").slider("option", "values")[0];
        var maxPrice = $("#custom-price-slider").slider("option", "values")[1];

        var minSquare = $("#custom-square-slider").slider("option", "values")[0];
        var maxSquare = $("#custom-square-slider").slider("option", "values")[1];

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL-je
            type: 'POST',
            data: {
                action: 'set_orientation_values_by_catalog',
                orientation: selectOritentation,
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value: minSquare, // terület
                slider_max_value: maxSquare, // terület
                availability: selectAvailability,
                garden_connection: selectGardenConnection,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode: shortcode,
                apartman_number: apartman_number,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                residental_park_id:selectedParkId
            },
            success: function(response) {
                
                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                $('#mib-spinner').hide();

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

                checkFavorites();
            }
        });

    });


    //catalog-gardenconnection-checkbox
    $(document).on('change', '.catalog-gardenconnection-checkbox', function(e) {

        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const cardContainer = $('#custom-card-container');
        const table = $('#custom-list-table-container');
        
        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;


        var minRoom = $("#custom-room-slider").slider("option", "values")[0];
        var maxRoom = $("#custom-room-slider").slider("option", "values")[1];

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minPrice = $("#custom-price-slider").slider("option", "values")[0];
        var maxPrice = $("#custom-price-slider").slider("option", "values")[1];

        var minSquare = $("#custom-square-slider").slider("option", "values")[0];
        var maxSquare = $("#custom-square-slider").slider("option", "values")[1];

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL-je
            type: 'POST',
            data: {
                action: 'set_garden_connection_values_by_catalog',
                orientation:selectOritentation,
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value:minSquare,//terület
                slider_max_value:maxSquare,//terület
                availability: selectAvailability,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode:shortcode,
                apartman_number: apartman_number,
                garden_connection:selectGardenConnection,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                residental_park_id:selectedParkId
            },
            success: function(response) {
                
                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                $('#mib-spinner').hide();

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                }else{
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

                checkFavorites();

            }
        });
    });

    
    $(document).on('change', '.catalog-orientation-checkbox', function(e) {
        
        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const cardContainer = $('#custom-card-container');
        const table = $('#custom-list-table-container');
        
        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;


        var minRoom = $("#custom-room-slider").slider("option", "values")[0];
        var maxRoom = $("#custom-room-slider").slider("option", "values")[1];

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minPrice = $("#custom-price-slider").slider("option", "values")[0];
        var maxPrice = $("#custom-price-slider").slider("option", "values")[1];

        var minSquare = $("#custom-square-slider").slider("option", "values")[0];
        var maxSquare = $("#custom-square-slider").slider("option", "values")[1];

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL-je
            type: 'POST',
            data: {
                action: 'set_orientation_values_by_catalog',
                orientation: selectOritentation,
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value: minSquare, // terület
                slider_max_value: maxSquare, // terület
                availability: selectAvailability,
                garden_connection: selectGardenConnection,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode: shortcode,
                apartman_number: apartman_number,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                residental_park_id:selectedParkId
            },
            success: function(response) {
                
                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                $('#mib-spinner').hide();

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

                checkFavorites();
            }
        });

    });

    $(document).on('change', '.select-residential-park', function(e) {

        let parkId = $(this).val();
        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const cardContainer = $('#custom-card-container');
        const table = $('#custom-list-table-container');
        
        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;


        var minRoom = $("#custom-room-slider").slider("option", "values")[0];
        var maxRoom = $("#custom-room-slider").slider("option", "values")[1];

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minPrice = $("#custom-price-slider").slider("option", "values")[0];
        var maxPrice = $("#custom-price-slider").slider("option", "values")[1];

        var minSquare = $("#custom-square-slider").slider("option", "values")[0];
        var maxSquare = $("#custom-square-slider").slider("option", "values")[1];

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL-je
            type: 'POST',
            data: {
                action: 'set_residential_park_id',
                orientation: selectOritentation,
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value: minSquare, // terület
                slider_max_value: maxSquare, // terület
                availability: selectAvailability,
                garden_connection: selectGardenConnection,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode: shortcode,
                apartman_number: apartman_number,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                residental_park_id:parkId
            },
            success: function(response) {
                
                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                $('#mib-spinner').hide();

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(parkId);
            }
        });
    });
    //catalog-orientation-shortcode-checkbox
    
    const urlParams = new URLSearchParams(window.location.search);
    if ( urlParams.size>0) {

        $('#mib-spinner').show();

        

        // Alapértelmezett változók
        let minPrice = null, maxPrice = null;
        let minFloor = null, maxFloor = null;
        let minRoom = null, maxRoom = null;
        let minSquare = null, maxSquare = null;

        // Ellenőrzés és értékek beállítása
        if (urlParams.has('price_min') && urlParams.has('price_max')) {
            minPrice = parseInt(urlParams.get('price_min'));
            maxPrice = parseInt(urlParams.get('price_max'));
            if (!isNaN(minPrice) && !isNaN(maxPrice)) {

                const priceText = jQuery(".custom-price-range-value").text().trim(); // '0 - 200000000'
                const [minPriceStr, maxPriceStr] = priceText.split('-').map(str => str.trim());

                var setMinPrice = parseInt(minPriceStr);
                var setMaxPrice = parseInt(maxPriceStr);

                
            }
        }

        if (urlParams.has('floor_min') && urlParams.has('floor_max')) {
            minFloor = parseInt(urlParams.get('floor_min'));
            maxFloor = parseInt(urlParams.get('floor_max'));
            if (!isNaN(minFloor) && !isNaN(maxFloor)) {

                const floorText = jQuery(".custom-floor-range-value").text().trim(); // '0 - 200000000'
                const [minFloorTextStr, maxFlooreStr] = floorText.split('-').map(str => str.trim());

                var setMinFloor = parseInt(minFloorTextStr);
                var setMaxFloor = parseInt(maxFlooreStr);

                
            }
        }

        if (urlParams.has('room_min') && urlParams.has('room_max')) {
            minRoom = parseInt(urlParams.get('room_min'));
            maxRoom = parseInt(urlParams.get('room_max'));
            if (!isNaN(minRoom) && !isNaN(maxRoom)) {

                const roomText = jQuery(".custom-room-range-value").text().trim(); // '0 - 200000000'
                const [minRoomTextStr, maxRoomeStr] = roomText.split('-').map(str => str.trim());

                var setMinRoom = parseInt(minRoomTextStr);
                var setMaxRoom  = parseInt(maxRoomeStr);

            }
        }

        if (urlParams.has('area_min') && urlParams.has('area_max')) {
            minSquare = parseInt(urlParams.get('area_min'));
            maxSquare = parseInt(urlParams.get('area_max'));
            if (!isNaN(minSquare) && !isNaN(maxSquare)) {

                const squareText = jQuery(".custom-square-range-value").text().trim(); // '0 - 200000000'
                const [minSquareTextStr, maxSquareStr] = squareText.split('-').map(str => str.trim());

                var setMinSquare = parseInt(minSquareTextStr);
                var setMaxSquare  = parseInt(maxSquareStr);

                
            }
        }

        // (Opcionális) egyéb paraméterek lekérése
        const selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();
        const selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();
        const selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        const table = $('#custom-list-table-container');
        const cardContainer = $('#custom-card-container');
        const page_type = (cardContainer.length === 1) ? 'card' : 'table';

        // Indítható az AJAX, ha szükséges:
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'set_room_slider_values',
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                slider_min_value: minSquare, // terület min
                slider_max_value: maxSquare, // terület max
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                availability: selectAvailability,
                orientation: selectOritentation,
                garden_connection: selectGardenConnection,
                page_type: page_type,
            },
            success: function(response) {
                
                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                $('#mib-spinner').hide();
                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });
                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                const cleanUrl = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);

                initializeCatalogPriceSlider(minPrice, maxPrice, setMinPrice, setMaxPrice);
                initializeFloorSlider(minFloor, maxFloor, setMinFloor, setMaxFloor);
                initializeRoomSlider(minRoom, maxRoom, setMinRoom, setMaxRoom);
                initializeCatalogSquareSlider(minSquare, maxSquare, setMinSquare, setMaxSquare);


            },
            error: function(xhr, status, error) {
                
                $('#mib-spinner').hide();
                console.error('AJAX error:', error);
            }
        });

    }else{


        $.ajax({
            url: ajaxurl,
            data: {
                'action': 'get_price_slider_range_data',
                'shortcode': $('#custom-card-container').hasClass('shortcode-card') ? $('#custom-card-container').data('shortcode') : null
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if (response) {

                    const minFloor = response.min;
                    const maxFloor = response.max;

                    initializeCatalogPriceSlider(minFloor, maxFloor, minFloor, maxFloor);
                } else {
                    console.error('Hiba: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX hiba: ' + error);
            }
        });


        $.ajax({
            url: ajaxurl,
            data: {
                'action': 'get_square_slider_range_data',
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if (response) {

                    const minSquare = response.min;
                    const maxSquare = response.max;

                    initializeCatalogSquareSlider(minSquare, maxSquare, minSquare, maxSquare);
                } else {
                    console.error('Hiba: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX hiba: ' + error);
            }
        });

        $.ajax({
            url: ajaxurl,
            data: {
                'action': 'get_floor_slider_range_data',
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if (response) {

                    const minFloor = response.min;
                    const maxFloor = response.max;

                    initializeFloorSlider(minFloor, maxFloor, minFloor, maxFloor);
                } else {
                    console.error('Hiba: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX hiba: ' + error);
            }
        });

        $.ajax({
            url: ajaxurl,
            data: {
                'action': 'get_room_slider_range_data',
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if (response) {
                    const minFloor = response.min;
                    const maxFloor = response.max;

                    initializeRoomSlider(minFloor, maxFloor, minFloor, maxFloor);
                } else {
                    console.error('Hiba: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX hiba: ' + error);
            }
        });

    }

    function initializeRoomSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {

        const slider = $('#custom-room-slider');
        const output = $('.custom-room-range-value');

        if (!slider.length) return;

        // Ha már volt inicializálva
        if (slider.hasClass('ui-slider')) {
            slider.slider('destroy');
        }

        slider.slider({
            range: true,
            min: Number(defaultMin),
            max: Number(defaultMax),
            values: [Number(minValue), Number(maxValue)],
            step: 1,
            slide: function(event, ui) {
                
                output.text(ui.values[0] + ' - ' + ui.values[1]);
            },
            change: function(event, ui) {
                output.text(ui.values[0] + ' - ' + ui.values[1]);
                saveRoomSliderValues(ui.values[0], ui.values[1]);
            }
        });

        output.text(minValue + ' - ' + maxValue);

        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }
    

    function initializeCatalogPriceSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {
        const slider = $('#custom-price-slider');
        const output = $('.custom-price-range-value');

        if (!slider.length) return;

        // Ha már volt inicializálva
        if (slider.hasClass('ui-slider')) {
            slider.slider('destroy');
        }

        slider.slider({
            range: true,
            min: Number(defaultMin),
            max: Number(defaultMax),
            values: [Number(minValue), Number(maxValue)],
            step: 1,
            slide: function(event, ui) {
                
                output.text(formatCurrency(ui.values[0]) + " - " + formatCurrency(ui.values[1]));
            },
            change: function(event, ui) {
                output.text(formatCurrency(ui.values[0]) + " - " + formatCurrency(ui.values[1]));
                savePriceCatalogSliderValues(ui.values[0], ui.values[1]);
            }
        });

        output.text(formatCurrency(minValue) + " - " + formatCurrency(maxValue));

        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }

    function initializeCatalogSquareSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {
        const slider = $('#custom-square-slider');
        const output = $('.custom-square-range-value');

        if (!slider.length) return;

        // Ha már volt inicializálva
        if (slider.hasClass('ui-slider')) {
            slider.slider('destroy');
        }

        slider.slider({
            range: true,
            min: Number(defaultMin),
            max: Number(defaultMax),
            values: [Number(minValue), Number(maxValue)],
            step: 1,
            slide: function(event, ui) {
                
                output.text(formatSquareMeter(ui.values[0]) + " - " + formatSquareMeter(ui.values[1]));
            },
            change: function(event, ui) {
                output.text(formatSquareMeter(ui.values[0]) + " - " + formatSquareMeter(ui.values[1]));
                saveSquareCatalogSliderValues(ui.values[0], ui.values[1]);
            }
        });

        output.text(formatSquareMeter(minValue) + " - " + formatSquareMeter(maxValue));

        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }

    function initializeFloorSlider(minValue, maxValue, defaultMin = -1, defaultMax = 10) {
        const slider = $('#custom-floor-slider');
        const output = $('.custom-floor-range-value');

        if (!slider.length) return;

        // Ha már volt inicializálva
        if (slider.hasClass('ui-slider')) {
            slider.slider('destroy');
        }

        slider.slider({
            range: true,
            min: Number(defaultMin),
            max: Number(defaultMax),
            values: [Number(minValue), Number(maxValue)],
            step: 1,
            slide: function(event, ui) {

                output.text(ui.values[0] + ' - ' + ui.values[1]);
            },
            change: function(event, ui) {
                output.text(ui.values[0] + ' - ' + ui.values[1]);
                saveFloorSliderValues(ui.values[0], ui.values[1]);
            }
        });

        output.text(minValue + ' - ' + maxValue);

        setTimeout(() => checkIfAnyFilterIsActive(), 100);
    }

    function saveSquareCatalogSliderValues(minSquare, maxSquare) {

        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const table = $('#custom-list-table-container');
        const cardContainer = $('#custom-card-container');

         var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;
        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var minRoom = $("#custom-room-slider").slider("option", "values")[0];
        var maxRoom = $("#custom-room-slider").slider("option", "values")[1];

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minPrice = $("#custom-price-slider").slider("option", "values")[0];
        var maxPrice = $("#custom-price-slider").slider("option", "values")[1];

        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'set_catalog_square_slider_values',
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value:minSquare,//terület
                slider_max_value:maxSquare,//terület
                availability: selectAvailability,
                orientation: selectOritentation,
                garden_connection: selectGardenConnection,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode:shortcode,
                apartman_number: apartman_number,
                residental_park_id:selectedParkId
            },
            success: function(response) {

                $('#mib-spinner').hide();

                if ($('#custom-square-slider').hasClass('custom-filter')) {
                    return;
                }

                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                    checkFavorites();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

            },
            error: function(error) {
                console.log('Hiba történt az emelet csúszka mentésekor.');
                $('#mib-spinner').hide();
            }
        });
    }

    function savePriceCatalogSliderValues(minPrice, maxPrice) {


        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const table = $('#custom-list-table-container');
        const cardContainer = $('#custom-card-container');

         var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var minRoom = $("#custom-room-slider").slider("option", "values")[0];
        var maxRoom = $("#custom-room-slider").slider("option", "values")[1];

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minSquare = $("#custom-square-slider").slider("option", "values")[0];
        var maxSquare = $("#custom-square-slider").slider("option", "values")[1];

        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'set_catalog_price_slider_values',
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value:minSquare,//terület
                slider_max_value:maxSquare,//terület
                sort: sort,
                sortType: sortType,
                availability: selectAvailability,
                orientation: selectOritentation,
                garden_connection: selectGardenConnection,
                stairway:selectStairway,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode:shortcode,
                apartman_number: apartman_number,
                residental_park_id:selectedParkId
            },
            success: function(response) {

                $('#mib-spinner').hide();

                if ($('#custom-price-slider').hasClass('custom-filter')) {
                    return;
                }

                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                    checkFavorites();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

            },
            error: function(error) {
                console.log('Hiba történt az emelet csúszka mentésekor.');
                $('#mib-spinner').hide();
            }
        });
    }

    function saveFloorSliderValues(minFloor, maxFloor) {

        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const table = $('#custom-list-table-container');
        const cardContainer = $('#custom-card-container');

         var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();
        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var roomVals = $("#custom-room-slider").slider("option", "values") || [0,0];
        var minRoom = roomVals[0];
        var maxRoom = roomVals[1];

        var priceVals = $("#custom-price-slider").slider("option", "values") || [0,0];
        var minPrice = priceVals[0];
        var maxPrice = priceVals[1];

        var squareVals = $("#custom-square-slider").slider("option", "values") || [0,0];
        var minSquare = squareVals[0];
        var maxSquare = squareVals[1];

        // Include sort parameters in AJAX
        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'set_floor_slider_values',
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value: minPrice,
                price_slider_max_value: maxPrice,
                slider_min_value: minSquare, // terület
                slider_max_value: maxSquare, // terület
                availability: selectAvailability,
                orientation: selectOritentation,
                garden_connection: selectGardenConnection,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode: shortcode,
                apartman_number: apartman_number,
                residental_park_id:selectedParkId
            },
            success: function(response) {

                $('#mib-spinner').hide();

                if ($('#custom-floor-slider').hasClass('custom-filter')) {
                    return;
                }

                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                    checkFavorites();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }
                
                

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

            },
            error: function(error) {
                console.log('Hiba történt az emelet csúszka mentésekor.');
                $('#mib-spinner').hide();
            }
        });
    }

    function saveRoomSliderValues(minRoom, maxRoom) {

        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const table = $('#custom-list-table-container');
        const cardContainer = $('#custom-card-container');

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minPrice = $("#custom-price-slider").slider("option", "values")[0];
        var maxPrice = $("#custom-price-slider").slider("option", "values")[1];

        var minSquare = $("#custom-square-slider").slider("option", "values")[0];
        var maxSquare = $("#custom-square-slider").slider("option", "values")[1];

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        // Determine current sort settings
        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'set_room_slider_values',
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value:minPrice,
                price_slider_max_value:maxPrice,
                slider_min_value: minSquare, // terület
                slider_max_value: maxSquare, // terület
                availability: selectAvailability,
                orientation: selectOritentation,
                garden_connection: selectGardenConnection,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode: shortcode,
                apartman_number: apartman_number,
                residental_park_id:selectedParkId
            },
            success: function(response) {

                $('#mib-spinner').hide();

                if ($('#custom-room-slider').hasClass('custom-filter')) {
                    return;
                }

                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                    checkFavorites();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

            },
            error: function(error) {
                console.log('Hiba történt az emelet csúszka mentésekor.');
                $('#mib-spinner').hide();
            }
        });
    }

    $(document).on('click', '#grid-view', function () {

        $(this).addClass('active');
        $('#list-view').removeClass('active');

        const $firstContainer = $('#custom-card-container').first(); // csak az első blokk

        $firstContainer.find('.card-wrapper')
            .removeClass('col-md-12')
            .addClass('col-md-3');

        $firstContainer.find('.card')
            .removeClass('list-view')
            .addClass('grid-view');

        sessionStorage.setItem('currentView', 'grid');
    });

    $(document).on('click', '#favorite-view', function () {
        const isActive = $(this).hasClass('active');
        const favorites = getFavorites();

        if (isActive) {
            // Ha már aktív, akkor visszaállítjuk a teljes nézetet
            $(this).removeClass('active');

            // Visszaállítjuk az előző nézetet (pl. grid vagy list)
            const previousView = sessionStorage.getItem('previousView') || 'grid';
            sessionStorage.setItem('currentView', previousView);

            if (previousView === 'grid') {
                $('#grid-view').addClass('active');
                $('#list-view').removeClass('active');

                $('.card-wrapper').show().removeClass('col-md-12').addClass('col-md-3');
                $('.card').removeClass('list-view').addClass('grid-view');
            } else {
                $('#list-view').addClass('active');
                $('#grid-view').removeClass('active');

                $('.card-wrapper').show().removeClass('col-md-3').addClass('col-md-12');
                $('.card').removeClass('grid-view').addClass('list-view');
            }

        } else {
            // Favorite nézet aktiválása
            $(this).addClass('active');
            $('#list-view, #grid-view').removeClass('active');

            // Elmentjük, melyik volt az előző nézet
            const currentView = sessionStorage.getItem('currentView') || 'grid';
            sessionStorage.setItem('previousView', currentView);
            sessionStorage.setItem('currentView', 'favorite');

            $('.card-wrapper').each(function () {
                const id = $(this).data('id');
                if (favorites.includes(id)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('.card')
                .removeClass('grid-view list-view')
                .addClass('grid-view');

            $('.card-wrapper')
                .removeClass('col-md-12')
                .addClass('col-md-3');
        }
    });

    $(document).on('click', '#list-view', function () {

        $(this).addClass('active');
        $('#grid-view').removeClass('active');

        const $firstContainer = $('#custom-card-container').first(); // csak az első blokk

        $firstContainer.find('.card-wrapper')
            .removeClass('col-md-3')
            .addClass('col-md-12');

        $firstContainer.find('.card')
            .removeClass('grid-view')
            .addClass('list-view');

        sessionStorage.setItem('currentView', 'list');
    });

    $(document).on('click', '#reset-filters-button', function (e) {

        e.preventDefault();

        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        var table = $('#custom-list-table-container');

        var cardContainer = $('#custom-card-container');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'delete_catalog_filters',
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode:shortcode,
                apartman_number: apartman_number
            },
            success: function(response) {
                if (table.length>0) {
                  table.replaceWith(response.data.html);
                }else{
                   cardContainer.replaceWith(response.data.html);
                   restoreViewMode();
                   hoverDropDown();
                }
                $('#mib-spinner').hide();
                

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(response.data.room_slider_min, response.data.room_slider_max, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(response.data.floor_slider_min, response.data.floor_slider_max, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(response.data.price_slider_min, response.data.price_slider_max, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(response.data.slider_min, response.data.slider_max, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                if (response.data.orientation_filter == null) {
                    $("#orientationDropdown").parent().parent().remove();
                }
                if (response.data.available_only == null) {
                    $("#availabilityDropdown").parent().parent().remove();
                }
            },
            error: function(error) {

                $('#mib-spinner').hide();

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(response.data.room_slider_min, response.data.room_slider_max, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(response.data.floor_slider_min, response.data.floor_slider_max, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(response.data.price_slider_min, response.data.price_slider_max, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(response.data.slider_min, response.data.slider_max, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }
            }
        });
    });

    
    $(document).on('change', '.catalog-stairway-checkbox', function (e) {
        
        $('#mib-spinner').show();

        var shortcode = '';
        var apartman_number = '';
        if ($('#custom-card-container').hasClass('shortcode-card')) {
            shortcode = $('#custom-card-container').data('shortcode');
            apartman_number = $('#custom-card-container').data('apartman_number');
        }

        const table = $('#custom-list-table-container');
        const cardContainer = $('#custom-card-container');

        var roomVals = $("#custom-room-slider").slider("option", "values") || [0, 0];
        var minRoom = roomVals[0];
        var maxRoom = roomVals[1];

        var minFloor = $("#custom-floor-slider").slider("option", "values")[0];
        var maxFloor = $("#custom-floor-slider").slider("option", "values")[1];

        var minPrice = $("#custom-price-slider").slider("option", "values")[0];
        var maxPrice = $("#custom-price-slider").slider("option", "values")[1];

        var minSquare = $("#custom-square-slider").slider("option", "values")[0];
        var maxSquare = $("#custom-square-slider").slider("option", "values")[1];

        var selectAvailability = $('.catalog-availability-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;

        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        // Determine current sort settings
        var sortParts = $('#mib-sort-select').val() ? $('#mib-sort-select').val().split('|') : [];
        var sort = sortParts[0] || '';
        var sortType = sortParts[1] || 'ASC';

        var selectedParkId = $('.select-residential-park').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'set_stairway_values',
                floor_slider_min_value: minFloor,
                floor_slider_max_value: maxFloor,
                room_slider_min_value: minRoom,
                room_slider_max_value: maxRoom,
                price_slider_min_value:minPrice,
                price_slider_max_value:maxPrice,
                slider_min_value: minSquare, // terület
                slider_max_value: maxSquare, // terület
                availability: selectAvailability,
                orientation: selectOritentation,
                garden_connection: selectGardenConnection,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                page_type: (cardContainer.length == 1) ? 'card' : 'table',
                shortcode: shortcode,
                apartman_number: apartman_number,
                residental_park_id:selectedParkId
            },
            success: function(response) {

                $('#mib-spinner').hide();

                if ($('#custom-room-slider').hasClass('custom-filter')) {
                    return;
                }

                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                    hoverDropDown();
                    checkFavorites();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(minRoom, maxRoom, response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(minFloor, maxFloor, response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(minPrice, maxPrice, response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(minSquare, maxSquare, response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });
                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectAvailability.forEach(function(value) {
                    $('.catalog-availability-checkbox[value="' + value + '"]').prop('checked', true);
                });

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);

                checkFavorites();

            },
            error: function(error) {
                console.log('Hiba történt az emelet csúszka mentésekor.');
                $('#mib-spinner').hide();
            }
        });
    });


    function restoreViewMode() {
        
        const currentView = sessionStorage.getItem('currentView');
        const isMobile = window.innerWidth < 768;

        const $containers = $('#custom-card-container'); // minden ilyen elem
        const $firstContainer = $containers.first(); // csak az elsővel dolgozunk

        // Új logika: ha csak 1 van, és van data-shortcode attribútuma, akkor kilépünk
        if ($containers.length === 1 && $firstContainer.is('[data-shortcode]')) {
            return; // nem módosítjuk a nézetet
        }

        if (isMobile) {
            // Mobilon mindig grid nézet
            $('#grid-view').addClass('active');
            $('#list-view').removeClass('active');

            $firstContainer.find('.card-wrapper')
                .removeClass('col-md-12')
                .addClass('col-md-3');

            $firstContainer.find('.card')
                .removeClass('list-view')
                .addClass('grid-view');

        } else {
            if (currentView === 'list') {
                $('#list-view').addClass('active');
                $('#grid-view').removeClass('active');

                $firstContainer.find('.card-wrapper')
                    .removeClass('col-md-3')
                    .addClass('col-md-12');

                $firstContainer.find('.card')
                    .removeClass('grid-view')
                    .addClass('list-view');

            } else {
                $('#grid-view').addClass('active');
                $('#list-view').removeClass('active');

                $firstContainer.find('.card-wrapper')
                    .removeClass('col-md-12')
                    .addClass('col-md-3');

                $firstContainer.find('.card')
                    .removeClass('list-view')
                    .addClass('grid-view');
            }
        }
    }

    function checkIfAnyFilterIsActive() {

        let isActive = false;

        const filters = [
            { id: "#custom-floor-slider", key: "floor" },
            { id: "#custom-price-slider", key: "price" },
            { id: "#custom-room-slider", key: "room" },
            { id: "#custom-square-slider", key: "square" }
        ];

        filters.forEach(filter => {
            const el = document.querySelector(filter.id);
            if (el && el.noUiSlider) {
                // noUiSlider.get() returns array of strings
                const values = el.noUiSlider.get().map(Number);
                const range = el.noUiSlider.options.range || {};
                const min = Number(range.min);
                const max = Number(range.max);
                if (values[0] !== min || values[1] !== max) {
                    isActive = true;
                }
            }
        });

        const selectOrientation = $('.catalog-orientation-checkbox:checked').length;
        const selectAvailability = $('.catalog-availability-checkbox:checked').length;
        const selectGardenConnection = $('.catalog-gardenconnection-checkbox:checked').length;
        const selectStairway = $('.catalog-stairway-checkbox:checked').length;

        if (selectOrientation > 0 || selectAvailability > 0 || selectGardenConnection>0 || selectStairway>0) {
            isActive = true;
        }

        if ($(".availability-checkbox:checked").length > 0) {
            isActive = true;
        }

        $(".reset-filters-wrapper").css('display', isActive ? 'inline-block' : 'none');
    }

    // Ellenőrizzük, hogy létezik-e a keresés gomb
    if ($('#search-apartman-btn').length) {

        const sliders = [
            { id: 'custom-price-slider', key: 'price' },
            { id: 'custom-floor-slider', key: 'floor' },
            { id: 'custom-room-slider', key: 'room' },
            { id: 'custom-square-slider', key: 'area' },
        ];

        function getSliderParams() {
            const params = new URLSearchParams();

            sliders.forEach(slider => {
                const $slider = $('#' + slider.id);
                if ($slider.length && $slider.data("ui-slider")) {
                    const values = $slider.slider('option', 'values');
                    if (Array.isArray(values)) {
                        params.set(slider.key + '_min', values[0]);
                        params.set(slider.key + '_max', values[1]);
                    }
                }
            });

            return params.toString();
        }

        $('#search-apartman-btn').on('click', function(e) {
            e.preventDefault();
            const paramStr = getSliderParams();
            const newUrl = '/lakaslista' + (paramStr ? '?' + paramStr : '');
            window.location.href = newUrl;
        });
    }


    // Először betöltjük a korábbi kedvenceket sütiből
    function getFavorites() {
        const cookie = Cookies.get('favorites'); // js-cookie plugin szükséges
        return cookie ? JSON.parse(cookie) : [];
    }

    function saveFavorites(favorites) {
        Cookies.set('favorites', JSON.stringify(favorites), { expires: 365 });
    }

    function checkFavorites() {
        // Betöltéskor a szívek megjelenésének frissítése
        $('.favorite-icon').each(function () {
            const apartmentId = $(this).data('id');
            const favorites = getFavorites();

            if (favorites.includes(apartmentId)) {
                // Ha kedvenc, akkor kitöltött szív
                $(this)
                    .removeClass('fa-regular fa-heart')
                    .addClass('fa-solid fa-heart third-text-color');
            } else {
                // Ha nem kedvenc, akkor üres szív
                $(this)
                    .removeClass('fa-solid fa-heart third-text-color')
                    .addClass('fa-regular fa-heart');
            }
        });
    }

    // Szív ikon kattintás esemény
    $(document).on('click', '.favorite-icon', function (e) {
        const apartmentId = $(this).data('id');
        let favorites = getFavorites();

        if (!favorites.includes(apartmentId)) {
            favorites.push(apartmentId);
            $(this)
                .removeClass('fa-regular fa-heart')
                .addClass('fa-solid fa-heart third-text-color'); // kedvenchez adva
        } else {
            favorites = favorites.filter(id => id !== apartmentId);
            $(this)
                .removeClass('fa-solid fa-heart third-text-color')
                .addClass('fa-regular fa-heart'); // eltávolítva a kedvencek közül
        }

        saveFavorites(favorites);
    });

    // Init sort filter AJAX
    $(document).on('change', '#mib-sort-select', function() {

        $('#mib-spinner').show();
        var table = $('#custom-list-table-container');
        var cardContainer = $('#custom-card-container');
        var shortcode = '';
        var apartman_number = '';
        if (cardContainer.hasClass('shortcode-card')) {
            shortcode = cardContainer.data('shortcode');
            apartman_number = cardContainer.data('apartman_number');
        }
        // Collect current filters
        var selectedFloor = $("#custom-floor-slider").slider("values") || [0, 0];
        var selectedRoom = $("#custom-room-slider").slider("values") || [0, 0];

        var selectOritentation = $('.catalog-orientation-checkbox:checked').map(function() {
            return this.value;
        }).get();
        
        var selectAvailability = $('.availability-checkbox:checked').map(function() { return this.value; }).get();

        var selectStairway = $('.catalog-stairway-checkbox:checked').map(function() {
            return this.value;
        }).get();
        // Get slider values
        // Get slider values (fallback to [0,0] if slider not present)
        var squareRange = $("#custom-square-slider").slider("values") || [0, 0];
        var priceRange = $("#custom-price-slider").slider("values") || [0, 0];
        // Sort parameters
        var parts = this.value.split('|');
        var sort = parts[0] || '';
        var sortType = parts[1] || 'ASC';
        // Determine page type
        var pageType = (cardContainer.length === 1) ? 'card' : 'table';

        var selectGardenConnection = $('.catalog-gardenconnection-checkbox').is(':checked') ? 1 : 0;
        var wasAdvancedFiltersVisible = $('#advanced-filters').is(':visible');

        var selectedParkId = $('.select-residential-park').val();

        // AJAX request
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'set_sort_values',
                slider_min_value: squareRange[0],
                slider_max_value: squareRange[1],
                price_slider_min_value: priceRange[0],
                price_slider_max_value: priceRange[1],
                floor_slider_min_value: selectedFloor[0],
                floor_slider_max_value: selectedFloor[1],
                room_slider_min_value:selectedRoom[0],
                room_slider_max_value:selectedRoom[1],
                orientation: selectOritentation,
                availability: selectAvailability,
                garden_connection: selectGardenConnection,
                stairway:selectStairway,
                sort: sort,
                sortType: sortType,
                page_type: pageType,
                shortcode: shortcode,
                apartman_number: apartman_number,
                residental_park_id:selectedParkId
            },
            success: function(response) {
                if (table.length > 0) {
                    table.replaceWith(response.data.html);
                } else {
                    cardContainer.replaceWith(response.data.html);
                    restoreViewMode();
                }

                if (wasAdvancedFiltersVisible) {
                    $('#advanced-filters').css('display', 'flex');
                    $('#toggle-advanced-filters').html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
                }

                $('#mib-spinner').hide();
                
                hoverDropDown();
                updateFloorSelectionCount('floor', 'Emelet');
                updateFloorSelectionCount('room', 'Szobák');
                updateFloorSelectionCount('orientation', 'Tájolás');

                if (response.data.room_slider_min != null) {
                    initializeRoomSlider(selectedRoom[0], selectedRoom[1], response.data.room_slider_min, response.data.room_slider_max);
                }else{
                    $("#custom-room-slider").parent().remove();
                }

                if (response.data.floor_slider_min != null) {
                    initializeFloorSlider(selectedFloor[0], selectedFloor[1], response.data.floor_slider_min, response.data.floor_slider_max);
                }else{
                    $("#custom-floor-slider").parent().remove();
                }

                if (response.data.price_slider_min != null) {
                    initializeCatalogPriceSlider(priceRange[0], priceRange[1], response.data.price_slider_min, response.data.price_slider_max);
                }else{
                    $("#custom-price-slider").parent().remove();
                }
                
                if (response.data.slider_min != null) {
                    initializeCatalogSquareSlider(squareRange[0], squareRange[1], response.data.slider_min, response.data.slider_max);
                }else{
                    $("#custom-square-slider").parent().remove();
                }

                selectOritentation.forEach(function(value) {
                    $('.catalog-orientation-checkbox[value="' + value + '"]').prop('checked', true);
                });

                if (selectGardenConnection == 1) {
                    $('.catalog-gardenconnection-checkbox').prop('checked', true);
                } else {
                    $('.catalog-gardenconnection-checkbox').prop('checked', false);
                }

                selectStairway.forEach(function(value) {
                    $('.catalog-stairway-checkbox[value="' + value + '"]').prop('checked', true);
                });

                $('.select-residential-park').val(selectedParkId);
            },
            error: function(error) {
                console.error('Hiba a rendezés AJAX közben:', error);
                $('#mib-spinner').hide();
            }
        });
    });
    checkFavorites();


    $(document).on('click', '#toggle-advanced-filters', function() {
        const $filters = $('#advanced-filters');
        const $button = $(this);

        if ($filters.is(':visible')) {
            $filters.hide();
            $button.html('<i class="fas fa-sliders-h me-1"></i> További szűrők');
        } else {
            $filters.css('display', 'flex');
            $button.html('<i class="fas fa-times me-1"></i> Szűrők bezárása');
        }
    });

});
