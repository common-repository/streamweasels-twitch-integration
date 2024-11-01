import {
	PanelBody,
	PanelRow,
	TextControl,
	QueryControls,
	ToggleControl,
	SelectControl,
	RangeControl
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
		layout,
		channels,
		team,
		game,
		language,
		limit
	} = attributes;

	const [data, setData] = wp.element.useState([]);
    const [currentSlide, setCurrentSlide] = useState(0);

	// Set channelsArray to channels first
	let channelsArray = channels ? channels.split(',') : [];
	// If team is set, add it to the array
	channelsArray = team ? (() => {
	  const newArray = [];
	  for (let i = 0; i < limit; i++) {
		newArray.push(team);
	  }
	  return newArray;
	})() : channelsArray;
	// If game is set, add it to the array
	channelsArray = game ? (() => {
		const newArray = [];
		for (let i = 0; i < limit; i++) {
		  newArray.push(game);
		}
		return newArray;
	  })() : channelsArray;	

	if (layout === 'vods') {
		var X = limit;
		if (channelsArray.length > 1) {
			channelsArray = [channelsArray[0]];
		}
		if (channelsArray.length > 0) {
			var duplicatedArray = Array(X).fill(channelsArray[0]);
			channelsArray = channelsArray.concat(duplicatedArray);
		  }		
	}

    const handlePrevious = () => {
        setCurrentSlide((prevSlide) => (prevSlide - 1 + channelsArray.length) % channelsArray.length);
    };

    const handleNext = () => {
        setCurrentSlide((prevSlide) => (prevSlide + 1) % channelsArray.length);
    };

	wp.apiFetch({ path: '/streamweasels/v1/data' }).then(response => {
		setData(response);
	});

	let dateTimestamp1, dateTimestamp2, connectionExpiresMeta, licenseStatusColour, licenseStatusLabel, spanStyle, layoutArray; // Declare the variables outside the if statement

	if (data.accessToken) {
		licenseStatusColour = 'green';
		licenseStatusLabel = 'Twitch API Connected!';
	} else {
		licenseStatusColour = 'gray';
		licenseStatusLabel = 'Not Connected';											
	}	

	if (data.connectionExpires) {
	  connectionExpiresMeta = '(expires on ' + data.connectionExpires + ')';
	  dateTimestamp1 = new Date(data.connectionExpires).getTime();
	  dateTimestamp2 = new Date().setHours(0, 0, 0, 0);
	}
	
	if (data.connectionExpires && dateTimestamp2 > dateTimestamp1) {
	  licenseStatusColour = 'red';
	  licenseStatusLabel = 'Twitch API Connection Expired!';
	  connectionExpiresMeta = '(expired on ' + data.connectionExpires + ')';
	}		

	if (data.accessTokenErrorCode) {
		licenseStatusColour = 'red';
		licenseStatusLabel = 'Twitch API Connection Error!';
		connectionExpiresMeta = '('+data.accessTokenErrorMessage+')';
	}

	if (data.proStatus) {
		layoutArray = [
			{label: 'Wall', value: 'wall'},
			{label: 'Rail', value: 'rail'},
			{label: 'Player', value: 'player'},
			{label: 'Status', value: 'status'},
			{label: 'Vods', value: 'vods'},
			{label: 'Feature', value: 'feature'},
			{label: 'Showcase', value: 'showcase'},
		];
	} else {
		layoutArray = [
			{label: 'Wall', value: 'wall'},
			{label: 'Rail', value: 'rail'},
			{label: 'Player', value: 'player'},
			{label: 'Status', value: 'status'},
			{label: 'Vods', value: 'vods'},
		];
	}

	spanStyle = {
		color: licenseStatusColour,
		fontWeight: 'bold',
	  };

	return (
		<>
		<InspectorControls>		
			<PanelBody title={ __( 'Twitch API Connection', 'streamweasels' ) }>
				<PanelRow>
					<div>
						<p style={spanStyle}>{licenseStatusLabel}</p>
						<p style={spanStyle}>{connectionExpiresMeta}</p>
						{licenseStatusColour !== 'green' && ( <p>Your Twitch API Connection needs attention! Check out the <a href="admin.php?page=streamweasels" target="_blank">Twitch Integration Settings</a> for more information.</p> )}
					</div>
				</PanelRow>
			</PanelBody>
			<PanelBody title={ __( 'Twitch Integration Settings', 'streamweasels' ) }>
				<PanelRow>
					<SelectControl
						label={ __(
							'Layout',
							'streamweasels'
						) }
						help={ __(
							'Choose the desired layout for your streams.',
							'streamweasels'
						) }						
						value={ layout }
						onChange={ ( layout ) =>
							setAttributes( { layout: layout } )
						}
						options={ layoutArray }
					/>
				</PanelRow>			
				<div>
					<p><a href="https://support.streamweasels.com/article/78-twitch-integration-layout-guide" target="_blank">Click Here</a> for detailed examples of each layout.</p>
				</div>					
				<PanelRow>
					<TextControl
						label={ __(
							'Twitch Channels',
							'streamweasels'
						) }
						help={ __(
							'Enter the Twitch channels you want to display, separated by commas.',
							'streamweasels'
						) }
						value={ channels }
						onChange={ ( channels ) => setAttributes( { channels: channels} ) }
					/>
				</PanelRow>	
				<PanelRow>
					<TextControl
						label={ __(
							'Twitch Team',
							'streamweasels'
						) }
						help={ __(
							'Enter the Twitch team you want to display.',
							'streamweasels'
						) }
						value={ team }
						onChange={ ( team ) => setAttributes( { team: team} ) }
					/>
				</PanelRow>	
				<PanelRow>
					<TextControl
						label={ __(
							'Game',
							'streamweasels'
						) }
						help={ __(
							'Enter the game you want to display from Twitch.',
							'streamweasels'
						) }
						value={ game }
						onChange={ ( game ) => setAttributes( { game: game} ) }
					/>
				</PanelRow>		
				<PanelRow>
					<div>
						<RangeControl 
							label="Number of Streams"
							help={ __(
								'Enter the number of streams to display.',
								'streamweasels'
							) }							
							value={ limit }
							onChange={ ( value ) => setAttributes( { limit: value } ) }
							min={ 1 }
							max={ 15 }
						/>
					</div>
				</PanelRow>																										
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
			{licenseStatusColour == 'red' && (
				<div className="cp-swti__error">
					<p>Your Twitch API Connection needs attention! Check out the <a href="admin.php?page=streamweasels" target="_blank">Twitch Integration Settings</a> for more information.</p>
				</div>
			)}
			{channelsArray.length ? (
            <div className="cp-swti" data-colour="light" data-columns="4" data-layout={layout}>
				{layout === 'rail' && (
					<>
						<button className="cp-swti__arrow cp-swti__arrow-left" onClick={handlePrevious}>←</button>
						<button className="cp-swti__arrow cp-swti__arrow-right" onClick={handleNext}>→</button>
						<div className="cp-swti__container" style={{ transform: `translateX(-${(currentSlide * 33.33)}%)` }}>
							{channelsArray.slice(0, limit).map((channel, index) => (
								<div key={index} className="cp-swti__stream">
									{channel}
								</div>
							))}
						</div>
					</>
				)}
				{layout === 'wall' && (
					<>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className="cp-swti__stream">
								{channel}
							</div>
						))}
					</>
				)}
				{layout === 'player' && (
					<>
						<div className="cp-swti__player-wrapper">
							<div className="cp-swti__player">
								<p>{channelsArray[0]}</p>
							</div>
						</div>
						<div className="cp-swti__player-list">
							{channelsArray.slice(0, limit).map((channel, index) => (
								<div key={index} className="cp-swti__stream">
									{channel}
								</div>
							))}
						</div>
					</>
				)}	
				{layout === 'status' && (
					<>
						<div className="cp-swti__twitch-logo">
							<span class="dashicon dashicons dashicons-twitch"></span>
						</div>
						<div className="cp-swti__player-list">
							<div className="cp-swti__stream">
								<p><strong>{channelsArray[0]}</strong></p>
								<p>Streaming X for 100 Viewers.</p>
							</div>
						</div>
					</>
				)}	
				{layout === 'vods' && (
					<>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className="cp-swti__stream">
								<span className="cp-swti__play"></span>
								<span className="cp-swti__channel">{channel}</span>
							</div>
						))}
					</>
				)}	
				{layout === 'feature' && (
					<>
					<button className="cp-swti__arrow cp-swti__arrow-left" onClick={handlePrevious}>←</button>
					<button className="cp-swti__arrow cp-swti__arrow-right" onClick={handleNext}>→</button>
					<div className="cp-swti__container" style={{ transform: `translateX(-${(currentSlide * 33.33)}%)` }}>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className={`cp-swti__stream ${index === currentSlide + 1 ? 'active' : ''}`}>
								{channel}
							</div>
						))}
					</div>
				</>
				)}
				{layout === 'showcase' && (
					<>
					<button className="cp-swti__arrow cp-swti__arrow-left" onClick={handlePrevious}>←</button>
					<button className="cp-swti__arrow cp-swti__arrow-right" onClick={handleNext}>→</button>
					<div className="cp-swti__container" style={{ transform: `translateX(-${(currentSlide * 25)}%)` }}>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className="cp-swti__stream">
								{channel}
							</div>
						))}
					</div>
					</>
				)}																							
            </div>
		) : (
			<div className="cp-swti__empty">
				<p>Enter Twitch Channels, a Team or a Game to start displaying Streams from Twitch!</p>
			</div>
		)}
        </div>
		</>
	);
}
