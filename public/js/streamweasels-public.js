function streamWeasels(opts) {
    this._opts = opts;
    this.uuid = opts.uuid;
    this.game = opts.game;
    this.gameName = opts.gameName;
    this.language = opts.language;
    this.channels = opts.channels;
    this.titleFilter = opts.titleFilter,
    this.limit = opts.limit;
    this.layout = opts.layout;
    this.embed = opts.embed;
    this.embedTheme = opts.embedTheme;
    this.embedChat = ((opts.embedChat) ? 'video-with-chat' : 'video');
    this.embedTitle = opts.embedTitle;
    this.embedTitlePosition = opts.embedTitlePosition;
    this.embedMuted = opts.embedMuted;
    this.showOffline = opts.showOffline;
    this.showOfflineText = opts.showOfflineText;
    this.showOfflineImage = opts.showOfflineImage;
    this.autoplay = opts.autoplay;
    this.autoplayOffline = opts.autoplayOffline;
    this.autoplaySelect = opts.autoplaySelect;
    this.featured = opts.featured;
    this.offlineImage = opts.offlineImage;
    this.logoImage = opts.logoImage;
    this.profileImage = opts.profileImage;
    this.tileLayout = opts.tileLayout;
    this.tileSorting = opts.tileSorting;
    this.tileLive = opts.tileLive;
    this.logoBgColour = opts.logoBgColour;
    this.logoBorderColour = opts.logoBorderColour;
    this.tileBgColour = opts.tileBgColour;
    this.tileTitleColour = opts.tileTitleColour;
    this.tileSubtitleColour = opts.tileSubtitleColour;
    this.tileRoundedCorners = opts.tileRoundedCorners;
    this.hoverColour = opts.hoverColour;
    this.disableScroll = opts.disableScroll;
    this.translationsLive = opts.translationsLive;
    this.translationsOffline = opts.translationsOffline;
    this.translationsViewers = opts.translationsViewers;
    this.translationsStreaming = opts.translationsStreaming;
    this.translationsFor = opts.translationsFor;
    this.nonce = opts.nonce;
    this.wrapper = document.querySelector('.cp-streamweasels--'+this.uuid);
    this.target = this.wrapper.querySelector('.cp-streamweasels__streams');
    this.loading = this.wrapper.querySelector('.cp-streamweasels__loader');
    this.player = this.wrapper.querySelector('.cp-streamweasels__player');
    if (this.layout == 'vods') {
        this._getVods();
    } else {
        this._getStreams(this.channels,this.game,this.language);
    }
}

