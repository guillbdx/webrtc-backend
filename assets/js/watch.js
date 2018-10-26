import {utils, messageTypes} from './utils.js';

//----------------------------------------------------------------
// VARIABLES SETTING
//----------------------------------------------------------------

let peerConnection;
let socket;
const roomId = document.getElementById('roomId').value;
const pathWatchTimeout = document.getElementById('pathWatchTimeout').value;

const remoteVideo = document.getElementById('remoteVideo');
const allowedIceType = document.getElementById('allowedIceType').value;

const alertShooterNotExists = document.getElementById('alertShooterNotExists');
const alertPendingConnection = document.getElementById('alertPendingConnection');
const alertShooterSeemsNotExists = document.getElementById('alertShooterSeemsNotExists');
const alertIssue = document.getElementById('alertIssue');
const alertNoRTC = document.getElementById('alertNoRTC');

let somethingHappened = false;

let iceServers = [];

//----------------------------------------------------------------
// CHECK SUPPORT
//----------------------------------------------------------------

if (typeof RTCPeerConnection !== 'function') {
    showNoRTC();
}

//----------------------------------------------------------------
// SIGNALING
//----------------------------------------------------------------

function startWatcher() {
    peerConnection = new RTCPeerConnection({'iceServers': iceServers});
    peerConnection.onicecandidate = (event) => utils.sendIceCandidate(event.candidate, socket, roomId, allowedIceType);

    if (typeof peerConnection.ontrack !== "undefined") {
        peerConnection.ontrack = function(event) {
            remoteVideo.srcObject = event.streams[0];
        };
    } else {
        peerConnection.onaddstream = function(event) {
            remoteVideo.srcObject = event.stream;
        };
    }

    peerConnection.oniceconnectionstatechange = iceConnectionChanged;
}

function gotMessageFromServer(message) {

    message = JSON.parse(message.data);

    if (messageTypes.SHOOTER_NOT_EXISTS === message.type) {
        showShooterNotExistsAlert();
        somethingHappened = true;
    }

    if (messageTypes.SHOOTER_SEEMS_NOT_EXISTS === message.type) {
        showShooterSeemsNotExistsAlert();
        somethingHappened = true;
    }

    if (messageTypes.OFFER === message.type) {

        iceServers = utils.filterServersIfEdge(message.body.iceServers);
        startWatcher();

        peerConnection.setRemoteDescription(new RTCSessionDescription(message.body.sdp)).then(function() {
            peerConnection.createAnswer().then((description) => {
                utils.sendAnswer(peerConnection, description, socket, roomId);
            }).catch(utils.errorHandler);
        }).catch(utils.errorHandler);
    }

    if (messageTypes.ICE === message.type) {
        let signal = message.body;
        peerConnection.addIceCandidate(new RTCIceCandidate(signal.ice)).catch(utils.errorHandler);
    }
}

function iceConnectionChanged() {
    if ('connected' === peerConnection.iceConnectionState) {
        showVideo();
        somethingHappened = true;
    }
}

//----------------------------------------------------------------
// SHOW / HIDE
//----------------------------------------------------------------

function showShooterNotExistsAlert() {
    alertShooterNotExists.style.display = 'block';
    alertPendingConnection.style.display = 'none';
}

function showShooterSeemsNotExistsAlert() {
    alertShooterSeemsNotExists.style.display = 'block';
    alertPendingConnection.style.display = 'none';
}

function showIssueAlert() {
    alertIssue.style.display = 'block';
    alertPendingConnection.style.display = 'none';
}

function showVideo() {
    alertShooterNotExists.style.display = 'none';
    alertShooterSeemsNotExists.style.display = 'none';
    alertPendingConnection.style.display = 'none';
    alertIssue.style.display = 'none';
}

function showNoRTC() {
    alertNoRTC.style.display = 'block';
    alertPendingConnection.style.display = 'none';
}

//----------------------------------------------------------------
// SOCKET INITIALIZATION
//----------------------------------------------------------------

socket = new WebSocket('wss://' + window.location.hostname + '/the_socket');
socket.onmessage = gotMessageFromServer;
socket.onopen = () => utils.send(socket, messageTypes.WATCHER_INTRODUCTION, roomId, '');

//----------------------------------------------------------------
// INACTIVITY REDIRECTION
//----------------------------------------------------------------

setTimeout(() => {
    window.location.replace(pathWatchTimeout);
}, 600000);

//----------------------------------------------------------------
// STOPWATCH BEFORE ISSUE
//----------------------------------------------------------------

setTimeout(function() {
    if (false === somethingHappened) {
        showIssueAlert();
    }
}, 25000);

