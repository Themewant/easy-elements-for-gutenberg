import { registerBlockType } from '@wordpress/blocks';

import './style.scss';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

const eelfgIcon = (
	<svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
		<rect x="8" y="30" width="38" height="38" rx="10" fill="#a216ff" />
		<rect x="54" y="34" width="38" height="7" rx="3.5" fill="#a216ff" opacity="0.6" />
		<rect x="54" y="48" width="30" height="6" rx="3" fill="#a216ff" opacity="0.4" />
		<rect x="54" y="60" width="22" height="6" rx="3" fill="#a216ff" opacity="0.4" />
	</svg>
);

registerBlockType(metadata.name, {
	icon: eelfgIcon,
	edit: Edit,
	save,
});