streamWeasels.prototype = Object.create(null,{
    constructor: {
        value: streamWeasels
    }, 
    _refresh: {
        value: function() {
            console.log('refresh active - refreshing streams...')
            this.refreshActive = true;
            this.target.innerHTML = '';
            this.wrapper.dataset.online = 0;
            this.wrapper.dataset.offline = 0;
            this.wrapper.dataset.onlineList = '';
            this.wrapper.classList.remove('cp-streamweasels--all-offline')
            this._getStreams(this.channels,this.game,this.language);
        }
    },
    _handleApiResponse: {
        value: function(data, functionName) {
            if (data.code === "rest_cookie_invalid_nonce") {
                console.error(`${functionName} - nonce validation failed:`, data);
                return false;
            }
            
            if (!Array.isArray(data.data)) {
                console.error(`${functionName} - unexpected data format:`, data);
                return false;
            }
            
            return true;
        }
    },   
    _getStreams:{
        value: function(channels, game, language, appendCount = 0, queryCount = 0, pagination = false){           
            // lets split the channels string into array chunks of 100
            var query = '';
            var channelsArray = [''];
            var channelsChunks = [];
            var onlineStreams =[];
            var xhr = [];
            var requestCount = 0;
            if (channels) {
                channelsArray =  channels.split(',');
            }
            for (i=0; i < channelsArray.length; i += 100) {
                let tempArray;
                tempArray = channelsArray.slice(i, i + 100);
                channelsChunks.push(tempArray)
            }
            for (var $i = 0; $i < channelsChunks.length; $i++) {
                (function($i){
                if (game) {
                    query = '&game_id='+game+'&first=100';
                    if (pagination) {
                        query = query + '&after='+pagination
                    }
                }
                if (channels) {
                    query = '&user_login='+channelsChunks[$i];
                }              
                if (language) {
                    if (game && !channels || !channels && !game) {
                        query = query + '&language='+language.toLowerCase();
                    }
                }
                xhr[$i] = new XMLHttpRequest();
                xhr[$i].open("GET", streamWeaselsVars.siteUrl + "/?rest_route=/streamweasels/v1/fetch-streams/"+query);
                xhr[$i].setRequestHeader("X-WP-Nonce", this.nonce);
                xhr[$i].onreadystatechange = function () {
                    if (xhr[$i].readyState === 4) {
                        requestCount++;
                        var data = JSON.parse(xhr[$i].responseText)

                        if (!this._handleApiResponse(data, '_getStreams')) {
                            return;
                        }

                        onlineStreams = onlineStreams.concat(data.data);
                        if (game && !channels) {
                            if (Object.keys(data.pagination).length > 0) {
                                pagination = data.pagination.cursor;
                            } else if (Object.keys(data.pagination).length == 0) {
                                pagination = 'end';
                                onlineStreams.push('empty stream')
                            }
                        }
                        if (requestCount == (channelsChunks.length)) {
                            if (channels && this.showOffline == 1 && channelsArray.length !== onlineStreams.length) {
                                this._getOfflineStreams(onlineStreams,channelsArray)
                            } else {
                                this._appendStreams(onlineStreams, false, appendCount, queryCount, pagination, this._clickHandler)
                            }
                        }                        
                    }
                }.bind(this);
                xhr[$i].send();
                }.bind(this))($i);
            }
        }
    },
    _getVods:{
        value: function(){
            var offlineCount = 0;
            gameIdArray = [];
            var xhrVods = [];
            var vodsArray =[];
            var randomVodVal = Math.floor(1000 + Math.random() * 9000);
            var vodChannel = this.wrapper.dataset.vodChannel;
            var requestCount = 0;

            if (vodChannel) {
                channelsArray = vodChannel.includes(',') ? vodChannel.split(',') : [vodChannel];
            } else {
                this.wrapper.classList.add('cp-streamweasels--no-vods')
                this._postAppend();
                return;
            }
            for (i=0; i < channelsArray.length; i++) {
                console.log(channelsArray[i])
            }
            for (var $i = 0; $i < channelsArray.length; $i++) {
                (function($i){
                var videoType = this.wrapper.dataset.vodType;
                var vodPeriod = this.wrapper.dataset.vodPeriod;
                var periodDate = new Date();
                if (videoType == 'clips') {
                    if (vodPeriod == 'month') {
                        periodDate.setDate(periodDate.getDate() - 30)
                    } else if (vodPeriod == 'week') {
                        periodDate.setDate(periodDate.getDate() - 7)
                    } else if (vodPeriod == 'day') {
                        periodDate.setDate(periodDate.getDate() - 1)
                    } else {
                        periodDate.setDate(periodDate.getDate() - 1000)
                    }
                    var today = new Date();
                    var todayDate = new Date(today).toISOString().slice(0, 10);
                    periodDate = new Date(periodDate).toISOString().slice(0, 10);
                    var videoParams = '&clip_type=clips&broadcaster_id='+channelsArray[$i]+'&started_at='+periodDate+'T00:00:00Z&ended_at='+todayDate+'T00:00:00Z&first=100';
                } else if (videoType == 'highlights') {
                    var videoParams = '&clip_type=videos&user_id='+channelsArray[$i]+'&type=highlight&first=100';
                } else if (videoType == 'past-broadcasts') {
                    var videoParams = '&clip_type=videos&user_id='+channelsArray[$i]+'&type=archive&first=100';
                } else {
                    var videoParams = '&clip_type=clips&broadcaster_id='+channelsArray[$i]+'&started_at='+periodDate+'T00:00:00Z&ended_at='+todayDate+'T00:00:00Z&first=100';
                }
                xhrVods[$i] = new XMLHttpRequest();
                xhrVods[$i].open("GET", streamWeaselsVars.siteUrl + "/?rest_route=/streamweasels/v1/fetch-video/"+videoParams);
                xhrVods[$i].setRequestHeader("X-WP-Nonce", this.nonce);
                xhrVods[$i].onreadystatechange = function () {
                    if (xhrVods[$i].readyState === 4) {
                        requestCount++;
                        var data = JSON.parse(xhrVods[$i].responseText)

                        if (!this._handleApiResponse(data, '_getVods')) {
                            return;
                        }

                        var vods = data.data;
                        vodsArray = vodsArray.concat(vods);
                        if (requestCount == (channelsArray.length)) {
                            if (vods.length) {
                                // sort array based on most recent
                                vodsArray.sort((a, b) => {
                                    // Convert the created_at strings to Date objects
                                    const dateA = new Date(a.created_at);
                                    const dateB = new Date(b.created_at);
                                    
                                    // Compare the dates to sort them
                                    return dateB - dateA; // For descending order
                                  });
                                this._appendVods(vodsArray)
                            } else {
                                this.wrapper.classList.add('cp-streamweasels--no-vods')
                                this._postAppend();
                            }
                        }
                    }
                }.bind(this);
                xhrVods[$i].send();
            }.bind(this))($i);      
        }        
        }
    },     
    _appendVods:{
        value: function(vods, appendCount = 0, queryCount = 0, pagination = false) {
            var vodCount = 0;
            var videoType = this.wrapper.dataset.vodType;
            var creatorFilter = this.wrapper.dataset.vodCreatorFilter;
            var creatorFilterArr = creatorFilter.split(",")            
            for (var $i = 0; $i < vods.length; $i++) {
                vodCount++;
                var vod = vods[$i];                           
                if (vodCount > this.limit) {
                    console.log('No more vods - limit of '+this.limit+' reached.')
                    if (videoType == 'clips') {
                        this._getGameName(gameIdArray)
                    }
                    this._postAppend();
                    break;
                }
                if (videoType == 'clips') {
                    var gameID = vod.game_id;
                    var username = vod.broadcaster_name;
                    var creator = vod.creator_name.toLowerCase();
                    var embed = vod.embed_url;
                    var thumbnail = vod.thumbnail_url;
                    var durationParsed = (vod.duration).toFixed();
                    if (durationParsed.length == 1) {
                        var duration = '0:0'+parseInt(vod.duration);
                    } else {
                        var duration = '0:'+parseInt(vod.duration);
                    }
                    var clippedBy = `<span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }">Clipped by ${creator}</span>`;
                    var dataCreator = `data-creator="${creator}"`;
                    if (gameID) {
                        gameIdArray.push(gameID);
                    }
                    if (creatorFilter !== '' && !(creatorFilterArr.indexOf(creator) !== -1)) {
                        console.log('Skipping clip by '+creator+'. Creator '+creator+' not found in '+creatorFilter);
                        vodCount--;
                        if ($i == vods.length - 1) {
                            if (videoType == 'clips') {
                                this._getGameName(gameIdArray)
                            }
                        }                                           
                        continue;
                    }
                } else {
                    var thumbnail = vod.thumbnail_url || streamWeaselsVars.thumbnail;
                    var thumbnail = thumbnail.replace('%{width}','480');
                    var thumbnail = thumbnail.replace('%{height}','272');
                    var username = vod.user_name;
                    var duration = vod.duration;
                    var dataCreator = '';
                    if (duration.includes('h')){
                        var durationSplit1 = duration.split('h')
                        var durationHours = durationSplit1[0]+':';
                    } else {
                        durationHours = '';
                    }
                    if (duration.includes('m')){
                        var durationSplit2 = duration.split('m')
                        if (duration.indexOf('m') > 2) {
                            var durationSplit3 = durationSplit2[0].split('h')
                            var durationMins = durationSplit3[1]+':';
                        } else {
                            var durationMins = durationSplit2[0]+':';
                        }
                        if (durationMins.length == 2) {
                            durationMins = '0'+durationMins;
                        }
                    }
                    if (duration.includes('s')){
                        var duration = duration.slice(0, -1);
                        var durationSplit4 = duration.split('m')
                        if (durationSplit4.length > 1) {
                            var durationSecs = durationSplit4[1];
                        } else {
                            var durationSecs = durationSplit4[0];
                        }
                        if (durationSecs.length == 1) {
                            durationSecs = '0'+durationSecs;
                        }                    
                    }    
                    duration = durationHours+durationMins+durationSecs;
                    var clippedBy = '';
                }
                var language = vod.language;
                var title = vod.title;
                var url = vod.url;
                var viewers = this._roundViewers(vod.view_count);
                var date = this._daysAgo(vod.created_at);
                var id = vod.id;
                var html = `
                    <div class="cp-stream" style="${this.hoverColour && 'background-color:'+this.hoverColour}" data-date="${vod.created_at}" ${dataCreator}>
                        <a class="cp-stream__inner cp-stream__inner--${videoType}" href="${url}" target="_blank" title="${title}" data-channel-name="${username}" data-vod-id="${id}" data-language="${language}" data-type="${videoType}" data-viewers="${vod.view_count}" data-embed="${embed}" style="${this.tileBgColour && 'background-color:'+this.tileBgColour };${this.tileRoundedCorners && 'border-radius:'+this.tileRoundedCorners+'px' };">
                            <div class="cp-stream__image">
                                <img loading="lazy" src="${thumbnail}">
                                <div class="cp-stream__overlay"></div>
                                <div class="cp-stream__status cp-stream__status-viewers"><span>${viewers} views</span></div>
                                <div class="cp-stream__status cp-stream__status-duration"><span>${duration}</span></div>
                                <div class="cp-stream__status cp-stream__status-date"><span>${date}</span></div>
                            </div>
                            <div class="cp-stream__info">
                                <img class="cp-stream__logo cp-stream__logo--${videoType}" src="${this.logoImage}" data-game-id="${gameID}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}"></img>
                                <div class="cp-stream__info-wrapper">
                                    <span class="cp-stream__title" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${title}</span>
                                    <span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }">${username}</span>
                                    ${clippedBy}
                                </div>
                            </div>
                        </a>
                    </div>
                `;
                this.target.insertAdjacentHTML('beforeend', html);  
                this.wrapper.dataset.online++;          
                if ($i == vods.length - 1 || this.wrapper.dataset.online == this.limit) {
                    if (videoType == 'clips') {
                        this._getGameName(gameIdArray)
                    }
                    if (this.wrapper.dataset.online == 0) {
                        this.wrapper.classList.add('cp-streamweasels--all-offline')
                    }                                
                    this._postAppend();
                }                                         
            }
        }
    },      
    _getOfflineStreams:{
        value: function(onlineStreams,channelsArray){
            var onlineStreamsArr = [];
            var offlineChannelsChunks = []
            var offlineStreams = [];
            var xhr = [];
            var requestCount = 0;
            for (var i=0;i<onlineStreams.length;i++) {
                var name = onlineStreams[i].user_login;
                onlineStreamsArr.push((name).toString());
            }
            var offlineArr = (channelsArray.filter(n => !onlineStreamsArr.includes(n)))
            for (i=0; i < offlineArr.length; i += 100) {
                let tempArray;
                tempArray = offlineArr.slice(i, i + 100);
                offlineChannelsChunks.push(tempArray)
            }
            for (var $i = 0; $i < offlineChannelsChunks.length; $i++) {
                (function($i){
                    var offlineChannels = '&login='+offlineChannelsChunks[$i].toString();
                    xhr[$i] = new XMLHttpRequest();
                    xhr[$i].open("GET", streamWeaselsVars.siteUrl + "/?rest_route=/streamweasels/v1/fetch-users/"+offlineChannels);
                    xhr[$i].setRequestHeader("X-WP-Nonce", this.nonce);
                    xhr[$i].onreadystatechange = function () {
                        if (xhr[$i].readyState === 4) {
                            requestCount++;
                            var data = JSON.parse(xhr[$i].responseText)

                            if (!this._handleApiResponse(data, '_getOfflineStreams')) {
                                return;
                            }

                            offlineStreams = offlineStreams.concat(data.data);
                            if (requestCount == (offlineChannelsChunks.length)) {
                                this._appendStreams(onlineStreams,offlineStreams)
                            }
                        }
                    }.bind(this);
                    xhr[$i].send();
                }.bind(this))($i);
            }
        }
    },         
    _appendStreams:{
        value: function(online,offline,appendCount = 0, queryCount = 0, pagination = false) {
            if (queryCount == 0) {
                this.wrapper.dataset.online = 0;
                this.wrapper.dataset.offline = 0;
                var onlineComplete = false;
                var offlineComplete = false;
            }
            var streamCount = 0;
            queryCount++;
            if (this.tileSorting == 'least') {
                online = online.reverse()
            }
            if (online.length) {
                for (var $i = 0; $i < online.length; $i++) {
                    streamCount++;
                    var user = online[$i];
                    // final page of results is reached and query ends
                    if (pagination && pagination == 'end') {
                        console.log('No more streams - twitch query completed.')
                        onlineComplete = true
                        offlineComplete = true;
                        break;
                    }
                    // limit is reached and query ends
                    if (streamCount > this.limit || appendCount == this.limit ) {
                        console.log('No more streams - limit of '+this.limit+' reached.')
                        onlineComplete = true
                        offlineComplete = true;
                        break;
                    }                     
                    // loop ends and limit is not yet reached, go to game page +100
                    if ($i == online.length - 1 && appendCount < this.limit && pagination && pagination !== 'end') {
                        console.log('Query '+queryCount+' finished - limit not yet reached.')
                        this._getStreams('',this.game,'',appendCount,queryCount,pagination);
                        break;
                    }                  
                    if (this.game !== '' && this.game !== online[$i].game_id) {
                        console.log('Skipping '+online[$i].user_name+'. Game '+online[$i].game_id+' does not match '+this.game);
                        streamCount--;
                        if($i == online.length - 1 && !pagination) {
                            onlineComplete = true;
                        }
                        continue;
                    }
                    if (this.titleFilter !== '' && !(user.title.toLowerCase().indexOf(this.titleFilter.toLowerCase()) !== -1)) {
                        console.log('Skipping '+online[$i].user_name+'. Title '+this.titleFilter+' not found in '+user.title.toLowerCase());
                        streamCount--;
                        if($i == online.length - 1 && !pagination) {
                            onlineComplete = true;
                        }                        
                        continue;
                    }          
                    var game = user.game_name;
                    var language = user.language;
                    var thumbnail = user.thumbnail_url || streamWeaselsVars.thumbnail;
                    if (this.layout == "showcase") {
                        var thumbnail = thumbnail.replace('{width}','888');
                        var thumbnail = thumbnail.replace('{height}','500');
                    } else {
                        var thumbnail = thumbnail.replace('{width}','440');
                        var thumbnail = thumbnail.replace('{height}','248');
                    }
                    var title = user.title;
                    var type = user.type;
                    var username = user.user_name;
                    var userLogin = user.user_login;
                    var viewers = this._roundViewers(user.viewer_count);
                    var logoImage = '';
                    if (this.logoImage !== '') {
                        logoImage = `<img class="cp-stream__logo" src="${this.logoImage}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}"></img>`
                    }                    
                    var liveStatus = '';
                    if (this.tileLive == 'live') {
                        liveStatus = `<div class="cp-stream__status cp-stream__status-live"><span>${this.translationsLive}</span></div>`
                    } else if (this.tileLive == 'viewer') {
                        liveStatus = `<div class="cp-stream__status cp-stream__status-viewers"><span>${viewers} <span>${this.translationsViewers}</span></span></div>`
                    } else if (this.tileLive == 'online') {
                        liveStatus = `<div class="cp-stream__status cp-stream__status-online"><span class="cp-stream__online-dot"></span><span>${viewers}</span></div>`
                    } else if (this.tileLive == 'none') {
                        liveStatus = `<div class="cp-stream__status cp-stream__status-none"></div>`
                    } else {
                        liveStatus = `<div class="cp-stream__status cp-stream__status-online"><span class="cp-stream__online-dot"></span><span>${viewers}</span></div>`
                    }  
                    var liveInfo = `
                    ${logoImage}<div class="cp-stream__info-wrapper">
                        <span class="cp-stream__title" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${username}</span>
                        <span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }"><span>${this.translationsStreaming}</span> <strong class="cp-stream__meta--game">${game}</strong> <span>${this.translationsFor}</span> <strong class="cp-stream__meta--viewers">${viewers}</strong> <span>${this.translationsViewers}</span></span>
                        </div>
                    `
                    if (this.wrapper.dataset.enableClassic == 1) {
                        var liveInfo = `
                        <div class="cp-stream__info-wrapper">
                            <span class="cp-stream__title cp-stream__title--classic" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }"><span class="swti-live-marker"></span>${this.wrapper.dataset.classicOnlineText}</span>
                        </div>
                        `
                    }              
                    var html = `
                        <div class="cp-stream cp-stream--online cp-stream--classic-${this.wrapper.dataset.enableClassic}" style="${this.hoverColour && 'background-color:'+this.hoverColour}" data-user="${userLogin.toLowerCase()}">
                            <a class="cp-stream__inner" href="https://www.twitch.tv/${userLogin}" target="_blank" title="${title}" data-channel-name="${userLogin.toLowerCase()}" data-game="${game}" data-language="${language}" data-type="${type}" data-viewers="${user.viewer_count}" data-status="online" style="${this.tileBgColour && 'background-color:'+this.tileBgColour };${this.tileRoundedCorners && 'border-radius:'+this.tileRoundedCorners+'px' };">
                                <div class="cp-stream__image">
                                    <img loading="lazy" src="${thumbnail}">
                                    <div class="cp-stream__overlay"></div>
                                </div>
                                <div class="cp-stream__info">
                                    ${liveInfo}
                                </div>
                                ${liveStatus}
                            </a>
                        </div>
                    `;
                    this.target.insertAdjacentHTML('beforeend', html);
                    appendCount++;
                    this.wrapper.dataset.online++;
                    this.wrapper.dataset.onlineList = this.wrapper.dataset.onlineList+userLogin.toLowerCase()+',';
                    // last item of loop
                    if (this.game == '' && $i == online.length - 1 && (!offline.length || this.showOffline == '0')) {
                        onlineComplete = true;
                        offlineComplete = true;
                    }
                    if (this.game == '' && $i == online.length - 1 && (offline.length && this.showOffline == '1')) {
                        onlineComplete = true;
                    }   
                    if (this.game !== '' && $i == online.length - 1 && !pagination) {
                        onlineComplete = true;
                    }
                };        
            } else {
                onlineComplete = true;
            }
            if (offline.length) {
                if (this.showOffline) {
                    for (var $i = 0; $i < offline.length; $i++) {
                        streamCount++;               
                        if (streamCount > this.limit) {
                            console.log('No more streams - limit of '+this.limit+' reached.')
                            offlineComplete = true;
                            break;
                        }
                        var user = offline[$i];
                        if (this.offlineImage) {
                            var thumbnail = this.offlineImage;
                        } else {
                            var thumbnail = user.offline_image_url || streamWeaselsVars.thumbnail;
                        }
                        var profile = user.profile_image_url;
                        var type = user.type;
                        var username = user.display_name;
                        var viewCount = user.view_count;
                        var logoImage = '';
                        if (this.logoImage !== '') {
                            logoImage = `<img class="cp-stream__logo" src="${this.logoImage}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}">`
                        } 
                        if (this.profileImage) {
                            logoImage = `<img class="cp-stream__logo" src="${profile}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}">`
                        }      
                        var offlineInfo = `
                        <div class="cp-stream__info-wrapper">
                            <span class="cp-stream__title" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${username}</span>
                            <span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }">${this.translationsOffline}</span>
                        </div>
                        `
                        if (this.wrapper.dataset.enableClassic == 1) {
                            var offlineInfo = `
                            <div class="cp-stream__info-wrapper">
                                <span class="cp-stream__title cp-stream__title--classic" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${this.wrapper.dataset.classicOfflineText}</span>
                            </div>
                            `
                        }                                                     
                        var html = `
                        <div class="cp-stream cp-stream--offline cp-stream--classic-${this.wrapper.dataset.enableClassic}" style="${this.hoverColour && 'background-color:'+this.hoverColour}">
                            <a class="cp-stream__inner" href="https://www.twitch.tv/${username}" target="_blank" data-channel-name="${username.toLowerCase()}" data-type="${type}" data-status="offline" style="${this.tileBgColour && 'background-color:'+this.tileBgColour };${this.tileRoundedCorners && 'border-radius:'+this.tileRoundedCorners+'px' }">
                                <div class="cp-stream__image">
                                    <img loading="lazy" src="${thumbnail}">
                                    <div class="cp-stream__overlay"></div>
                                </div>
                                <div class="cp-stream__info">
                                    ${logoImage}
                                    ${offlineInfo}
                                </div>                         
                            </a>
                        </div>
                        `;
                        this.target.insertAdjacentHTML('beforeend', html);
                        this.wrapper.dataset.offline++;
                        if ($i == offline.length - 1 && !pagination) {
                            offlineComplete = true;
                        }     
                    }; 
                }   
            } else {
                offlineComplete = true;
            }
            if (onlineComplete && offlineComplete) {
                this._postAppend();
            }
        }
    },
    _postAppend:{
        value: function() {
            if (this.loading) {
                this.loading.remove();
            }
            this._sortStreams();      
            if (this.wrapper.dataset.online == 0) {
                this.wrapper.classList.add('cp-streamweasels--all-offline')
                if (this.showOfflineText || this.showOfflineImage) {
                    this._offlineMessage();       
                }
                if (this.featured) {
                    this._moveFeaturedStream();   
                }
            }
            if (this.layout == 'rail') {
                this._startRailSlides(this.wrapper,this.target)
            }
            if (this.layout == 'showcase') {
                setTimeout(function() {
                    this._startShowcase(this.wrapper,this.target)
                }.bind(this), 300)
            }            
            if (this.layout == 'feature') {
                if (this.target.children.length) {
                var nodeCount = this.target.querySelectorAll('.cp-stream');
                    if(nodeCount.length == 1) {
                        var node = nodeCount[0];
                        var clone = node.cloneNode(true);
                        this.target.appendChild(clone)
                    }
                setTimeout(function() {
                    this._startFlipster(this.wrapper,this.target)
                }.bind(this), 300)
                }
            }
            if (this.layout == 'nav') {
                var channelNodes = document.querySelectorAll('.swti-twitch-nav--channel');
                var teamNodes = document.querySelectorAll('.swti-twitch-nav--team');
                var channels = this.wrapper.dataset.channels;
                var team = this.wrapper.dataset.team;
                if (channels) {
                    channelNodes.forEach(function(channelNode){
                        if (channelNode && channels) {
                            navNodeChild = channelNode.querySelector('a');
                            liveMeta = '';
                            liveMetaMarkup = '';
                            indicatorClass = '';
                            if (this.wrapper.dataset.online == '1') {
                                var streamInner = this.target.querySelector('.cp-stream__inner');
                                var liveMeta = streamInner.dataset.viewers;
                                navNodeChild.setAttribute('title', this._roundViewers(liveMeta)+' '+this.translationsViewers)
                                indicatorClass = 'swti-twitch-nav__indicator--live';
                                if (this.wrapper.dataset.showViewers > 0) {
                                    var liveMetaMarkup = `<span class="swti-twitch-nav__meta"></span>${this._roundViewers(liveMeta)}</span>`
                                }
                            } else if (this.wrapper.dataset.online > 1) {
                                var liveMeta = this.wrapper.dataset.online;
                                navNodeChild.setAttribute('title', liveMeta+' '+this.translationsStreaming)
                                indicatorClass = 'swti-twitch-nav__indicator--live';
                                if (this.wrapper.dataset.showViewers > 0) {
                                    var liveMetaMarkup = `<span class="swti-twitch-nav__meta"></span>${liveMeta}</span>`
                                }
                            } else {
                                navNodeChild.classList.add('swti-twitch-nav--offline')
                                navNodeChild.setAttribute('title', '0'+' '+this.translationsStreaming)
                            }
                        }                   
                        if (this.wrapper.dataset.hideDot == '1' && this.wrapper.dataset.online == '0') {
                            liveIndicatorMarkup = '';
                        } else {
                            liveIndicatorMarkup = `<span class="swti-twitch-nav__indicator ${indicatorClass}"></span>`
                            navNodeChild.classList.add('swti-twitch-nav--show-indicator')
                        }   
                        if (channelNode) {
                            twitchLogo = '<span class="swti-twitch-nav__logo"><svg width="22px" height="23px" viewBox="0 0 256 268" version="1.1" preserveAspectRatio="xMidYMid"><g><path d="M17.4579119,0 L0,46.5559188 L0,232.757287 L63.9826001,232.757287 L63.9826001,267.690956 L98.9144853,267.690956 L133.811571,232.757287 L186.171922,232.757287 L256,162.954193 L256,0 L17.4579119,0 Z M40.7166868,23.2632364 L232.73141,23.2632364 L232.73141,151.29179 L191.992415,192.033461 L128,192.033461 L93.11273,226.918947 L93.11273,192.033461 L40.7166868,192.033461 L40.7166868,23.2632364 Z M104.724985,139.668381 L127.999822,139.668381 L127.999822,69.843872 L104.724985,69.843872 L104.724985,139.668381 Z M168.721862,139.668381 L191.992237,139.668381 L191.992237,69.843872 L168.721862,69.843872 L168.721862,139.668381 Z" fill="inherit"></path></g></svg></span>';
                            html = 
                            `<span class="swti-twitch-nav">${liveIndicatorMarkup}${liveMetaMarkup}</span>`;
                            navNodeChild.insertAdjacentHTML('beforeend', html);
                            if (this.wrapper.dataset.twitchLogo > 0 ) {
                                if (this.wrapper.dataset.hideLogo == '1' && this.wrapper.dataset.online == '0') {
        
                                } else {
                                    navNodeChild.classList.add('swti-twitch-nav--show-logo')
                                    navNodeChild.insertAdjacentHTML('afterbegin', twitchLogo);
                                }
                            }
                        }                                     
                    }.bind(this))  
                }
                if (team) {
                    teamNodes.forEach(function(teamNode){
                        if (teamNode && team) {
                            liveMetaMarkup = '';
                            indicatorClass = '';
                            liveMeta = this.wrapper.dataset.online;
                            navNodeChild = teamNode.querySelector('a');
                            if (this.wrapper.dataset.online > 0) {
                                navNodeChild.setAttribute('title', liveMeta+' '+this.translationsStreaming)
                                indicatorClass = 'swti-twitch-nav__indicator--live';
                                if (this.wrapper.dataset.showTeamMembers > 0) {
                                    var liveMetaMarkup = `<span class="swti-twitch-nav__meta"></span>${liveMeta}</span>`
                                }
                            } else {
                                navNodeChild.classList.add('swti-twitch-nav--offline')
                                navNodeChild.setAttribute('title', '0'+' '+this.translationsStreaming)
                            }                                  
                        }                    
                        if (this.wrapper.dataset.hideDot == '1' && this.wrapper.dataset.online == '0') {
                            liveIndicatorMarkup = '';
                        } else {
                            liveIndicatorMarkup = `<span class="swti-twitch-nav__indicator ${indicatorClass}"></span>`
                            navNodeChild.classList.add('swti-twitch-nav--show-indicator')
                        }   
                        if (teamNode) {
                            twitchLogo = '<span class="swti-twitch-nav__logo"><svg width="22px" height="23px" viewBox="0 0 256 268" version="1.1" preserveAspectRatio="xMidYMid"><g><path d="M17.4579119,0 L0,46.5559188 L0,232.757287 L63.9826001,232.757287 L63.9826001,267.690956 L98.9144853,267.690956 L133.811571,232.757287 L186.171922,232.757287 L256,162.954193 L256,0 L17.4579119,0 Z M40.7166868,23.2632364 L232.73141,23.2632364 L232.73141,151.29179 L191.992415,192.033461 L128,192.033461 L93.11273,226.918947 L93.11273,192.033461 L40.7166868,192.033461 L40.7166868,23.2632364 Z M104.724985,139.668381 L127.999822,139.668381 L127.999822,69.843872 L104.724985,69.843872 L104.724985,139.668381 Z M168.721862,139.668381 L191.992237,139.668381 L191.992237,69.843872 L168.721862,69.843872 L168.721862,139.668381 Z" fill="inherit"></path></g></svg></span>';
                            html = 
                            `<span class="swti-twitch-nav">${liveIndicatorMarkup}${liveMetaMarkup}</span>`;
                            navNodeChild.insertAdjacentHTML('beforeend', html);
                            if (this.wrapper.dataset.twitchLogo > 0 ) {
                                if (this.wrapper.dataset.hideLogo == '1' && this.wrapper.dataset.online == '0') {
        
                                } else {
                                    navNodeChild.classList.add('swti-twitch-nav--show-logo')
                                    navNodeChild.insertAdjacentHTML('afterbegin', twitchLogo);
                                }
                            }
                        }                                     
                    }.bind(this))    
                }                
            }            
            if (this.layout == 'status') {
                this.wrapper.classList.add('cp-streamweasels--loaded')
                setTimeout(function() {
                    this.target.classList.add('cp-streamweasels__streams--loaded')
                }.bind(this), 1000)
                setTimeout(function() {
                    this.wrapper.classList.add('cp-streamweasels--animation-finished')
                }.bind(this), 2000)  
                if (this.target.classList.contains('cp-streamweasels__streams--carousel-0')) {
                    setTimeout(function() {
                        jQuery(this.target).slick({
                            dots: false,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            swipeToSlide: true,
                            nextArrow: '<button type="button" class="slick-prev"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/><path d="M0-.5h24v24H0z" fill="none"/></svg></button>',
                            prevArrow: '<button type="button" class="slick-next"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/><path d="M0-.25h24v24H0z" fill="none"/></svg></button>',
                        })
                    }.bind(this), 3000)
                }
            }
            this._clickHandler();   
        }
    },
    _moveFeaturedStream:{
        value: function() {
            var streams = this.wrapper.querySelectorAll('.cp-stream--offline');
            var featuredStream;
            streams.forEach(tile => {
                if (this.featured.toLowerCase() == tile.children[0].dataset.channelName.toLowerCase()) {
                    featuredStream = tile;
                    this.target.prepend(featuredStream)
                }
            })
        }
    },
    _clickHandler:{
        value: function() {
            if (this.autoplay && this.embed !== 'twitch' && this.layout !== 'vods' && this.layout !== 'nav') {
                var streams = this.wrapper.querySelectorAll('.cp-stream--online')
                if (streams.length > 0) {
                    var streamCount = streams.length - 1;
                    var streamRandom = Math.floor(Math.random() * streams.length)
                    if (this.autoplaySelect == 'most') {
                        var featuredStream = streams[0].querySelector('a')
                    } else if (this.autoplaySelect == 'least') {
                        var featuredStream = streams[streamCount].querySelector('a')
                    } else if (this.autoplaySelect == 'random') {
                        var featuredStream = streams[streamRandom].querySelector('a')
                    } else {
                        var featuredStream = streams[0].querySelector('a')
                    }
                    if (this.featured) {
                        streams.forEach(tile => {
                            if (this.featured.toLowerCase() == tile.children[0].dataset.channelName.toLowerCase()) {
                                featuredStream = tile.children[0];
                            }
                        })
                    }
                    // refresh is active, and featured stream is not the same as the embedded player
                    if (this.refreshActive && this.player.dataset.channelName !== featuredStream.dataset.channelName) {
                        var onlineList = this.wrapper.dataset.onlineList;
                        var onlineListArray = onlineList.split(',');
                        // if refreshing, only change the embed, if the current embed is offline
                        if (!onlineListArray.includes(this.player.dataset.channelName)) {
                            this._embedStream(featuredStream);
                        }
                    } else {
                        this._embedStream(featuredStream);
                    }
                } else {
                    if (this.autoplayOffline && this.layout !== 'vods') {
                        var streams = this.wrapper.querySelectorAll('.cp-stream--offline')
                        var featuredStream = streams[0].querySelector('a')
                        this._embedStream(featuredStream);
                    }
                }
            }           
            var tiles = this.wrapper.querySelectorAll('.cp-stream__inner');
            tiles.forEach(tile => {
                tile.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (this.layout == 'vods') {
                        this._embedVod(tile);                         
                    } else {
                        this._embedStream(tile);                         
                    }
                }.bind(this));
            })
        }
    },
    _offlineMessage: {
        value: function() {
            var offlineHTML =
            `<div class="cp-streamweasels__offline">
                ${this.showOfflineImage && "<img src='"+this.showOfflineImage+"'>"}
                ${this.showOfflineText && "<h3>"+this.showOfflineText+"</h3>"}
            </div>`;
            if (this.embed !== 'inside' && this.layout !== 'nav') {
                this.wrapper.querySelector('.cp-streamweasels__offline-wrapper').innerHTML = '';
                this.wrapper.querySelector('.cp-streamweasels__offline-wrapper').insertAdjacentHTML('beforeend', offlineHTML)
            }
        }
    },
    _embedStream:{
        value: function(channelNode) {
            var body = document.querySelector('body')
            var modalHtml =
            `<div class="cp-streamweasels-modal">
                ${(this.embedTitle && this.embedTitlePosition == 'top' && channelNode.dataset.status == 'online') ? '<div class="cp-streamweasels-modal__title cp-streamweasels-modal__title--'+this.embedChat+'"><p>'+channelNode.getAttribute('title')+'</p></div>' : ''}
                <div class="cp-streamweasels-modal__player cp-streamweasels-modal__player--${this.embedChat}"></div>
                ${(this.embedTitle && this.embedTitlePosition == 'bottom' && channelNode.dataset.status == 'online') ? '<div class="cp-streamweasels-modal__title cp-streamweasels-modal__title--'+this.embedChat+'"><p>'+channelNode.getAttribute('title')+'</p></div>' : ''}
            </div>`
            var player = this.wrapper.querySelector('.cp-streamweasels__player');
            if (this.embed == 'twitch') {
                window.open('https://twitch.tv/'+channelNode.dataset.channelName, '_blank');
            }
            if (this.layout == 'status' && this.embed == 'page') {
                this.embed = 'popup'
            }
            if (this.layout == 'feature' && this.embed == 'inside') {
                setTimeout(function() {
                    var featureEmbed = this.wrapper.querySelector('.flipster__item--current');
                    var featureEmbedInner = featureEmbed.querySelector('.cp-stream__image');
                    var featureEmbedIframe = featureEmbedInner.querySelector('iframe')
                    var featureEmbedImage = featureEmbedInner.querySelector('img');
                    var featureEmbedInnerWidth = featureEmbedImage.width
                    var featureEmbedInnerHeight = featureEmbedImage.height
                    if (this.tileLayout == 'detailed') {
                        featureEmbedInnerHeight = featureEmbedInnerHeight + 48;
                    }
                    featureEmbed.classList.add('flipster__item--embed')
                    if (featureEmbedIframe) {
                        featureEmbedIframe.remove()
                    }
                    var embed = new Twitch.Embed(featureEmbedInner, {
                        width: featureEmbedInnerWidth+'px',
                        height: featureEmbedInnerHeight+'px',
                        channel: channelNode.dataset.channelName,
                        theme: this.embedTheme,
                        layout: 'video'
                    });
                    this.wrapper.classList.add('cp-streamweasels--embed-page-active');
                    this.embedTitle = false;
                }.bind(this), 300)
            }            
            if (this.embed == 'page') {
                var activePlayer = player.querySelector('iframe') ?? false;
                var iframeSrc = activePlayer ? activePlayer.getAttribute('src') : false;
                var iframeSrcParams = iframeSrc ? new URLSearchParams(new URL(iframeSrc).search) : false;
                var iframeSrcChannel = iframeSrcParams ? iframeSrcParams.get('channel') : false;
                    if (activePlayer && iframeSrcChannel == channelNode.dataset.channelName) {
                        console.log('Stream already playing')
                    } else {
                        player.innerHTML = '';
                        var embed = new Twitch.Embed(player, {
                            width: '100%',
                            height: '100%',
                            channel: channelNode.dataset.channelName,
                            theme: this.embedTheme,
                            layout: this.embedChat
                        });
                        if (this.layout == 'wall' && !this.refreshActive && this.disableScroll == '0' ) {
                            player.scrollIntoView();
                        }
                        this.wrapper.classList.add('cp-streamweasels--embed-page-active');
                        this.player.classList.add('cp-streamweasels__player--embed-page-active');
                        this.player.dataset.channelName = channelNode.dataset.channelName;
                    }
            }
            if (this.embed == 'popup') {
                body.insertAdjacentHTML('beforeend', modalHtml);
                var wrapper = document.querySelector('.cp-streamweasels-modal')
                var modalPlayer = document.querySelector('.cp-streamweasels-modal__player')
                var embed = new Twitch.Embed(modalPlayer, {
                    width: '100%',
                    height: '100%',
                    channel: channelNode.dataset.channelName,
                    theme: this.embedTheme,
                    layout: this.embedChat
                });
                this._modalControls(wrapper);     
                this.wrapper.classList.add('cp-streamweasels--embed-popup-active');    
            }   
            if (this.embedMuted && this.embed !== 'twitch') {
                this._muteEmbed(embed, true)
            }
            if (this.embedTitle && this.embed == 'page' && channelNode.dataset.status == 'online') {
                this._embedTitle(channelNode.getAttribute('title'))
            }
        }
    },
    _embedVod:{
        value: function(channelNode) {
            var videoType = this.wrapper.dataset.vodType;
            var body = document.querySelector('body')
            var player = this.wrapper.querySelector('.cp-streamweasels__player');    
            var modalHtml =
            `<div class="cp-streamweasels-modal">
                ${(this.embedTitle && this.embedTitlePosition == 'top') ? '<div class="cp-streamweasels-modal__title"><p>'+channelNode.getAttribute('title')+'</p></div>' : ''}
                <div class="cp-streamweasels-modal__player"></div>
                ${(this.embedTitle && this.embedTitlePosition == 'bottom') ? '<div class="cp-streamweasels-modal__title"><p>'+channelNode.getAttribute('title')+'</p></div>' : ''}
            </div>`
            var player = this.wrapper.querySelector('.cp-streamweasels__player');    
            if (this.embed == 'twitch') {
                window.open(channelNode.getAttribute('href'), '_blank');
            }
            if (this.embed == 'page') {
                player.innerHTML = '';
                if (videoType == 'clips') {
                    var iframe = `
                    <iframe
                        src="https://clips.twitch.tv/embed?clip=${channelNode.dataset.vodId}&parent=${window.location.host}&muted=${this.embedMuted && true}"
                        height="100%"
                        width="100%"
                        allowfullscreen="true">
                    </iframe>
                `
                player.insertAdjacentHTML('beforeend', iframe);
                } else {
                    var embed = new Twitch.Embed(player, {
                        width: '100%',
                        height: '100%',
                        video: channelNode.dataset.vodId,
                        theme: this.embedTheme,
                        layout: this.embedChat
                    });
                }
                player.scrollIntoView();
                this.wrapper.classList.add('cp-streamweasels--embed-page-active');
                this.player.classList.add('cp-streamweasels__player--embed-page-active');
            }
            if (this.embed == 'popup') {
                body.insertAdjacentHTML('beforeend', modalHtml);
                var wrapper = document.querySelector('.cp-streamweasels-modal')
                var modalPlayer = document.querySelector('.cp-streamweasels-modal__player')
                if (videoType == 'clips') {
                    var iframe = `
                    <iframe
                        src="https://clips.twitch.tv/embed?clip=${channelNode.dataset.vodId}&parent=${window.location.host}&muted=${this.embedMuted && true}"
                        height="100%"
                        width="100%"
                        allowfullscreen="true">
                    </iframe>
                    `
                    modalPlayer.insertAdjacentHTML('beforeend', iframe);
                } else {
                    var embed = new Twitch.Embed(modalPlayer, {
                        width: '100%',
                        height: '100%',
                        video: channelNode.dataset.vodId,
                        theme: this.embedTheme,
                        layout: this.embedChat
                    });
                }
                this._modalControls(wrapper);     
                this.wrapper.classList.add('cp-streamweasels--embed-popup-active');    
            }   
            if (this.embedTitle && this.embed == 'page') {
                this._embedTitle(channelNode.getAttribute('title'))
            }
        }
    },    
    _sortStreams:{
        value: function() {
            var streams = this.wrapper.querySelector('.cp-streamweasels__streams');
            [...streams.children]
                .sort(function(a,b) {
                    if (this.tileSorting == 'alpha') {
                        if (this.layout == 'vods') {
                            return (new Date(a.children[0].dataset.date) > new Date(b.children[0].dataset.date) ? 1 : -1);
                        } else {
                            return (a.children[0].dataset.channelName>b.children[0].dataset.channelName? 1: -1);
                        }
                        return (a.children[0].dataset.channelName>b.children[0].dataset.channelName? 1: -1);
                    }                    
                    if (this.tileSorting == 'least') {
                        return a.children[0].dataset.viewers - b.children[0].dataset.viewers;
                    }
                    if (this.tileSorting == 'most') {
                        return b.children[0].dataset.viewers - a.children[0].dataset.viewers;
                    }      
                    if (this.tileSorting == 'random') {
                        return 0.5 - Math.random()
                    }                                   
                }.bind(this))
                .forEach(node=> {
                    streams.appendChild(node)
                });
        }
    },
    _muteEmbed:{
        value: function(embed) {
            embed.addEventListener(Twitch.Embed.VIDEO_READY, () => {
                var player = embed.getPlayer();
                player.setMuted(true);
            });
        }
    },
    _embedTitle:{
        value: function(title) {
            var titleWrapper = this.wrapper.querySelector('.cp-streamweasels__title');
            titleWrapper.innerHTML = '<p>'+title+'</p>';
        }
    },    
    _modalControls:{
        value: function(modal) {
            modal.addEventListener('click', function(e) {
                modal.remove();
            })
            document.onkeydown = function(e){
                if(e.key === 'Escape'){
                    modal.remove();
                }
            }
        }
    },
    _roundViewers:{
        value: function(viewers) {
            if (viewers > 1000 && viewers < 999999) {
                viewers = (viewers / 1000).toFixed(1) + 'K';
                if (viewers.slice(viewers.length - 3) == '.0K') {
                    viewers = viewers.replace('.0K', 'K')
                }                
            } 
            return viewers;
        }
    },
    _getGameName:{
        value: function(gameIdArray) {
            var gameIdArrayFiltered = gameIdArray.filter((c, index) => {
                return gameIdArray.indexOf(c) === index;
            });
            var gameIds = '&id='+gameIdArrayFiltered.toString();
            var randomGameVal = Math.floor(1000 + Math.random() * 9000);
            var xhr = [];
            xhr[randomGameVal] = new XMLHttpRequest();
            xhr[randomGameVal].open("GET", streamWeaselsVars.siteUrl + "/?rest_route=/streamweasels/v1/fetch-games/"+gameIds);
            xhr[randomGameVal].setRequestHeader("X-WP-Nonce", this.nonce);
            xhr[randomGameVal].onreadystatechange = function () {
                if (xhr[randomGameVal].readyState === 4) {
                    var data = JSON.parse(xhr[randomGameVal].responseText)

                    if (!this._handleApiResponse(data, '_getGameName')) {
                        return;
                    }

                    this._setGameName(data.data)
                }
            }.bind(this)
            xhr[randomGameVal].send()
        }
    },
    _setGameName:{
        value: function(gameData) {
            var gameDataJson = new Object();
            for (var i = 0; i < gameData.length; i++) {
                gameDataJson[gameData[i].id] = {
                    name: gameData[i].name,
                    art: gameData[i].box_art_url,
                }
            }
            if (this.target.children.length) {
                var nodes = this.target.querySelectorAll('.cp-stream');
                nodes.forEach(function(item, index, array) {
                    var gamePlaceholder = item.querySelector('.cp-stream__logo')
                    var gamePlaceholderId = gamePlaceholder.dataset.gameId;
                    var boxArt = gameDataJson[gamePlaceholderId]?.art || '';
                    var boxArt = boxArt.replace('{width}','80');
                    var boxArt = boxArt.replace('{height}','106');
                    gamePlaceholder.setAttribute('src', boxArt)
                })
            }
        }
    },
    _startRailSlides:{
        value: function(wrapper,target) {
            var slidesToShow = 3;
            if (wrapper.offsetWidth < 768) {
              slidesToShow = 2;
            }
            if (wrapper.offsetWidth < 560) {
              slidesToShow = 1;
            }
            jQuery(target).slick({
                dots: false,
                slidesToShow: slidesToShow,
                slidesToScroll: 1,
                swipeToSlide: true,
                nextArrow: '<button type="button" class="slick-prev"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/><path d="M0-.5h24v24H0z" fill="none"/></svg></button>',
                prevArrow: '<button type="button" class="slick-next"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/><path d="M0-.25h24v24H0z" fill="none"/></svg></button>',
                responsive: [
                  {
                    breakpoint: 768,
                    settings: {
                      slidesToShow: 2
                    }
                  },
                  {
                    breakpoint: 560,
                    settings: {
                      slidesToShow: 1
                    }
                  }
                ]
            })
        }
    },
    _startShowcase:{
        value: function(wrapper,target) {
            var wrapperInner = this.wrapper.querySelector('.cp-streamweasels__inner');
            if (wrapperInner.offsetWidth >= 1920) {
                slidesToShow = 7;
            } else if (wrapperInner.offsetWidth >= 1680) {
                slidesToShow = 6;
            } else if (wrapperInner.offsetWidth >= 1440) {
                slidesToShow = 5;
            } else if (wrapperInner.offsetWidth >= 1280) {
                slidesToShow = 4;
            } else if (wrapperInner.offsetWidth >= 1024) {
                slidesToShow = 3;
            } else if (wrapperInner.offsetWidth >= 768) {
                slidesToShow = 2;
            } else if (wrapperInner.offsetWidth >= 560) {
                slidesToShow = 1;
            }
            jQuery(target).slick({
                dots: false,
                slidesToShow: slidesToShow,
                slidesToScroll: 1,
                swipeToSlide: true,
                nextArrow: '<button type="button" class="slick-prev"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/><path d="M0-.5h24v24H0z" fill="none"/></svg></button>',
                prevArrow: '<button type="button" class="slick-next"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/><path d="M0-.25h24v24H0z" fill="none"/></svg></button>',
                responsive: [
                    {
                        breakpoint: 9999,
                        settings: {
                            slidesToShow: wrapperInner.offsetWidth >= 1680 ? 6 : slidesToShow
                        }
                        },					
                    {
                        breakpoint: 1680,
                        settings: {
                            slidesToShow: wrapperInner.offsetWidth >= 1440 ? 5 : slidesToShow
                        }
                        },			
                    {
                        breakpoint: 1440,
                        settings: {
                            slidesToShow: wrapperInner.offsetWidth >= 1280 ? 4 : slidesToShow
                        }
                        },			
                    {
                        breakpoint: 1280,
                        settings: {
                            slidesToShow: wrapperInner.offsetWidth >= 1024 ? 4 : slidesToShow
                        }
                        },			
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: wrapperInner.offsetWidth >= 768 ? 3 : slidesToShow
                        }
                        },			
                    {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: wrapperInner.offsetWidth >= 560 ? 2 : slidesToShow
                    }
                    },
                    {
                    breakpoint: 560,
                    settings: {
                        slidesToShow: 1
                    }
                    }
                ]
            })
        }
    },
    _startFlipster:{
        value: function(wrapper,target) {
            var numberOnline = target.querySelectorAll('.cp-stream--online').length;
            var startAt = 2;
            switch(numberOnline) {
                case 1:
                    startAt = 0;
                    break;
                case 2:
                    startAt = 1;
                    break;
            }
            jQuery(wrapper).flipster({
                style: 'carousel',
                itemContainer: '.cp-streamweasels__streams',
                itemSelector: '.cp-stream',
                loop: true,
                buttons: true,
                spacing: -0.5,
                scrollwheel: false,
                start: startAt,
                onItemSwitch: function() {
                    var activeEmbed = wrapper.querySelector('.flipster__item--embed')
                    var slidePast = wrapper.querySelector('.flipster__item--past-1')
                    var slideFuture = wrapper.querySelector('.flipster__item--future-1')
                    if (activeEmbed) {
                        var activeiFrame = activeEmbed.querySelector('iframe')
                        var activeiPast = slidePast ? slidePast.querySelector('iframe') : '';
                        var activeiFuture = slideFuture ? slideFuture.querySelector('iframe') : '';
                        activeEmbed.classList.remove('flipster__item--embed')
                        if (activeiFrame) {
                            activeiFrame.remove()
                        }
                        if (activeiPast) {
                            activeiFrame.remove()
                        }	
                        if (activeiFuture) {
                            activeiFrame.remove()
                        }							
                    }
                }
            });
        }
    },
    _daysAgo:{
        value: function(date) {
            const now = new Date();
            // Mimick a backend date
            var daysAgo = new Date(date);
            daysAgo.setDate(daysAgo.getDate());
            // Compare both, outputs in miliseconds
            var ago = now - daysAgo;
            var ago = Math.floor(ago / 1000);
            var part = 0;
            if (ago < 2) { return "a moment ago"; }
            if (ago < 5) { return "moments ago"; }
            if (ago < 60) { return ago + " seconds ago"; }    
            if (ago < 120) { return "a minute ago"; }
            if (ago < 3600) {
              while (ago >= 60) { ago -= 60; part += 1; }
              return part + " minutes ago";
            }
            if (ago < 7200) { return "an hour ago"; }
            if (ago < 86400) {
              while (ago >= 3600) { ago -= 3600; part += 1; }
              return part + " hours ago";
            }    
            if (ago < 172800) { return "a day ago"; }
            if (ago < 604800) {
              while (ago >= 172800) { ago -= 172800; part += 1; }
              return part + " days ago";
            }
            if (ago < 1209600) { return "a week ago"; }
            if (ago < 2592000) {
              while (ago >= 604800) { ago -= 604800; part += 1; }
              return part + " weeks ago";
            }
            if (ago < 5184000) { return "a month ago"; }
            if (ago < 31536000) {
              while (ago >= 2592000) { ago -= 2592000; part += 1; }
              return part + " months ago";
            }
            if (ago < 1419120000) { // 45 years, approximately the epoch
              return "more than year ago";
            }
        }
    }
})

