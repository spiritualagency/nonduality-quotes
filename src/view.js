
var QUOTES = [
	{ text: "You are not a drop in the ocean. You are the entire ocean in a drop.", author: "Rumi", category: "Sufism" },
	{ text: "The eye through which I see God is the same eye through which God sees me.", author: "Meister Eckhart", category: "Christian Mysticism" },
	{ text: "In the beginning was the Word, and the Word was with God, and the Word was God.", author: "Gospel of John", category: "Christian Mysticism" },
	{ text: "Be still, and know that I am God.", author: "Psalm 46:10", category: "Christian Mysticism" },
	{ text: "The Tao that can be told is not the eternal Tao.", author: "Lao Tzu", category: "Taoism" },
	{ text: "When I let go of what I am, I become what I might be.", author: "Lao Tzu", category: "Taoism" },
	{ text: "Nature does not hurry, yet everything is accomplished.", author: "Lao Tzu", category: "Taoism" },
	{ text: "The world is illusion. Brahman alone is real. The world is Brahman.", author: "Adi Shankara", category: "Advaita Vedanta" },
	{ text: "That which permeates all, which nothing transcends and which, like the universal space around us, fills everything completely from within and without, that Supreme non-dual Brahman \u2014 that thou art.", author: "Shankaracharya", category: "Advaita Vedanta" },
	{ text: "Brahman is the only truth, the world is unreal, and there is ultimately no difference between Brahman and individual self.", author: "Adi Shankara", category: "Advaita Vedanta" },
	{ text: "You are awareness. Awareness is another name for you.", author: "Ramana Maharshi", category: "Advaita Vedanta" },
	{ text: "Your own Self-realization is the greatest service you can render the world.", author: "Ramana Maharshi", category: "Advaita Vedanta" },
	{ text: "The mind turned inwards is the Self; turned outwards, it becomes the ego and all the world.", author: "Ramana Maharshi", category: "Advaita Vedanta" },
	{ text: "In the sky, there is no distinction of east and west; people create distinctions out of their own minds and then believe them to be true.", author: "Buddha", category: "Buddhism" },
	{ text: "Form is emptiness, emptiness is form.", author: "Heart Sutra", category: "Buddhism" },
	{ text: "You are the sky. Everything else \u2014 it\u2019s just the weather.", author: "Pema Ch\u00f6dr\u00f6n", category: "Buddhism" },
	{ text: "The finger pointing at the moon is not the moon.", author: "Zen Proverb", category: "Buddhism" },
	{ text: "Before enlightenment, chop wood, carry water. After enlightenment, chop wood, carry water.", author: "Zen Proverb", category: "Buddhism" },
	{ text: "Silence is the language of God, all else is poor translation.", author: "Rumi", category: "Sufism" },
	{ text: "What you seek is seeking you.", author: "Rumi", category: "Sufism" },
	{ text: "I have lived on the lip of insanity, wanting to know reasons, knocking on a door. It opens. I\u2019ve been knocking from the inside.", author: "Rumi", category: "Sufism" },
	{ text: "The lamps are different, but the Light is the same.", author: "Rumi", category: "Sufism" },
	{ text: "Out beyond ideas of wrongdoing and rightdoing, there is a field. I\u2019ll meet you there.", author: "Rumi", category: "Sufism" },
	{ text: "We are not human beings having a spiritual experience. We are spiritual beings having a human experience.", author: "Pierre Teilhard de Chardin", category: "Inspirational" },
	{ text: "The privilege of a lifetime is to become who you truly are.", author: "Carl Jung", category: "Inspirational" },
	{ text: "Knowing others is intelligence; knowing yourself is true wisdom.", author: "Lao Tzu", category: "Taoism" },
	{ text: "The only way to make sense out of change is to plunge into it, move with it, and join the dance.", author: "Alan Watts", category: "Inspirational" },
	{ text: "You are an aperture through which the universe is looking at and exploring itself.", author: "Alan Watts", category: "Inspirational" },
	{ text: "Muddy water is best cleared by leaving it alone.", author: "Alan Watts", category: "Taoism" },
	{ text: "The soul that is attached to anything, however much good there may be in it, will not arrive at the liberty of the divine.", author: "St. John of the Cross", category: "Christian Mysticism" },
	{ text: "God is not found in the soul by adding anything, but by a process of subtraction.", author: "Meister Eckhart", category: "Christian Mysticism" },
	{ text: "There is no creation, no destruction, no bondage, no longing to be freed, no striving for liberation, and no one who has attained. Know that this is the ultimate truth.", author: "Ramana Maharshi", category: "Advaita Vedanta" },
	{ text: "When you make the two one, and when you make the inside like the outside and the outside like the inside, then you will enter the Kingdom.", author: "Gospel of Thomas", category: "Christian Mysticism" },
	{ text: "Do not be satisfied with the stories that come before you. Unfold your own myth.", author: "Rumi", category: "Sufism" },
	{ text: "The you that goes in one side of the meditation experience is not the same you that comes out the other side.", author: "Bhante Henepola Gunaratana", category: "Buddhism" },
	{ text: "To understand everything is to forgive everything.", author: "Buddha", category: "Buddhism" },
	{ text: "To study the Way is to study the self. To study the self is to forget the self. To forget the self is to be enlightened by all things.", author: "Dogen", category: "Zen" },
	{ text: "If you understand, things are just as they are; if you do not understand, things are just as they are.", author: "Zen Proverb", category: "Zen" },
	{ text: "In the beginner\u2019s mind there are many possibilities, but in the expert\u2019s mind there are few.", author: "Shunryu Suzuki", category: "Zen" },
	{ text: "The instant you speak about a thing, you miss the mark.", author: "Zen Proverb", category: "Zen" },
	{ text: "All the Buddhas and all sentient beings are nothing but the One Mind, beside which nothing exists.", author: "Huang Po", category: "Zen" },
	{ text: "Sitting quietly, doing nothing, spring comes, and the grass grows by itself.", author: "Matsuo Basho", category: "Zen" },
	{ text: "When you try to stay on the surface of the water, you sink; but when you try to sink, you float.", author: "Alan Watts", category: "Zen" },
	{ text: "No snowflake ever falls in the wrong place.", author: "Zen Proverb", category: "Zen" }
];

