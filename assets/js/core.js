if( typeof serialize != 'function' ){ 
	function serialize(form){if(!form||form.nodeName!=="FORM"){return }var i,j,q=[];for(i=form.elements.length-1;i>=0;i=i-1){if(form.elements[i].name===""){continue}switch(form.elements[i].nodeName){case"INPUT":switch(form.elements[i].type){case"email":case"tel":case"number":case"text":case"hidden":case"password":case"button":case"reset":case"submit":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"checkbox":case"radio":if(form.elements[i].checked){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value))}break;case"file":break}break;case"TEXTAREA":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"SELECT":switch(form.elements[i].type){case"select-one":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"select-multiple":for(j=form.elements[i].options.length-1;j>=0;j=j-1){if(form.elements[i].options[j].selected){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].options[j].value))}}break}break;case"BUTTON":switch(form.elements[i].type){case"reset":case"submit":case"button":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break}break}}return q.join("&")};
}

// Render SVG Icons
const rbdIcons = {
	'close': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"></path></svg>',
	'star':  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 21"><path d="M11.4428797,0.682367821 L13.1475073,5.91258374 C13.3362303,6.49202457 13.8779163,6.88340127 14.4879506,6.88340127 L20.0068228,6.88340127 C20.9667598,6.88340127 21.3666485,8.10734296 20.5893138,8.66950222 L16.1242299,11.9021721 C15.6304898,12.2600022 15.4244247,12.8943375 15.6131477,13.4727617 L17.3177753,18.7029777 C17.6146315,19.6128014 16.5700242,20.3691242 15.7937097,19.8069649 L11.3286258,16.574295 C10.8348856,16.2164649 10.1656841,16.2164649 9.67194396,16.574295 L5.20686008,19.8069649 C4.43054552,20.3691242 3.38491815,19.6128014 3.68177431,18.7029777 L5.38742207,13.4727617 C5.57614506,12.8943375 5.36905984,12.2600022 4.87531969,11.9021721 L0.410235806,8.66950222 C-0.366078756,8.10734296 0.0327898298,6.88340127 0.992726759,6.88340127 L6.51159901,6.88340127 C7.12265344,6.88340127 7.6633193,6.49202457 7.85204228,5.91258374 L9.55769005,0.682367821 C9.85454621,-0.22745594 11.1460235,-0.22745594 11.4428797,0.682367821"></path></svg>',
	'chart': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M500 400c6.6 0 12 5.4 12 12v24c0 6.6-5.4 12-12 12H12c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h24c6.6 0 12 5.4 12 12v324h452zm-356-60v-72c0-6.6-5.4-12-12-12h-24c-6.6 0-12 5.4-12 12v72c0 6.6 5.4 12 12 12h24c6.6 0 12-5.4 12-12zm96 0V140c0-6.6-5.4-12-12-12h-24c-6.6 0-12 5.4-12 12v200c0 6.6 5.4 12 12 12h24c6.6 0 12-5.4 12-12zm96 0V204c0-6.6-5.4-12-12-12h-24c-6.6 0-12 5.4-12 12v136c0 6.6 5.4 12 12 12h24c6.6 0 12-5.4 12-12zm96 0V108c0-6.6-5.4-12-12-12h-24c-6.6 0-12 5.4-12 12v232c0 6.6 5.4 12 12 12h24c6.6 0 12-5.4 12-12z" class=""></path></svg>',
	'sync':  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M483.515 28.485L431.35 80.65C386.475 35.767 324.485 8 256 8 123.228 8 14.824 112.338 8.31 243.493 7.971 250.311 13.475 256 20.301 256h28.045c6.353 0 11.613-4.952 11.973-11.294C66.161 141.649 151.453 60 256 60c54.163 0 103.157 21.923 138.614 57.386l-54.128 54.129c-7.56 7.56-2.206 20.485 8.485 20.485H492c6.627 0 12-5.373 12-12V36.971c0-10.691-12.926-16.045-20.485-8.486zM491.699 256h-28.045c-6.353 0-11.613 4.952-11.973 11.294C445.839 370.351 360.547 452 256 452c-54.163 0-103.157-21.923-138.614-57.386l54.128-54.129c7.56-7.56 2.206-20.485-8.485-20.485H20c-6.627 0-12 5.373-12 12v143.029c0 10.691 12.926 16.045 20.485 8.485L80.65 431.35C125.525 476.233 187.516 504 256 504c132.773 0 241.176-104.338 247.69-235.493.339-6.818-5.165-12.507-11.991-12.507z" class=""></path></svg>',
}

const rbdPopupContainer = document.querySelector('#rbd-popup-container');

function RBD_renderSVG(){
	let renderSVG = document.querySelectorAll('.renderSVG');

	for( i = 0, n = renderSVG.length; i < n; ++i ){
		let el     = renderSVG[i],
			icon   = el.getAttribute('data-icon'),
			repeat = el.getAttribute('data-repeat');

		if( !el.classList.contains('rendered') ){
			el.innerHTML = repeat != null ? rbdIcons[icon].repeat(repeat) : rbdIcons[icon];
			el.classList.add('rendered');
		}
	}
}

function RBD_strpos(haystack, needle, offset) {
	var i = (haystack+'').indexOf(needle, (offset || 0));
	return i === -1 ? false : i;
}

function RBD_closePopup(){
	if( rbdPopupContainer != null ){
		if( rbdPopupContainer.classList.contains('rbd-shown') ){
			rbdPopupContainer.classList.remove('rbd-shown');

			if( !rbdPopupContainer.parentNode.classList.contains('rbd-review-engine-display-admin') ){
				rbdPopupContainer.style.height = 0;
				let rbdDynamicContent = rbdPopupContainer.querySelector('.rbd-dynamic');
				rbdPopupContainer.querySelector('.rbd-popup-content').removeChild( rbdDynamicContent );
			}
		}
	}
}

window.onresize = function(event) {
	if( rbdPopupContainer != null ){
		rbdPopupContainer.style.width  = document.body.clientWidth;
		rbdPopupContainer.style.height = document.body.clientHeight;
	}
}

window.onkeyup = function(e){
	if( e.keyCode == 27 ){
		RBD_closePopup();
	}
}

let rbdPopupClose = document.querySelector('.rbd-popup-close');
if( rbdPopupClose != null ){
	rbdPopupClose.onclick = function(){
		RBD_closePopup();
	}
}

if( rbdPopupContainer != null ){
	rbdPopupContainer.onclick = function(e){
		if( e.target == this ){
			RBD_closePopup();
		}
	}
}

window.addEventListener('DOMContentLoaded', function() {
	RBD_renderSVG();
}, true);

// XHR CORS (Ajax) Request
function RBD_createCORSRequest(method, url) {
	var xhr = new XMLHttpRequest();
	if( 'withCredentials' in xhr ){
		xhr.open(method, url, true);
	} else if( typeof XDomainRequest != 'undefined' ){
		xhr = new XDomainRequest();
		xhr.open(method, url);
	} else {
		xhr = null;
	}

	return xhr;
}