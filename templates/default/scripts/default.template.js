(function(d, w) {

	function offsetTop(e) {
		if (e.id = "top") return 0;
		if (e.style.position == 'fixed') return e.offsetTop;
		var result = e.offsetTop;
		while (e = e.offsetParent) result += e.offsetTop;
		return result;
	} // offsetTop

	var scroll = {
		accelleration : 1.1,
		startVelocity : 5,
		maxVelocity : 200,
		frame : d.getElementById('fauxBody'),
		active : false
	};

	for (var a of d.getElementsByTagName('a')) {

		if (
			a.classList.contains('noSmoothScroll') ||
			a.classList.contains('mainMenuClose') ||
			a.classList.contains('modalClose')
		) continue;

		var href = a.getAttribute('href');
		if (
			!href ||
			(href === "#") ||
			(href.charAt(0) !== "#") ||
			href === '#mainMenu'
		) continue;

		var target = d.getElementById(href.substr(1));
		if (!target || target.classList.contains('modal')) continue;

		a.addEventListener('click', scrollTo, false);

	} // for a of anchors

	function scrollTo(event) {
	
		var
			href = event.currentTarget.getAttribute('href').substr(1),
			target = d.getElementById(href);
			
		if (!target) return;
		
		scroll.target = target;
		scroll.hash = href;
		
		if (!scroll.active) {
			scroll.active = true;
			scroll.velocity = scroll.startVelocity;
			scrollAnimation();
		}
		
		event.preventDefault();
		event.currentTarget.blur();
		
	} // scrollTo

	function scrollAnimation() {
		
		var
			destination = Math.min(
				offsetTop(scroll.target),
				scroll.frame.scrollHeight - scroll.frame.clientHeight
			),
			direction = scroll.frame.scrollTop < destination ? 1 : -1,
			position = scroll.frame.scrollTop + scroll.velocity * direction;
			
		if (
			((direction < 0) && (position < destination)) ||
			((direction > 0) && (position > destination))
		) {
			scroll.frame.scroll(scroll.frame.scrollLeft, destination);
			if (scroll.hash !== "top") window.location.hash = scroll.hash;
			scroll.active = false;
			return;
		}
		scroll.frame.scroll(scroll.frame.scrollLeft, position);
			
		if (scroll.velocity < scroll.maxVelocity) scroll.velocity *= scroll.accelleration;
		requestAnimationFrame(scrollAnimation);
			
	} // scrollAnimation
	
	for (var input of d.querySelectorAll("input.remember")) {
		input.addEventListener("change", inputRemember, false);
		if (input.id) input.checked = localStorage.getItem(
			"remember_" + input.id
		) ? "selected" : "";
	}
	
	function inputRemember(e) {	
		localStorage.setItem("remember_" + e.currentTarget.id, (
			e.currentTarget.checked ? "selected" : ""
		));
	} // inputRemember
	
  d.addEventListener('keydown', function(e) {
    if ((e.key !== "Escape") || (location.hash.length  < 2)) return;
		var target = d.getElementById(location.hash.substr(1));
		if (!target || !target.classList.contains('modal')) return;
		location.hash = "#";
		e.preventDefault();
  }, false);

})(document, window);