var tnqNatureTerms = [
	'mountain+landscape', 'forest+sunlight', 'ocean+waves', 'sunrise+nature',
	'river+forest', 'waterfall+nature', 'meadow+flowers', 'starry+sky',
	'misty+mountains', 'lake+reflection', 'autumn+leaves', 'zen+garden',
	'bamboo+forest', 'cherry+blossom', 'desert+sunset', 'northern+lights'
];

var tnqImageCache = {};

function tnqGetFilteredQuotes( category ) {
	if ( category === 'all' ) {
		return QUOTES;
	}
	return QUOTES.filter( function( q ) {
		return q.category === category;
	} );
}

function tnqGetRandomIndex( max, current ) {
	if ( max <= 1 ) {
		return 0;
	}
	var newIndex;
	do {
		newIndex = Math.floor( Math.random() * max );
	} while ( newIndex === current );
	return newIndex;
}

function tnqRenderQuote( block, quote, showCategory ) {
	var textEl = block.querySelector( '.tnq-quote-text' );
	var authorEl = block.querySelector( '.tnq-quote-author' );
	var categoryEl = block.querySelector( '.tnq-quote-category' );

	if ( textEl ) {
		textEl.textContent = quote.text;
	}
	if ( authorEl ) {
		authorEl.textContent = '\u2014 ' + quote.author;
	}
	if ( categoryEl ) {
		if ( showCategory ) {
			categoryEl.textContent = quote.category;
			categoryEl.style.display = '';
		} else {
			categoryEl.textContent = '';
			categoryEl.style.display = 'none';
		}
	}
}

