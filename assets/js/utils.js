const utils = {
    sendIceCandidate : function(candidate, socket, roomId, allowedIceType) {
        let that = this;
        if (candidate == null) {
            return;
        }

        if (allowedIceType === 'relay' && candidate.candidate.indexOf('typ relay') === -1) {
            return;
        }

        that.send(socket, messageTypes.ICE, roomId, {
            'ice': candidate
        });
    },

    sendOffer : function(peerConnection, description, iceServers, socket, roomId) {
        let that = this;
        peerConnection.setLocalDescription(description).then(function() {
            that.send(socket, messageTypes.OFFER, roomId, {
                'sdp': peerConnection.localDescription,
                'iceServers': iceServers
            });
        }).catch(self.errorHandler);
    },

    sendAnswer : function(peerConnection, description, socket, roomId) {
        let that = this;
        peerConnection.setLocalDescription(description).then(function() {
            that.send(socket, messageTypes.ANSWER, roomId, peerConnection.localDescription);
        }).catch(self.errorHandler);
    },

    closePeerIfDisconnected: function(peerConnection) {
        if ('disconnected' === peerConnection.iceConnectionState) {
            peerConnection.close();
        }
    },

    errorHandler : function(error) {
        console.log(error);
    },

    send : function(socketConnection, type, roomId, body) {
        if (typeof socketConnection === "undefined") {
            return;
        }
        if (socketConnection.readyState !== 1) {
            return;
        }
        socketConnection.send(JSON.stringify({type: type, roomId: roomId, body: body}));
    },

    filterServersIfEdge: function(iceServers) {
        if (window.navigator.userAgent.indexOf("Edge") > -1) {
            return [iceServers[0]];
        }
        return iceServers;
    },

    resizeCanvas: function(canvas, localVideo, newHeight) {
        canvas.setAttribute('height', newHeight);
        let newWidth = newHeight * localVideo.videoWidth / localVideo.videoHeight;
        canvas.setAttribute('width', newWidth);
    },

    extractBase64FromVideo: function(canvas, localVideo) {
        canvas.getContext('2d').drawImage(localVideo, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg');
    },

    takePhoto: function(canvas, localVideo, snapPath, roomId) {
        let base64 = this.extractBase64FromVideo(canvas, localVideo);

        return jQuery.ajax({
            url: snapPath,
            method: 'POST',
            data: {
                user: roomId,
                base64: base64
            }
        });
    },

    loopTakePhoto: function(canvas, localVideo, snapPath, roomId) {
        let that = this;
        this.takePhoto(canvas, localVideo, snapPath, roomId).always(function() {
            setTimeout(function() {
                that.loopTakePhoto(canvas, localVideo, snapPath, roomId);
            }, 58000);
        });
    },

    postMismatch: function(roomId, beforeBase64, currentBase64, mismatch) {
        return jQuery.ajax({
            url: '/photo/mismatch',
            method: 'POST',
            data: {
                'photoBefore[user]': roomId,
                'photoBefore[base64]': beforeBase64,
                'photoAfter[user]': roomId,
                'photoAfter[base64]': currentBase64,
                'mismatch': mismatch
            }
        });
    }

};

const messageTypes = {
    PING:                       'PING',
    PONG:                       'PONG',
    SHOOTER_INTRODUCTION:       'SHOOTER_INTRODUCTION',
    WATCHER_INTRODUCTION:       'WATCHER_INTRODUCTION',
    OFFER:                      'OFFER',
    ANSWER:                     'ANSWER',
    ICE:                        'ICE',
    SHOOTER_NOT_EXISTS:         'SHOOTER_NOT_EXISTS',
    SHOOTER_SEEMS_NOT_EXISTS:   'SHOOTER_SEEMS_NOT_EXISTS',
    START_SIGNALING:            'START_SIGNALING'
};

export {utils, messageTypes};