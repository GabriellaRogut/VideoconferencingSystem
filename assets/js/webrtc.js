const localVideo = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");
const localVideoCard = document.getElementById("localVideoCard");

let localStream;
let pc;

const meetingCode = new URLSearchParams(window.location.search).get("code");
const ws = new WebSocket("wss://issac-unrescued-langston.ngrok-free.dev");


// const config = { iceServers: [{ urls: "stun:stun.l.google.com:19302" }] };

// -----------------------------
// Initialize media
// -----------------------------
async function initMedia() {
  try {
    // IMPORTANT: предотвратява двойно взимане на камера/микрофон
    if (localStream) return localStream;

    localStream = await navigator.mediaDevices.getUserMedia({
      video: true,
      audio: true,
    });

    localVideo.srcObject = localStream;

    // Start speaking detection
    detectSpeaking(localStream, localVideoCard);

    return localStream;
  } catch (err) {
    console.error("Error accessing media devices:", err);
    alert("Не може да се достъпи камерата или микрофона");
    throw err;
  }
}

// -----------------------------
// ICE buffering helpers (FIX)
// -----------------------------
let pendingCandidates = [];

async function flushCandidates() {
  if (!pc) return;
  if (!pc.remoteDescription || !pc.remoteDescription.type) return;

  const toAdd = pendingCandidates;
  pendingCandidates = [];

  for (const c of toAdd) {
    await pc.addIceCandidate(c).catch(console.error);
  }
}

// -----------------------------
// Create PeerConnection
// -----------------------------
function createPeerConnection() {
  // Metered
  pc = new RTCPeerConnection({
  iceServers: [
      {
        urls: "stun:stun.l.google.com:19302"
      },
      {
        urls: "turn:global.relay.metered.ca:80",
        username: "6fc33b1992777f2fae3ed0dc",
        credential: "yI+XPW6hE+yHfX0s",
      },
      {
        urls: "turn:global.relay.metered.ca:80?transport=tcp",
        username: "6fc33b1992777f2fae3ed0dc",
        credential: "yI+XPW6hE+yHfX0s",
      },
      {
        urls: "turn:global.relay.metered.ca:443",
        username: "6fc33b1992777f2fae3ed0dc",
        credential: "yI+XPW6hE+yHfX0s",
      },
      {
        urls: "turns:global.relay.metered.ca:443?transport=tcp",
        username: "6fc33b1992777f2fae3ed0dc",
        credential: "yI+XPW6hE+yHfX0s",
      },
  ],
});

  // Debug
  pc.onsignalingstatechange = () => console.log("sig:", pc.signalingState);
  pc.oniceconnectionstatechange = () => console.log("ice:", pc.iceConnectionState);
  pc.onconnectionstatechange = () => console.log("conn:", pc.connectionState);

pc.onicecandidate = (e) => {
  if (e.candidate) {
    console.log("SEND ICE:", e.candidate.candidate);
    ws.send(JSON.stringify({ type: "ice", candidate: e.candidate }));
  } else {
    console.log("ICE gathering complete");
  }
};

  pc.ontrack = (e) => {
    console.log("ONTRACK kind:", e.track.kind, "muted:", e.track.muted, "state:", e.track.readyState);
    console.log(
      "Stream tracks:",
      e.streams[0].getTracks().map((t) => ({
        kind: t.kind,
        enabled: t.enabled,
        muted: t.muted,
        state: t.readyState,
      }))
    );

    remoteVideo.srcObject = e.streams[0];

    remoteVideo.muted = true;
    remoteVideo.playsInline = true;
    remoteVideo.autoplay = true;

    remoteVideo.onloadedmetadata = () => {
      console.log("remote size:", remoteVideo.videoWidth, remoteVideo.videoHeight);
      remoteVideo.play().catch((err) => console.warn("remote play() blocked:", err));
    };
  };

  // Add local tracks
  localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));
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
ws.onopen = async () => {
  await initMedia();
  ws.send(JSON.stringify({ type: "join", code: meetingCode }));
};

ws.onmessage = async (msg) => {
  const data = JSON.parse(msg.data);

  if (data.type === "start-call") {
    if (!localStream) await initMedia();
    if (!pc) await startCall();
  }

  if (data.type === "offer") {
    if (!localStream) await initMedia();
    if (!pc) createPeerConnection();

    await pc.setRemoteDescription(data.offer);
    await flushCandidates();

    const answer = await pc.createAnswer();
    await pc.setLocalDescription(answer);

    ws.send(JSON.stringify({ type: "answer", answer }));
  }

  if (data.type === "answer") {
    if (!pc) return;

    await pc.setRemoteDescription(data.answer);
    await flushCandidates();
  }


  if (data.type === "ice") {
    const candidate = new RTCIceCandidate(data.candidate);
    pendingCandidates.push(candidate);
	
	console.log("RECV ICE:", data.candidate?.candidate);
    await flushCandidates();
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
// Initialize (optional local preview)
// -----------------------------
initMedia().catch(() => {});
