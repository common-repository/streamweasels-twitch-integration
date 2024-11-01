import {
	PanelBody,
	PanelRow,
	TextControl,
	QueryControls,
	ToggleControl,
	SelectControl,
	RangeControl,
	BaseControl,
	FormToggle
} from '@wordpress/components';

import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {

	const { 
		channel,
		autoplay,
		muted,
		embedChat,
		theme,
		width,
		height
	} = attributes;

	let iframeWidth, iframeHeight = '100%';

	console.log(width)

	if (!width.endsWith('px') && !width.endsWith('%')) {
		iframeWidth = width + 'px';
	  } else {
		iframeWidth = width ? width : iframeWidth;
	  }
	if (!height.endsWith('px') && !height.endsWith('%')) {
		iframeHeight = height + 'px';
	} else {
		iframeHeight = height ? height : iframeHeight;
	  }  


	return (
		<>
		<InspectorControls>		
			<PanelBody title={ __( 'Twitch Embed Settings', 'streamweasels' ) }>
				<TextControl
					type="text"
					name="channel"
					label={ __( 'Twitch Channel', 'streamweasels' ) }
					placeholder={ __( 'lirik', 'streamweasels' ) }
					value={ channel }
					onChange={ ( content ) =>
						setAttributes( { channel: content } )
					}
				/>	
				<ToggleControl
					label={ __(
						'Autoplay',
						'streamweasels'
					) }
					help={ __(
						'Start your stream automatically when the page loads.',
						'streamweasels'
					) }				
					checked={autoplay}
					onChange={ () =>
						setAttributes( { autoplay: ! autoplay } )
					}
				/>
				<ToggleControl
					label={ __(
						'Start Muted',
						'streamweasels'
					) }
					help={ __(
						'Start your Twitch stream muted.',
						'streamweasels'
					) }				
					checked={muted}
					onChange={ () =>
						setAttributes( { muted: ! muted } )
					}
				/>
				<ToggleControl
					label={ __(
						'Embed Chat',
						'streamweasels'
					) }
					help={ __(
						'Embed Twitch chat alongside your stream.',
						'streamweasels'
					) }				
					checked={embedChat}
					onChange={ () =>
						setAttributes( { embedChat: ! embedChat } )
					}
				/>	
				<SelectControl
					label={ __(
						'Theme',
						'streamweasels'
					) }
					help={ __(
						'Choose the desired colour theme for your Twitch embed.',
						'streamweasels'
					) }						
					value={ theme }
					onChange={ ( theme ) =>
						setAttributes( { theme: theme } )
					}
					options={ [{label: 'Dark', value: 'dark'},{label: 'Light', value: 'light'}] }
				/>
				<TextControl
					type="text"
					name="width"
					label={ __( 'Embed Width', 'streamweasels' ) }
					placeholder={ __( '100%', 'streamweasels' ) }
					help={ __(
						'Leave this blank to default to 100% width.',
						'streamweasels'
					) }					
					value={ width }
					onChange={ ( content ) =>
						setAttributes({ width: content !== '' ? content : '100%' })
					}
				/>
				<TextControl
					type="text"
					name="height"
					label={ __( 'Embed Height', 'streamweasels' ) }
					placeholder={ __( '100%', 'streamweasels' ) }
					help={ __(
						'Leave this blank to default to 100% height.',
						'streamweasels'
					) }						
					value={ height }
					onChange={ ( content ) =>
						setAttributes({ height: content !== '' ? content : '100%' })
					}
				/>															
			</PanelBody>
			<PanelBody title={ __( 'Twitch Advanced Settings', 'streamweasels' ) }>
				<PanelRow>
					<div>
						<p>Looking to customise your Twitch Integration even further? Check out the <a href="admin.php?page=streamweasels" target="_blank">Twitch Integration Settings</a> page for more options.</p>
					</div>
				</PanelRow>
			</PanelBody>						
		</InspectorControls>
					
        <div { ...useBlockProps() }>	
			<div className="cp-swti__embed" data-colour={theme} style={{width: iframeWidth, height: iframeHeight}}>
				<div>
					<span className="cp-swti__embed--play"></span>
					<span className="cp-swti__embed--channel">{channel}</span>
				</div>
			</div>
        </div>
		</>
	);
}
