const localVideo = document.getElementById("localVideo");
const remoteVideo = document.querySelector(".video-card video:not(#localVideo)");
const localVideoCard = document.getElementById("localVideoCard");

let localStream;
let pc;

const meetingCode = new URLSearchParams(window.location.search).get("code");
const ws = new WebSocket("ws://localhost:3000");

const config = { iceServers: [{ urls: "stun:stun.l.google.com:19302" }] };

// -----------------------------
// Initialize media
// -----------------------------
async function initMedia() {
  try {
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    localVideo.srcObject = localStream;

    // Start speaking detection
    detectSpeaking(localStream, localVideoCard);

  } catch (err) {
    console.error("Error accessing media devices:", err);
    alert("Не може да се достъпи камерата или микрофона");
  }
}

// -----------------------------
// Create PeerConnection
// -----------------------------
function createPeerConnection() {
  pc = new RTCPeerConnection(config);

  pc.onicecandidate = (e) => {
    if (e.candidate) {
      ws.send(JSON.stringify({ type: "ice", candidate: e.candidate }));
    }
  };

  pc.ontrack = (e) => {
    remoteVideo.srcObject = e.streams[0];
  };

  localStream.getTracks().forEach(track => pc.addTrack(track, localStream));
}

// -----------------------------
// Start call (offer)
// -----------------------------
async function startCall() {
  createPeerConnection();
  const offer = await pc.createOffer();
  await pc.setLocalDescription(offer);
  ws.send(JSON.stringify({ type: "offer", offer }));
}

// -----------------------------
// Handle WebSocket messages
// -----------------------------
ws.onopen = () => {
  ws.send(JSON.stringify({ type: "join", code: meetingCode }));
};

ws.onmessage = async (msg) => {
  const data = JSON.parse(msg.data);

  if (!pc && data.type === "offer") {
    createPeerConnection();
    await pc.setRemoteDescription(data.offer);
    const answer = await pc.createAnswer();
    await pc.setLocalDescription(answer);
    ws.send(JSON.stringify({ type: "answer", answer }));
  }

  if (data.type === "answer" && pc) {
    await pc.setRemoteDescription(data.answer);
  }

  if (data.type === "ice" && pc) {
    try {
      await pc.addIceCandidate(data.candidate);
    } catch (err) {
      console.error("Error adding ICE candidate:", err);
    }
  }

  // If second user joined first, we start the call
  if (data.type === "start-call") {
    if (!pc) startCall();
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
    const volume = dataArray.reduce((a, b) => a + b, 0) / dataArray.length;

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
initMedia();
