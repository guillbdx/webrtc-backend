import {utils, messageTypes} from './utils.js';

//--------------------------------------------------------
// VARIABLES SETTING
//---------------------------------------------------------

let localStream;
let peerConnection = null;
let socket;
let pingpong;
const roomId = document.getElementById('roomId').value;
const cameraRefusedPath = document.getElementById('cameraRefusedPath').value;
const allowedIceType = document.getElementById('allowedIceType').value;

const localVideo = document.getElementById('localVideo');
let iceServers = [];

//----------------------------------------------------------------
// SIGNALING
//----------------------------------------------------------------

function startShooter() {

    if (peerConnection instanceof RTCPeerConnection) {
        peerConnection.close();
    }

    peerConnection = new RTCPeerConnection({'iceServers': iceServers});
    peerConnection.onicecandidate = (event) => utils.sendIceCandidate(event.candidate, socket, roomId, allowedIceType);
    peerConnection.oniceconnectionstatechange = () => utils.closePeerIfDisconnected(peerConnection);

    if (typeof localStream.getTracks !== "undefined" && typeof peerConnection.addTrack !== "undefined") {
        localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
    } else {
        peerConnection.addStream(localStream);
    }

    peerConnection.createOffer().then((description) => {
            utils.sendOffer(peerConnection, description, iceServers, socket, roomId);
    }).catch(utils.errorHandler);
}

function gotMessageFromServer(message) {

    message = JSON.parse(message.data);

    if (messageTypes.START_SIGNALING === message.type) {
        iceServers = message.body;
        startShooter();
        return;
    }

    if (messageTypes.PONG === message.type) {
        pingpong = true;
    }

    if (messageTypes.ANSWER === message.type) {
        peerConnection.setRemoteDescription(new RTCSessionDescription(message.body)).catch(utils.errorHandler);
    }

    if (messageTypes.ICE === message.type) {
        let signal = message.body;
        peerConnection.addIceCandidate(new RTCIceCandidate(signal.ice)).catch(utils.errorHandler);
    }

}

//----------------------------------------------------------------
// SOCKET INITIALIZATION
//----------------------------------------------------------------

function createSocket() {
    socket = new WebSocket('wss://' + window.location.hostname + '/the_socket');
    socket.onmessage = gotMessageFromServer;
    socket.onopen = function() {
        utils.send(socket, messageTypes.SHOOTER_INTRODUCTION, roomId, '');
        pingpong = true;
    };
}

setInterval(function() {
    if (pingpong === false) {
        createSocket();
    } else {
        pingpong = false;
        utils.send(socket, messageTypes.PING, roomId, '');
    }

}, 10000);

//----------------------------------------------------------------
// SNAP PHOTO
//----------------------------------------------------------------

const snapButton = document.getElementById('snapButton');
const canvas = document.getElementById('canvas');
const snapPath = document.getElementById('snapPath').value;

if (snapButton != null) {
    snapButton.addEventListener('click', (event) => {
        event.preventDefault();
        utils.takePhoto(canvas, localVideo, snapPath, roomId);
    });
}

//----------------------------------------------------------------
// MISMATCH
//----------------------------------------------------------------

let beforeBase64 = null;
let countdownMismatch = 0;

function loopMismatch() {

    countdownMismatch--;

    let currentBase64 = utils.extractBase64FromVideo(canvas, localVideo);
    if (beforeBase64 == null) {
        beforeBase64 = currentBase64;
    }

    if (countdownMismatch > 0) {
        continueLoopMismatch(currentBase64);
        return;
    }

    resemble(beforeBase64)
        .compareTo(currentBase64)
        .ignoreColors()
        .onComplete(function(data){
            let mismatch = parseInt(100 * data.misMatchPercentage);
            if (mismatch >= 300) {
                utils.postMismatch(roomId, beforeBase64, currentBase64, mismatch).always(function() {
                    countdownMismatch = 5;
                    continueLoopMismatch(currentBase64);
                });
            } else {
                continueLoopMismatch(currentBase64);
            }
        });
}

function continueLoopMismatch(currentBase64) {
    beforeBase64 = currentBase64;
    setTimeout(function() {
        loopMismatch();
    }, 2000);
}

//----------------------------------------------------------------
// ACCESS MEDIA AND LAUNCHING
//----------------------------------------------------------------

function waitAndLaunch() {
    setTimeout(() => {
        utils.resizeCanvas(canvas, localVideo, 480);
        utils.loopTakePhoto(canvas, localVideo, snapPath, roomId);
        loopMismatch();
    }, 5000);
}

navigator.mediaDevices.getUserMedia({
    video: true,
    audio: false,
}).then((stream) => {
    localStream = stream;
    localVideo.srcObject = stream;
    createSocket();
    waitAndLaunch();
}).catch((error) => {
    window.location.replace(cameraRefusedPath);
});