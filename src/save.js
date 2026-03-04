
import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { category, fontSize, textAlign, showCategory, showButton, colorScheme } = attributes;

	const blockProps = useBlockProps.save( {
		className: `tnq-scheme-${ colorScheme } tnq-size-${ fontSize } tnq-align-${ textAlign }`,
		'data-category': category,
		'data-show-category': showCategory ? 'true' : 'false',
		'data-show-button': showButton ? 'true' : 'false',
	} );

	return (
		<div { ...blockProps }>
			<div className="tnq-quote-card">
				<div className="tnq-quote-icon" aria-hidden="true">&ldquo;</div>
				<blockquote className="tnq-quote-text"></blockquote>
				<div className="tnq-quote-meta">
					<span className="tnq-quote-author"></span>
					<span className="tnq-quote-category"></span>
				</div>
				<button type="button" className="tnq-new-quote-btn">New Quote</button>
			</div>
		</div>
	);
}
