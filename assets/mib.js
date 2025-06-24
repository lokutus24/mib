window.addEventListener("load", function() {

	// store tabs variables
	var tabs = document.querySelectorAll("ul.nav-tabs > li");

	for (i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener("click", switchTab);
	}

	function switchTab(event) {
		event.preventDefault();

		document.querySelector("ul.nav-tabs li.active").classList.remove("active");
		document.querySelector(".tab-pane.active").classList.remove("active");

		var clickedTab = event.currentTarget;
		var anchor = event.target;
		var activePaneID = anchor.getAttribute("href");

		clickedTab.classList.add("active");
		document.querySelector(activePaneID).classList.add("active");

	}

});


jQuery(document).ready(function($) {

	$('.toggle-filter').on('change', function () {
        const target = $(this).data('target');
        $('#' + target).toggle(this.checked);
    });

    $('#reset').click(function () {
	    setTimeout(function () {
	        $('#shortcode_name').prop('readonly', false);
	        residentialParkIds = []; // tagek logikájának törlése
	        renderParkTags();        // friss megjelenítés: üres
	        apartmentSkus = [];
			renderSkuTags();
	        $('.filter-extra-inputs').hide();
	    }, 0);
	});

    let residentialParkIds = [];
    let apartmentSkus = [];

    function renderSkuTags() {
	    const container = $('#apartment_skus_tags');
	    container.empty();
	    apartmentSkus.forEach(function(sku, index) {
	        const tag = $(`<span class="tag" style="display:inline-block;padding:5px 10px;margin:5px;background:#e2e2e2;border-radius:3px;">${sku} <a href="#" data-index="${index}" class="remove-sku-tag" style="margin-left:5px;color:red;text-decoration:none;">×</a></span>`);
	        container.append(tag);
	    });
	    $('#apartment_skus_hidden').val(apartmentSkus.join(','));
	}

	function renderParkTags() {
	    const container = $('#residential_park_tags');
	    container.empty();
	    residentialParkIds.forEach(function(id, index) {
	        const tag = $(`<span class="tag" style="display:inline-block;padding:5px 10px;margin:5px;background:#e2e2e2;border-radius:3px;">${id} <a href="#" data-index="${index}" class="remove-tag" style="margin-left:5px;color:red;text-decoration:none;">×</a></span>`);
	        container.append(tag);
	    });
	    $('#residential_park_ids_hidden').val(residentialParkIds.join(','));
	}

	$(document).on('click', '#add_residential_park_id', function () {
	    const value = parseInt($('#residential_park_input').val());
	    if (!isNaN(value) && !residentialParkIds.includes(value)) {
	        residentialParkIds.push(value);
	        renderParkTags();
	        $('#residential_park_input').val('');
	    }
	});

	$(document).on('click', '.remove-tag', function (e) {
	    e.preventDefault();
	    const index = $(this).data('index');
	    residentialParkIds.splice(index, 1);
	    renderParkTags();
	});

	$(document).on('click', '#add_apartment_sku', function () {
	    const value = $('#apartment_sku_input').val().trim();
	    if (value !== '' && !apartmentSkus.includes(value)) {
	        apartmentSkus.push(value);
	        renderSkuTags();
	        $('#apartment_sku_input').val('');
	    }
	});

	$(document).on('click', '.remove-sku-tag', function (e) {
	    e.preventDefault();
	    const index = $(this).data('index');
	    apartmentSkus.splice(index, 1);
	    renderSkuTags();
	});

	// Resetnél:
        $('#shortcode-form').on('reset', function () {
            setTimeout(function () {
                residentialParkIds = [];
                renderParkTags();

                apartmentSkus = [];
                        renderSkuTags();
                $('#shortcode_type').val('');
            }, 0);
        });

    // Shortcode szerkesztés
	$(document).on('click', '.edit-shortcode', function(e) {
	    e.preventDefault();
	    const shortcode = $(this).data('shortcode');

	    $.ajax({
	        url: ajax_object.ajaxurl,
	        type: 'POST',
	        data: {
	            action: 'mib_get_shortcode',
	            shortcode: shortcode
	        },
	        success: function(response) {
	            if (response.success) {
	                const data = response.data;

	                $('#shortcode_name').val(shortcode).prop('readonly', true);

	                // TÖBBES Residential Park ID-k betöltése tagként
                        residentialParkIds = (data.residential_park_ids || []).map(Number);
                                renderParkTags();

                                apartmentSkus = Array.isArray(data.apartment_skus) ? data.apartment_skus : [];
                                        renderSkuTags();

                        $('#shortcode_type').val(data.type || '');

	                $('#residential_park_input').val('');
	                $('input[name="filters[]"]').prop('checked', false);
	                $('.filter-extra-inputs').hide().find('input').val('');

	                if (Array.isArray(data.filters)) {
	                    data.filters.forEach(function(filter) {
	                        $('input[name="filters[]"][value="' + filter + '"]').prop('checked', true);
	                        $('#' + filter + '-inputs').show();
	                    });
	                }

	                $('#number_of_apartment').val('');
	                //console.log(data.number_of_apartment);
	                if (data.number_of_apartment) {
	                	$('#number_of_apartment').val(data.number_of_apartment);

	                }	                

	                if (data.ranges) {
	                    Object.entries(data.ranges).forEach(function([key, range]) {
	                        if (range.min !== undefined) {
	                            $('input[name="' + key + '_min"]').val(range.min);
	                        }
	                        if (range.max !== undefined) {
	                            $('input[name="' + key + '_max"]').val(range.max);
	                        }
	                    });
	                }

	                $('input[name="extras[]"]').prop('checked', false);
	                if (Array.isArray(data.extras)) {
	                    data.extras.forEach(function(extra) {
	                        $('input[name="extras[]"][value="' + extra + '"]').prop('checked', true);
	                    });
	                }
	            } else {
	                alert('Nem sikerült betölteni.');
	            }
	        }
	    });
	});

    // Shortcode törlés
    $(document).on('click', '.delete-shortcode', function(e) {
        e.preventDefault();
        const shortcode = $(this).data('shortcode');

        if (!confirm('Biztosan törlöd?')) return;

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'mib_delete_shortcode',
                shortcode: shortcode
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Nem sikerült törölni.');
                }
            }
        });
    });

});
