import { registerBlockType } from '@wordpress/blocks';

import './style.scss';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

const eelfgIcon = (
	<svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
		<rect x="8" y="20" width="38" height="60" rx="8" fill="#a216ff" opacity="0.85" />
		<rect x="54" y="20" width="38" height="60" rx="8" fill="#a216ff" opacity="0.45" />
		<circle cx="27" cy="38" r="8" fill="#fff" />
	</svg>
);

registerBlockType(metadata.name, {
	icon: eelfgIcon,
	edit: Edit,
	save,
});
