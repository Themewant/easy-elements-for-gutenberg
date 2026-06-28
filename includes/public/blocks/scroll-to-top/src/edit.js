import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-stt-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Scroll Button', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={attributes.scrollIcon || ''} onChange={(v) => setAttributes({ scrollIcon: v })} />
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Scroll Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ColorPopover label={__('Color', 'easy-elements-for-gutenberg')} color={attributes.color} onChange={(v) => setAttributes({ color: v })} />
					<ColorPopover label={__('Background Color', 'easy-elements-for-gutenberg')} color={attributes.bgColor} onChange={(v) => setAttributes({ bgColor: v })} />
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/scroll-to-top" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
