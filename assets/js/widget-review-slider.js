let defaults = {
	'speed': 3000,
	'pausable': true,
};

window.addEventListener('DOMContentLoaded', function() {
	let initSliders = (function(){
			let $objects = [],
			sliders   = document.querySelectorAll('.rbd-review-slider');
		
		for( i = 0, n = sliders.length; i < n; ++i ){
			let setHeight = 0,
				slider = sliders[i],
				slides = slider.querySelectorAll('.rbd-review');

			$objects[i] = {};
			$objects[i].slider = slider;
			$objects[i].slider.slides = slides;
			$objects[i].slider.options = {};
			$objects[i].slider.options.speed = slider.getAttribute('data-speed') != null ? slider.getAttribute('data-speed') : defaults.speed;
			$objects[i].slider.options.pausable = slider.getAttribute('data-pausable') != null ? slider.getAttribute('data-pausable') : defaults.pausable;
			
			for( s = 0, x = slides.length; s < x; ++s ){
				setHeight = slides[s].offsetHeight > setHeight ? slides[s].offsetHeight : setHeight;
				$objects[i].slider.options.height = setHeight + 'px';
			}
		}
		
		return $objects;
	})();

	initSliders.forEach(function(obj){
		obj.slider.style.height = obj.slider.options.height;
	
		switch(obj.slider.options.pausable){
			case true:
			case 'true':
				(function(){
					obj.slider.onmouseleave = function(){ this.classList.remove('rbd-paused'); }
					obj.slider.onmouseenter = function(){ this.classList.add('rbd-paused'); }
				})();
		}
		
		setInterval(function(){
			slide(obj);
		}, obj.slider.options.speed);
	});
	
	function slide(obj){
		if( obj.slider.classList.contains('rbd-paused') ) return;
		
		let prev, curr, next, soon, activeSlide;
		let slides = obj.slider.slides;
		
		slides.forEach(function(slide){
			if( slide.classList.contains('rbd-curr') ) activeSlide = slide;
		});
		
		curr = activeSlide;
		prev = activeSlide.previousElementSibling;
		next = activeSlide.nextElementSibling;

		if( prev == null ){ // No Previous Slide, Means "last" slide is Previous.
			prev = slides[slides.length-1];
		}
		
		if( next != null ){ // There's a Next, but see if there's a soon.
			soon = next.nextElementSibling == null ? slides[0] : next.nextElementSibling;
		} else {
			// No Next Slide, Means we're on the last, "First" slide is Next.
			next = slides[0];
			soon = slides[1];
		}

		if( prev != null ) prev.classList.remove('rbd-prev');
		if( curr != null ) curr.classList.remove('rbd-curr'); curr.classList.add('rbd-prev');
		if( next != null ) next.classList.remove('rbd-next'); next.classList.add('rbd-curr');
		if( soon != null ) soon.classList.remove('rbd-prev', 'rbd-curr', 'rbd-next'); soon.classList.add('rbd-next');
	}
}, true);