function tnqFetchNatureImage( apiKey, callback ) {
	if ( ! apiKey ) {
		callback( null );
		return;
	}

	var term = tnqNatureTerms[ Math.floor( Math.random() * tnqNatureTerms.length ) ];

	if ( tnqImageCache[ term ] && tnqImageCache[ term ].length > 0 ) {
		var cached = tnqImageCache[ term ];
		callback( cached[ Math.floor( Math.random() * cached.length ) ] );
		return;
	}

	var page = Math.floor( Math.random() * 3 ) + 1;
	var url = 'https://pixabay.com/api/?key=' + encodeURIComponent( apiKey ) +
		'&q=' + encodeURIComponent( term ) +
		'&image_type=photo&orientation=horizontal&category=nature' +
		'&min_width=800&per_page=10&page=' + page +
		'&safesearch=true';

	var xhr = new XMLHttpRequest();
	xhr.open( 'GET', url, true );
	xhr.onreadystatechange = function() {
		if ( xhr.readyState === 4 ) {
			if ( xhr.status === 200 ) {
				try {
					var data = JSON.parse( xhr.responseText );
					if ( data.hits && data.hits.length > 0 ) {
						var urls = data.hits.map( function( hit ) {
							return hit.webformatURL;
						} );
						tnqImageCache[ term ] = urls;
						callback( urls[ Math.floor( Math.random() * urls.length ) ] );
					} else {
						callback( null );
					}
				} catch ( e ) {
					callback( null );
				}
			} else {
				callback( null );
			}
		}
	};
	xhr.send();
}

function tnqApplyBackground( card, imageUrl ) {
	var bgEl = card.querySelector( '.tnq-bg-image' );
	if ( ! bgEl ) {
		return;
	}
	if ( imageUrl ) {
		bgEl.style.backgroundImage = 'url(' + imageUrl + ')';
		card.classList.add( 'tnq-has-bg' );
	} else {
		bgEl.style.backgroundImage = '';
		card.classList.remove( 'tnq-has-bg' );
	}
}

function tnqShowToast( block, message ) {
	var toast = block.querySelector( '.tnq-toast' );
	if ( ! toast ) {
		return;
	}
	toast.textContent = message;
	toast.style.display = 'block';
	setTimeout( function() {
		toast.style.display = 'none';
	}, 2000 );
}

function tnqGetShareText( quote ) {
	return '"' + quote.text + '" \u2014 ' + quote.author;
}

function tnqShareTo( platform, quote, block ) {
	var text = tnqGetShareText( quote );
	var pageUrl = window.location.href;
	var encodedText = encodeURIComponent( text );
	var encodedUrl = encodeURIComponent( pageUrl );
	var shareUrl = '';

	switch ( platform ) {
		case 'facebook':
			shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl + '&quote=' + encodedText;
			window.open( shareUrl, '_blank', 'width=600,height=400' );
			break;

		case 'x':
			shareUrl = 'https://x.com/intent/tweet?text=' + encodedText + '&url=' + encodedUrl;
			window.open( shareUrl, '_blank', 'width=600,height=400' );
			break;

		case 'whatsapp':
			shareUrl = 'https://api.whatsapp.com/send?text=' + encodedText + '%0A%0A' + encodedUrl;
			window.open( shareUrl, '_blank' );
			break;

		case 'linkedin':
			shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl;
			window.open( shareUrl, '_blank', 'width=600,height=400' );
			break;

		case 'instagram':
			if ( navigator.clipboard ) {
				navigator.clipboard.writeText( text ).then( function() {
					tnqShowToast( block, 'Quote copied! Paste it in your Instagram post.' );
				} );
			} else {
				tnqCopyFallback( text );
				tnqShowToast( block, 'Quote copied! Paste it in your Instagram post.' );
			}
			break;

		case 'link':
			var linkText = text + '\n\n' + pageUrl;
			if ( navigator.clipboard ) {
				navigator.clipboard.writeText( linkText ).then( function() {
					tnqShowToast( block, 'Copied to clipboard!' );
				} );
			} else {
				tnqCopyFallback( linkText );
				tnqShowToast( block, 'Copied to clipboard!' );
			}
			break;
	}
}

