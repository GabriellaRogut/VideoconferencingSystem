// -----------------------------
// Elements
// -----------------------------
const localVideo = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");
const localVideoCard = document.getElementById("localVideoCard");

// -----------------------------
// Variables
// -----------------------------
let localStream;
let pc;
const ws = new WebSocket("ws://localhost:3000");

const config = {
  iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
};

// -----------------------------
// Initialize Media & PeerConnection
// -----------------------------
async function initMediaAndStart() {
  try {
    // Get user media
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    localVideo.srcObject = localStream;

    // Create PeerConnection
    pc = new RTCPeerConnection(config);

    pc.onicecandidate = e => {
      if (e.candidate) ws.send(JSON.stringify({ type: "ice", candidate: e.candidate }));
    };

    pc.ontrack = e => {
      remoteVideo.srcObject = e.streams[0];
    };

    // Add local tracks
    localStream.getTracks().forEach(track => pc.addTrack(track, localStream));

    // Start speaking detection
    detectSpeaking(localStream, localVideoCard);

    // Start call when WebSocket is open
    if (ws.readyState === WebSocket.OPEN) {
      await startCall();
    } else {
      ws.onopen = async () => {
        await startCall();
      };
    }

  } catch (err) {
    console.error("Error accessing media devices:", err);
  }
}

// -----------------------------
// Start WebRTC call
// -----------------------------
async function startCall() {
  if (!pc) {
    console.error("PeerConnection not initialized yet!");
    return;
  }

  const offer = await pc.createOffer();
  await pc.setLocalDescription(offer);
  ws.send(JSON.stringify({ type: "offer", offer }));
}

// -----------------------------
// Handle incoming WebSocket messages
// -----------------------------
ws.onmessage = async (msg) => {
  const data = JSON.parse(msg.data);

  if (!pc) return;

  if (data.type === "offer") {
    await pc.setRemoteDescription(data.offer);
    const answer = await pc.createAnswer();
    await pc.setLocalDescription(answer);
    ws.send(JSON.stringify({ type: "answer", answer }));
  }

  if (data.type === "answer") {
    await pc.setRemoteDescription(data.answer);
  }

  if (data.type === "ice") {
    try {
      await pc.addIceCandidate(data.candidate);
    } catch (err) {
      console.error("Error adding ICE candidate:", err);
    }
  }
};

// -----------------------------
// Speaking detection
// -----------------------------
function detectSpeaking(stream, videoCardEl) {
  if (!videoCardEl || !stream) return;

  const AudioContext = window.AudioContext || window.webkitAudioContext;
  const audioContext = new AudioContext();

  const analyser = audioContext.createAnalyser();
  analyser.fftSize = 1024;
  const microphone = audioContext.createMediaStreamSource(stream);
  microphone.connect(analyser);

  const dataArray = new Uint8Array(analyser.frequencyBinCount);
  let speaking = false;
  const THRESHOLD = 20;

  // Unlock AudioContext
  const resumeAudio = () => {
    if (audioContext.state === "suspended") audioContext.resume();
    document.removeEventListener("click", resumeAudio);
  };
  document.addEventListener("click", resumeAudio);

  function checkVolume() {
    analyser.getByteFrequencyData(dataArray);
    const volume = dataArray.reduce((a,b) => a+b,0) / dataArray.length;

    if (volume > THRESHOLD && !speaking) {
      speaking = true;
      videoCardEl.classList.add("video-card-speaking");
    }

    if (volume <= THRESHOLD && speaking) {
      speaking = false;
      videoCardEl.classList.remove("video-card-speaking");
    }

    requestAnimationFrame(checkVolume);
  }

  checkVolume();
}

// -----------------------------
// Initialize
// -----------------------------
initMediaAndStart();
