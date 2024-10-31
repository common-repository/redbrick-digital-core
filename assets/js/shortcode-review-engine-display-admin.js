function RBD_insertAtCaret(textAreaID, text){
	let textArea  = document.getElementById(textAreaID),
		scrollPos = textArea.scrollTop,
		strPos    = 0,
		br        = ( (textArea.selectionStart || textArea.selectionStart == '0') ? "ff" :
						(document.selection ? "ie" :
							false ) );
	if( br == 'ie' ){
		textArea.focus();
		let range = document.selection.createRange();
		range.moveStart('character', -textArea.value.length);
		strPos = range.text.length;
	} else if( br == "ff"){
		strPos = textArea.selectionStart;
	}

	let front = textArea.value.substring(0, strPos),
		back  = textArea.value.substring(strPos, textArea.value.length);

	textArea.value = front+text+back;
	strPos         = strPos + text.length;

	if( br == 'ie' ){
		textArea.focus();
		let range = document.selection.createRange();
		range.moveStart('character', -textArea.value.length);
		range.moveStart('character', strPos);
		range.moveEnd('character', 0);
		range.select();
	} else if( br == "ff" ){
		textArea.selectionStart = strPos;
		textArea.selectionEnd = strPos;
		textArea.focus();
	}
	
	textArea.scrollTop = scrollPos;
}

let rbdShortcodeForm = document.querySelector('form#review-engine-display');
rbdShortcodeForm.onsubmit = function(e){
	e.preventDefault();

	let parameters = serialize(this),
		components = decodeURIComponent( parameters ),
		generated  = '[rbd_review_engine '+ components +'"]',
		shortcode  = generated.replace(/\&/g, '" ').replace(/\=/g, '="').replace(/\+/g, ' ').replace(new RegExp('" ]', 'g'), '"]');

	let currentEditor = document.querySelector('.wp-editor-area');

	RBD_insertAtCaret( currentEditor.getAttribute('id'), shortcode );
	tinyMCE.execCommand( 'mceInsertContent', false, shortcode );

	RBD_closePopup();
}

$('.rbd-form').on( 'submit', function( event ) {
	event.preventDefault();

	var generated = $(this).serialize();
		console.log(generated);
		//Do stuff to "generated"
		generated = decodeURIComponent( generated );
		generated = '[rbd_review_engine '+ generated +'"]';
		generated = generated.replace(/\&/g, '" ').replace(/\=/g, '="').replace(/\+/g, ' ').replace(new RegExp('" ]', 'g'), '"]');

	var textAreaaffected = $('.wp-editor-area').attr("id");
	insertAtCaret(textAreaaffected, generated);
	tinyMCE.execCommand('mceInsertContent', false, generated);

	$('.rbd-re-popup-cloud').fadeOut().addClass('hidden');
	//console.log( $( this ).serialize() );
});

let rbdShortcodeButton = document.querySelector('#rbd-review-engine-display-button');
rbdShortcodeButton.onclick = function(){
	rbdPopupContainer.classList.add('rbd-shown');
	rbdPopupContainer.parentNode.classList.add('rbd-review-engine-display-admin');
}

let rbdSync = document.querySelector('#rbd-popup-container .rbd-sync');
rbdSync.onclick = function(){

	this.classList.add('rbd-rotate');
	let syncURL = this.parentNode.querySelector('[name="url"]').value;
	RBD_dyanmicAPIdata( syncURL, this );

}

function RBD_dyanmicAPIdata(url){
	url = url.replace('https://', '');
	url = url.replace('http://', '');

	let xhr = RBD_createCORSRequest('GET', 'https://'+ url + '/reviews-api-v2/?user=test&key=cdff.1070bdaacf&reviews_per_page=1');

	if( !xhr ){
		throw new Error('CORS not supported');
	} else {
		xhr.onload = (function(response) {
			if( xhr.status == 200 ){
				let response = xhr.response,
					json     = JSON.parse(response),
					tax      = json.company[0].taxonomies[0];
				
				let services  = tax.service[0],
					employees = tax.employee[0],
					locations = tax.location[0];

				let servicesSelect  = document.querySelector('#rbd-popup-container #service'),
					employeesSelect = document.querySelector('#rbd-popup-container #employee'),
					locationsSelect = document.querySelector('#rbd-popup-container #location');

				if( 0 < services.length ){
					servicesSelect.style.display = 'block';
					servicesSelect.classList.remove('rbd-pulse');
					servicesSelect.removeAttribute('disabled');
					servicesSelect.innerHTML = '<option value="all">All</option>';
					services.forEach(function(option){
						var opt = document.createElement('option');
						opt.value = option.slug;
						opt.innerHTML = option.name;
						servicesSelect.appendChild(opt);
					});
					setTimeout(function(){ servicesSelect.classList.add('rbd-pulse'); }, 50 );
				} else {
					servicesSelect.innerHTML = '<option value="all">N/A</option>';
					servicesSelect.setAttribute('disabled', 'disabled');
				}

				if( 0 < employees.length ){
					employeesSelect.style.display = 'block';
					employeesSelect.classList.remove('rbd-pulse');
					employeesSelect.removeAttribute('disabled');
					employeesSelect.innerHTML = '<option value="all">All</option>';
					employees.forEach(function(option){
						var opt = document.createElement('option');
						opt.value = option.slug;
						opt.innerHTML = option.name;
						employeesSelect.appendChild(opt);
					});
					setTimeout(function(){ employeesSelect.classList.add('rbd-pulse'); }, 50 );
				} else {
					employeesSelect.innerHTML = '<option value="all">N/A</option>';
					employeesSelect.setAttribute('disabled', 'disabled');
				}

				if( 0 < locations.length ){
					locationsSelect.style.display = 'block';
					locationsSelect.classList.remove('rbd-pulse');
					locationsSelect.removeAttribute('disabled');
					locationsSelect.innerHTML = '<option value="all">All</option>';
					locations.forEach(function(option){
						var opt = document.createElement('option');
						opt.value = option.slug;
						opt.innerHTML = option.name;
						locationsSelect.appendChild(opt);
					});
					setTimeout(function(){ locationsSelect.classList.add('rbd-pulse'); }, 50 );
				} else {
					locationsSelect.innerHTML = '<option value="all">N/A</option>';
					locationsSelect.setAttribute('disabled', 'disabled');
				}
			} else {
				alert( 'Review Engine API Request Failed. Check the URL and try again.' );
			}
			rbdSync.classList.remove('rbd-rotate');
		}).bind(this);

		xhr.onerror = (function( response ){
			alert( 'Review Engine API Request Failed. Check the URL and try again.' );
			rbdSync.classList.remove('rbd-rotate');
		});
		xhr.send();
	}

}