function tnqCopyFallback( text ) {
	var textarea = document.createElement( 'textarea' );
	textarea.value = text;
	textarea.style.position = 'fixed';
	textarea.style.left = '-9999px';
	document.body.appendChild( textarea );
	textarea.select();
	document.execCommand( 'copy' );
	document.body.removeChild( textarea );
}

// Merge custom quotes from the admin Manage Quotes page.
if ( typeof tnqCustomQuotes !== 'undefined' && Array.isArray( tnqCustomQuotes ) ) {
	for ( var ci = 0; ci < tnqCustomQuotes.length; ci++ ) {
		QUOTES.push( tnqCustomQuotes[ ci ] );
	}
}

document.addEventListener( 'DOMContentLoaded', function() {
	var blocks = document.querySelectorAll( '.wp-block-spiritual-agency-nonduality-quotes' );

	blocks.forEach( function( block ) {
		var category = block.getAttribute( 'data-category' ) || 'all';
		var showCategory = block.getAttribute( 'data-show-category' ) !== 'false';
		var showButton = block.getAttribute( 'data-show-button' ) !== 'false';
		var showBackground = block.getAttribute( 'data-show-background' ) !== 'false';
		var pixabayKey = block.getAttribute( 'data-pixabay-key' ) || '';
		var filteredQuotes = tnqGetFilteredQuotes( category );
		var currentIndex = Math.floor( Math.random() * filteredQuotes.length );
		var card = block.querySelector( '.tnq-quote-card' );
		var btn = block.querySelector( '.tnq-new-quote-btn:not(.tnq-share-btn)' );
		var shareBtn = block.querySelector( '.tnq-share-btn' );
		var shareDropdown = block.querySelector( '.tnq-share-dropdown' );
		var shareOptions = block.querySelectorAll( '.tnq-share-option' );

		if ( filteredQuotes.length > 0 ) {
			tnqRenderQuote( block, filteredQuotes[ currentIndex ], showCategory );
		}

		if ( showBackground && pixabayKey && card ) {
			tnqFetchNatureImage( pixabayKey, function( url ) {
				tnqApplyBackground( card, url );
			} );
		}

		if ( btn ) {
			if ( ! showButton ) {
				btn.style.display = 'none';
			} else {
				btn.addEventListener( 'click', function() {
					if ( card ) {
						card.classList.add( 'tnq-fading' );
					}
					setTimeout( function() {
						currentIndex = tnqGetRandomIndex( filteredQuotes.length, currentIndex );
						tnqRenderQuote( block, filteredQuotes[ currentIndex ], showCategory );
						if ( showBackground && pixabayKey && card ) {
							tnqFetchNatureImage( pixabayKey, function( url ) {
								tnqApplyBackground( card, url );
							} );
						}
						if ( card ) {
							card.classList.remove( 'tnq-fading' );
						}
					}, 300 );
				} );
			}
		}

		if ( shareBtn && shareDropdown ) {
			shareBtn.addEventListener( 'click', function( e ) {
				e.stopPropagation();
				var isVisible = shareDropdown.style.display !== 'none';
				shareDropdown.style.display = isVisible ? 'none' : 'block';
			} );
		}

		shareOptions.forEach( function( option ) {
			option.addEventListener( 'click', function( e ) {
				e.stopPropagation();
				var platform = option.getAttribute( 'data-platform' );
				var quote = filteredQuotes[ currentIndex % filteredQuotes.length ];
				if ( quote ) {
					tnqShareTo( platform, quote, block );
				}
				if ( shareDropdown ) {
					shareDropdown.style.display = 'none';
				}
			} );
		} );

		document.addEventListener( 'click', function() {
			if ( shareDropdown ) {
				shareDropdown.style.display = 'none';
			}
		} );
	} );
} );