function fetchFreshNonce() {
    return fetch(streamWeaselsVars.ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_fresh_nonce'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('fetchFreshNonce() failed');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data && data.data.nonce) {
            return data.data.nonce;
        } else {
            throw new Error('fetchFreshNonce() success - data not valid: ' + JSON.stringify(data));
        }
    });
}

var streamWeaselsNodes = document.querySelectorAll('.cp-streamweasels');
var navChannelCount = 0;
var navTeamCount = 0;
streamWeaselsNodes.forEach(function(item, index, array) {
    var uuid = item.dataset.uuid;
    var layout = item.dataset.layout;
    if (layout == 'nav') {
        if (item.dataset.channels) {
            navChannelCount++;
            if (navChannelCount > 1) {
                return;
            }
        }
        if (item.dataset.team) {
            navTeamCount++;
            if (navTeamCount > 1) {
                return;
            }
        }        
    }

fetchFreshNonce().then(function(freshNonce) {
    var streamWeaselsVarUuid = eval('streamWeaselsVars' + uuid);
    var streamWeaselsInit = new streamWeasels({
        uuid: uuid,
        gameName: streamWeaselsVarUuid.gameName,
        game: streamWeaselsVarUuid.gameid,
        language: streamWeaselsVarUuid.language,
        channels: streamWeaselsVarUuid.channels,
        team: streamWeaselsVarUuid.team,
        titleFilter: streamWeaselsVarUuid.titleFilter,
        limit: streamWeaselsVarUuid.limit,
        layout: streamWeaselsVarUuid.layout,
        embed: streamWeaselsVarUuid.embed,
        embedTheme: streamWeaselsVarUuid.embedTheme,
        embedChat: streamWeaselsVarUuid.embedChat,
        embedTitle: streamWeaselsVarUuid.embedTitle,
        embedTitlePosition: streamWeaselsVarUuid.embedTitlePosition,
        embedMuted: streamWeaselsVarUuid.embedMuted,
        showOffline: streamWeaselsVarUuid.showOffline,
        showOfflineText: streamWeaselsVarUuid.showOfflineText,
        showOfflineImage: streamWeaselsVarUuid.showOfflineImage,
        autoplay: streamWeaselsVarUuid.autoplay,
        autoplayOffline: streamWeaselsVarUuid.autoplayOffline,
        autoplaySelect: streamWeaselsVarUuid.autoplaySelect,
        featured: streamWeaselsVarUuid.featured,
        offlineImage: streamWeaselsVarUuid.offlineImage,
        logoImage: streamWeaselsVarUuid.logoImage,
        profileImage: streamWeaselsVarUuid.profileImage,
        tileLayout: streamWeaselsVarUuid.tileLayout,
        tileSorting: streamWeaselsVarUuid.tileSorting,
        tileLive: streamWeaselsVarUuid.tileLive,
        logoBgColour: streamWeaselsVarUuid.logoBgColour,
        logoBorderColour: streamWeaselsVarUuid.logoBorderColour,
        maxWidth: streamWeaselsVarUuid.maxWidth,
        tileBgColour: streamWeaselsVarUuid.tileBgColour,
        tileTitleColour: streamWeaselsVarUuid.tileTitleColour,
        tileSubtitleColour: streamWeaselsVarUuid.tileSubtitleColour,
        tileRoundedCorners: streamWeaselsVarUuid.tileRoundedCorners,
        hoverColour: streamWeaselsVarUuid.hoverColour,
        refresh: streamWeaselsVarUuid.refresh,
        disableScroll: streamWeaselsVarUuid.disableScroll,
        translationsLive: streamWeaselsVarUuid.translationsLive,
        translationsOffline: streamWeaselsVarUuid.translationsOffline,
        translationsViewers: streamWeaselsVarUuid.translationsViewers,
        translationsStreaming: streamWeaselsVarUuid.translationsStreaming,        
        translationsFor: streamWeaselsVarUuid.translationsFor,
        nonce: freshNonce
    });

    if (streamWeaselsVarUuid.layout == 'wall' && streamWeaselsVarUuid.refresh == '1') {
        setInterval(function() {
            streamWeaselsInit._refresh();
        }, 60000);
    }
}).catch(function(error) {
    console.error('Error fetching nonce:', error);
});

})