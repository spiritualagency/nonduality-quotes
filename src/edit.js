
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, BlockControls, AlignmentToolbar } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { useState, useEffect, useCallback } from '@wordpress/element';
import './editor.scss';

const QUOTES = [
	{ text: "You are not a drop in the ocean. You are the entire ocean in a drop.", author: "Rumi", category: "Sufism" },
	{ text: "The eye through which I see God is the same eye through which God sees me.", author: "Meister Eckhart", category: "Christian Mysticism" },
	{ text: "In the beginning was the Word, and the Word was with God, and the Word was God.", author: "Gospel of John", category: "Christian Mysticism" },
	{ text: "Be still, and know that I am God.", author: "Psalm 46:10", category: "Christian Mysticism" },
	{ text: "The Tao that can be told is not the eternal Tao.", author: "Lao Tzu", category: "Taoism" },
	{ text: "When I let go of what I am, I become what I might be.", author: "Lao Tzu", category: "Taoism" },
	{ text: "Nature does not hurry, yet everything is accomplished.", author: "Lao Tzu", category: "Taoism" },
	{ text: "The world is illusion. Brahman alone is real. The world is Brahman.", author: "Adi Shankara", category: "Advaita Vedanta" },
	{ text: "That which permeates all, which nothing transcends and which, like the universal space around us, fills everything completely from within and without, that Supreme non-dual Brahman — that thou art.", author: "Shankaracharya", category: "Advaita Vedanta" },
	{ text: "Brahman is the only truth, the world is unreal, and there is ultimately no difference between Brahman and individual self.", author: "Adi Shankara", category: "Advaita Vedanta" },
	{ text: "You are awareness. Awareness is another name for you.", author: "Ramana Maharshi", category: "Advaita Vedanta" },
	{ text: "Your own Self-realization is the greatest service you can render the world.", author: "Ramana Maharshi", category: "Advaita Vedanta" },
	{ text: "The mind turned inwards is the Self; turned outwards, it becomes the ego and all the world.", author: "Ramana Maharshi", category: "Advaita Vedanta" },
	{ text: "In the sky, there is no distinction of east and west; people create distinctions out of their own minds and then believe them to be true.", author: "Buddha", category: "Buddhism" },
	{ text: "Form is emptiness, emptiness is form.", author: "Heart Sutra", category: "Buddhism" },
	{ text: "You are the sky. Everything else — it's just the weather.", author: "Pema Chödrön", category: "Buddhism" },
	{ text: "The finger pointing at the moon is not the moon.", author: "Zen Proverb", category: "Buddhism" },
	{ text: "Before enlightenment, chop wood, carry water. After enlightenment, chop wood, carry water.", author: "Zen Proverb", category: "Buddhism" },
	{ text: "Silence is the language of God, all else is poor translation.", author: "Rumi", category: "Sufism" },
	{ text: "What you seek is seeking you.", author: "Rumi", category: "Sufism" },
	{ text: "I have lived on the lip of insanity, wanting to know reasons, knocking on a door. It opens. I've been knocking from the inside.", author: "Rumi", category: "Sufism" },
	{ text: "The lamps are different, but the Light is the same.", author: "Rumi", category: "Sufism" },
	{ text: "Out beyond ideas of wrongdoing and rightdoing, there is a field. I'll meet you there.", author: "Rumi", category: "Sufism" },
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
	{ text: "In the beginner's mind there are many possibilities, but in the expert's mind there are few.", author: "Shunryu Suzuki", category: "Zen" },
	{ text: "The instant you speak about a thing, you miss the mark.", author: "Zen Proverb", category: "Zen" },
	{ text: "All the Buddhas and all sentient beings are nothing but the One Mind, beside which nothing exists.", author: "Huang Po", category: "Zen" },
	{ text: "Sitting quietly, doing nothing, spring comes, and the grass grows by itself.", author: "Matsuo Basho", category: "Zen" },
	{ text: "When you try to stay on the surface of the water, you sink; but when you try to sink, you float.", author: "Alan Watts", category: "Zen" },
	{ text: "No snowflake ever falls in the wrong place.", author: "Zen Proverb", category: "Zen" },
];

const CATEGORIES = [
	{ label: __( 'All Categories', 'telex-nonduality-quotes' ), value: 'all' },
	{ label: __( 'Advaita Vedanta', 'telex-nonduality-quotes' ), value: 'Advaita Vedanta' },
	{ label: __( 'Buddhism', 'telex-nonduality-quotes' ), value: 'Buddhism' },
	{ label: __( 'Taoism', 'telex-nonduality-quotes' ), value: 'Taoism' },
	{ label: __( 'Christian Mysticism', 'telex-nonduality-quotes' ), value: 'Christian Mysticism' },
	{ label: __( 'Sufism', 'telex-nonduality-quotes' ), value: 'Sufism' },
	{ label: __( 'Zen', 'telex-nonduality-quotes' ), value: 'Zen' },
	{ label: __( 'Inspirational', 'telex-nonduality-quotes' ), value: 'Inspirational' },
];

