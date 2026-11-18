/**
 * On this page: open beside the story on wide screens (and not collapsible
 * there), a closed disclosure on small ones, and the link of the heading
 * being read is marked.
 */
document.querySelectorAll( '[data-sk-toc]' ).forEach( ( toc ) => {
	const wide = window.matchMedia( '(min-width: 1000px)' );
	const summary = toc.querySelector( 'summary' );

	const place = () => {
		toc.toggleAttribute( 'open', wide.matches );
	};

	place();
	toc.setAttribute( 'data-sk-ready', '' );
	wide.addEventListener( 'change', place );

	// The list is always open on wide screens: Enter or Space must not close it.
	summary.addEventListener( 'click', ( event ) => {
		if ( wide.matches ) {
			event.preventDefault();
		}
	} );

	const links = [ ...toc.querySelectorAll( 'a[href^="#"]' ) ];
	const targets = links
		.map( ( link ) =>
			document.getElementById(
				decodeURIComponent( link.hash.slice( 1 ) )
			)
		)
		.filter( Boolean );

	if ( ! targets.length || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	const mark = ( id ) => {
		links.forEach( ( link ) => {
			if ( decodeURIComponent( link.hash.slice( 1 ) ) === id ) {
				link.setAttribute( 'aria-current', 'location' );
			} else {
				link.removeAttribute( 'aria-current' );
			}
		} );
	};

	// The active heading is the last one that has crossed the upper third.
	const observer = new window.IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					mark( entry.target.id );
				}
			} );
		},
		{ rootMargin: '0px 0px -66% 0px' }
	);

	targets.forEach( ( target ) => observer.observe( target ) );
} );
