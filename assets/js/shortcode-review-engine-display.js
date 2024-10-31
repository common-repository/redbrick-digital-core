let reviewEngineURL = document.querySelector('.rbd-review-engine-display').getAttribute( 'data-review-engine-url' );

// Ajax Request to Review Engine
function RBD_newReview( targetGrid, review, characters, hideGravatars ){
	let newReview     = document.createElement('div');
	let reviewerName  = ( review.review_meta.reviewer.anonymous != null ) ? 'Anonymous' : review.review_meta.reviewer.display_name;
	let gravatar      = ( hideGravatars === true || review.review_meta.reviewer.gravatar == null || review.review_meta.reviewer.gravatar.length < 1 ) ? '' : '<img class="rbd-gravatar" src="'+ review.review_meta.reviewer.gravatar +'" />';

	review.content = review.content.replace(/(<([^>]+)>)/ig,"");

	let contentLimit = review.content.substr(0, RBD_strpos(review.content, ' ', characters ) ); // Get Review Content before char limit (break at word)
	let contentMore  = review.content.replace(contentLimit, ''); // Replace content limit with blank, we'll add to data-more

	let reviewContent = ( review.content.length > characters ) ? '<span class="rbd-content-limit"> '+ contentLimit + '…</span> <a href="#" data-more="'+ contentMore +'">Read More</a>' : review.content;

	if( contentLimit.length < 2 && contentMore.length > 2 ){
		//Review got switched with strpos
		reviewContent = review.content;
	}

	newReview.setAttribute('data-permalink', review.url );
	newReview.setAttribute('data-meta', 'Written by '+ reviewerName + ' on '+ review.review_meta.review_date.short_date );
	newReview.classList.add('rbd-review');

	// Freaking IE won't let me use Template Literals...
	newReview.innerHTML = '<h3 class="rbd-heading">'+ review.title +'</h3>\
						  <i class="rbd-score renderSVG" data-icon="star" data-repeat="5" data-score="'+ review.rating +'"></i>\
						  <p class="rbd-content">'+ gravatar + reviewContent +'</p>';

	targetGrid.appendChild(newReview);
}

// Read More Function
rbdPopupReview    = rbdPopupContainer.querySelector('.rbd-popup-content > div');
rbdPopupContent   = rbdPopupReview.parentNode;

function RBD_initReadMore(){
	let readMore = document.querySelectorAll('.rbd-review a');
	for( i = 0, n = readMore.length; i < n; ++i ){
		readMore[i].onclick = function(){
			let review   = this.parentNode.parentNode,
				more     = '<span class="rbd-temp-highlight">' + this.getAttribute('data-more') + '</span>';
				//more   = '<strong class="rbd-temp-highlight">' + this.getAttribute('data-more') + '</strong>';

			let html         = review.cloneNode(true),
				link         = html.querySelector('.rbd-content a');
				meta         = '<span class="rbd-review-meta">' + html.getAttribute('data-meta') + '</span>';
				content      = html.querySelector('.rbd-content'),
				contentLimit = html.querySelector('.rbd-content-limit');

			let viewportOffset = review.getBoundingClientRect(),
				popPosTop      = viewportOffset.top + window.scrollY,
				popPosLeft     = viewportOffset.left + window.scrollX,
				popWidth       = viewportOffset.width * .9090909091,
				contWidth      = document.body.clientWidth;
				contHeight     = document.body.clientHeight,


			html.classList.add('rbd-dynamic');
			html.querySelector('.rbd-content').removeChild(link);

			contentLimit.textContent = contentLimit.textContent.replace('…', '');
			content.insertAdjacentHTML('beforeend', more);
			content.insertAdjacentHTML('afterend', meta);
			content.insertAdjacentHTML('afterend', '<div style="text-align:center; margin: 10px 0;"><a target="_blank" class="rbd-button rbd-small" href="'+ review.getAttribute('data-permalink') +'">View Review Source</a></div>');
			
			rbdPopupContainer.classList.add('rbd-shown');
			rbdPopupContainer.style.width  = contWidth + 'px';
			rbdPopupContainer.style.height = contHeight + 'px';
			
			rbdPopupContent.classList.add('rbd-review-engine-display');
			rbdPopupContent.style.top   = popPosTop + 'px';
			rbdPopupContent.style.left  = popPosLeft + 'px';
			rbdPopupContent.style.width = popWidth + 'px';
			
			rbdPopupContent.insertBefore(html, rbdPopupReview);
			return false;
		}
	}
}
RBD_initReadMore();

// Show Reputation Breakdown
let showBreakdown = document.querySelectorAll('.rbd-review-engine-display .rbd-view-breakdown');
for( i = 0, n = showBreakdown.length; i < n; ++i ){
	showBreakdown[i].onclick = function(){
		let gridHeader = this.parentNode;
		let breakdownContainer = gridHeader.parentNode.querySelector('.rbd-breakdown-container');

		if( this.textContent == 'View Rating Breakdown' ) this.textContent = 'Hide Rating Breakdown';
		else if( this.textContent == 'Hide Rating Breakdown' ) this.textContent = 'View Rating Breakdown';

		breakdownContainer.classList.toggle('rbd-scaleIn');
	}
}

// Load More Reviews via Ajax
let loadMore = document.querySelectorAll('.rbd-load-more');
for( i = 0, n = loadMore.length; i < n; ++i ){
	loadMore[i].onclick = function(){
		let rbdParent  = this.parentNode;
		let targetGrid = rbdParent.querySelector('.rbd-review-grid');

		// Prevent Dom resizing by setting this to the exact height/width while it's animating
		// Both are set to height to make it square (circular with radius)
		let height = this.offsetHeight + 'px';
		this.style.width  = height;
		this.style.height = height;
		this.classList.add('rbd-currently-loading');
		
		// Define some API related information (perpage, offset, character trim, etc.)
		let perpage       = this.getAttribute('data-perpage'),
			offset        = this.getAttribute('data-offset'),
			characters    = this.getAttribute('data-characters');
			hideGravatars = this.getAttribute('data-hide-gravatars');

		let data = [].filter.call(this.attributes, function(at) { return /^data-/.test(at.name); });

		let xhr = RBD_createCORSRequest('GET', reviewEngineURL + 'reviews-api-v2/?user=test&key=cdff.1070bdaacf&threshold=3&query_v2=true&reviews_per_page='+perpage+'&offset='+offset);

		if( !xhr ){
			throw new Error('CORS not supported');
		} else {
			xhr.onload = (function(response) {
				if (xhr.status == 200) {
					let response = xhr.response,
						json     = JSON.parse(response);

					let reviews = json.reviews;
					for( i = 0, n = reviews.length; i < n; ++i ){
						RBD_newReview( targetGrid, reviews[i], characters, hideGravatars );
						++offset;
					}

					this.classList.remove('rbd-currently-loading');
					this.style.width  = null;
					this.style.height = null;
					this.setAttribute('data-offset', offset);
					if( offset >= parseInt( targetGrid.getAttribute('data-max' ) ) ){
						this.style.display = 'none';
					}

					RBD_renderSVG();
					RBD_initReadMore();
				} else {
					alert( 'Review Engine API Request Failed' );
					this.classList.remove('rbd-currently-loading');
				}
			}).bind(this);
			xhr.send();
		}
	}
}