const FONT_SIZES = [
	{ label: __( 'Small', 'telex-nonduality-quotes' ), value: 'small' },
	{ label: __( 'Medium', 'telex-nonduality-quotes' ), value: 'medium' },
	{ label: __( 'Large', 'telex-nonduality-quotes' ), value: 'large' },
];

const COLOR_SCHEMES = [
	{ label: __( 'Light', 'telex-nonduality-quotes' ), value: 'light' },
	{ label: __( 'Dark', 'telex-nonduality-quotes' ), value: 'dark' },
	{ label: __( 'Warm', 'telex-nonduality-quotes' ), value: 'warm' },
	{ label: __( 'Serene', 'telex-nonduality-quotes' ), value: 'serene' },
];

function getFilteredQuotes( category ) {
	if ( category === 'all' ) {
		return QUOTES;
	}
	return QUOTES.filter( ( q ) => q.category === category );
}

function getRandomQuote( quotes, currentIndex ) {
	if ( quotes.length <= 1 ) {
		return 0;
	}
	let newIndex;
	do {
		newIndex = Math.floor( Math.random() * quotes.length );
	} while ( newIndex === currentIndex );
	return newIndex;
}

export default function Edit( { attributes, setAttributes } ) {
	const { category, fontSize, textAlign, showCategory, showButton, colorScheme, showBackground } = attributes;
	const [ currentIndex, setCurrentIndex ] = useState( 0 );
	const [ fading, setFading ] = useState( false );

	const filteredQuotes = getFilteredQuotes( category );
	const quote = filteredQuotes[ currentIndex % filteredQuotes.length ] || filteredQuotes[ 0 ];

	useEffect( () => {
		setCurrentIndex( 0 );
	}, [ category ] );

	const handleNewQuote = useCallback( () => {
		setFading( true );
		setTimeout( () => {
			setCurrentIndex( ( prev ) => getRandomQuote( filteredQuotes, prev ) );
			setFading( false );
		}, 300 );
	}, [ filteredQuotes ] );

	const blockProps = useBlockProps( {
		className: `tnq-scheme-${ colorScheme } tnq-size-${ fontSize } tnq-align-${ textAlign }`,
	} );

	return (
		<>
			<BlockControls>
				<AlignmentToolbar
					value={ textAlign }
					onChange={ ( newAlign ) => setAttributes( { textAlign: newAlign || 'center' } ) }
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Quote Settings', 'telex-nonduality-quotes' ) }>
					<SelectControl
						label={ __( 'Category', 'telex-nonduality-quotes' ) }
						value={ category }
						options={ CATEGORIES }
						onChange={ ( value ) => setAttributes( { category: value } ) }
					/>
					<ToggleControl
						label={ __( 'Show Category Label', 'telex-nonduality-quotes' ) }
						checked={ showCategory }
						onChange={ ( value ) => setAttributes( { showCategory: value } ) }
					/>
					<ToggleControl
						label={ __( 'Show New Quote Button', 'telex-nonduality-quotes' ) }
						checked={ showButton }
						onChange={ ( value ) => setAttributes( { showButton: value } ) }
					/>
					<ToggleControl
						label={ __( 'Show Nature Background', 'telex-nonduality-quotes' ) }
						help={ __( 'Requires a Pixabay API key in Settings > Nonduality Quotes.', 'telex-nonduality-quotes' ) }
						checked={ showBackground }
						onChange={ ( value ) => setAttributes( { showBackground: value } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Appearance', 'telex-nonduality-quotes' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Font Size', 'telex-nonduality-quotes' ) }
						value={ fontSize }
						options={ FONT_SIZES }
						onChange={ ( value ) => setAttributes( { fontSize: value } ) }
					/>
					<SelectControl
						label={ __( 'Color Scheme', 'telex-nonduality-quotes' ) }
						value={ colorScheme }
						options={ COLOR_SCHEMES }
						onChange={ ( value ) => setAttributes( { colorScheme: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className={ `tnq-quote-card${ fading ? ' tnq-fading' : '' }` }>
					<div className="tnq-quote-icon" aria-hidden="true">&ldquo;</div>
					<blockquote className="tnq-quote-text">
						{ quote ? quote.text : __( 'No quotes found for this category.', 'telex-nonduality-quotes' ) }
					</blockquote>
					{ quote && (
						<div className="tnq-quote-meta">
							<span className="tnq-quote-author">&mdash; { quote.author }</span>
							{ showCategory && (
								<span className="tnq-quote-category">{ quote.category }</span>
							) }
						</div>
					) }
					<div className="tnq-quote-actions">
						{ showButton && (
							<button
								type="button"
								className="tnq-new-quote-btn"
								onClick={ handleNewQuote }
							>
								{ __( 'New Quote', 'telex-nonduality-quotes' ) }
							</button>
						) }
						<button
							type="button"
							className="tnq-new-quote-btn tnq-share-btn"
							onClick={ () => {} }
							title={ __( 'Share', 'telex-nonduality-quotes' ) }
						>
							{ __( 'Share', 'telex-nonduality-quotes' ) }
						</button>
					</div>
				</div>
			</div>
		</>
	);
}
